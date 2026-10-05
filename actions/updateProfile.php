<?php

session_start();

require_once("../includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit;
}

if (!isset($_SESSION["id"])) {
    echo "not_logged";
    exit;
}

$id = $_SESSION["id"];

$name = preg_replace('/\s+/', ' ', trim($_POST["name"]));
$email = trim($_POST["email"]);

/*==============================
VALIDACIONES
==============================*/

if (empty($name) || empty($email)) {
    echo "empty_fields";
    exit;
}

if (strlen($name) < 3) {
    echo "invalid_name";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "invalid_email";
    exit;
}

/*==============================
OBTENER USUARIO ACTUAL
==============================*/

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$user) {
    echo "user_not_found";
    exit;
}

/*==============================
VERIFICAR EMAIL DUPLICADO
==============================*/

$stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND id<>?");
$stmt->bind_param("si", $email, $id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    echo "email_exists";
    exit;
}

$stmt->close();

/*==============================
PROCESAR FOTO
==============================*/

$photo = $user["photo"];

if (
    isset($_FILES["photo"]) &&
    $_FILES["photo"]["error"] == 0
) {

    $allowedExtensions = [
        "jpg",
        "jpeg",
        "png",
        "webp"
    ];

    $allowedMime = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    $extension = strtolower(
        pathinfo(
            $_FILES["photo"]["name"],
            PATHINFO_EXTENSION
        )
    );

    $mime = mime_content_type(
        $_FILES["photo"]["tmp_name"]
    );

    if (
        !in_array($extension, $allowedExtensions) ||
        !in_array($mime, $allowedMime)
    ) {

        echo "invalid_image";
        exit;
    }

    if ($_FILES["photo"]["size"] > 5 * 1024 * 1024) {

        echo "large_image";
        exit;
    }

    $newPhoto = uniqid("user_") . "." . $extension;

    $uploadDir = dirname(__DIR__) . "/uploads/profiles/";

    if (!is_dir($uploadDir)) {

        mkdir($uploadDir, 0777, true);
    }

    if (!move_uploaded_file(
        $_FILES["photo"]["tmp_name"],
        $uploadDir . $newPhoto
    )) {

        echo "upload_error";
        exit;
    }

    // eliminar foto anterior

    if (
        $photo != "default.png" &&
        file_exists($uploadDir . $photo)
    ) {

        unlink($uploadDir . $photo);
    }

    $photo = $newPhoto;
}


/*==============================
VALIDAR SI HUBO CAMBIOS
==============================*/

$photoChanged = (
    isset($_FILES["photo"]) &&
    $_FILES["photo"]["error"] == 0
);

if (
    $name === $user["name"] &&
    $email === $user["email"] &&
    !$photoChanged
) {

    echo "no_changes";
    exit;
}

/*==============================
ACTUALIZAR
==============================*/

$stmt = $conn->prepare("
UPDATE users
SET
name=?,
email=?,
photo=?
WHERE id=?
");

$stmt->bind_param(
    "sssi",
    $name,
    $email,
    $photo,
    $id
);

if (!$stmt->execute()) {

    echo "server_error";
    exit;
}

/*==============================
ACTUALIZAR SESSION
==============================*/

$_SESSION["name"] = $name;
$_SESSION["email"] = $email;
$_SESSION["photo"] = $photo;



echo "success";

$stmt->close();
$conn->close();

exit;
