<?php

session_start();

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/conexion.php";
require_once __DIR__ . "/../includes/mail.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit;
}

$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$password = trim($_POST["password"]);

if (empty($name) || empty($email) || empty($password)) {
    echo "empty_fields";
    exit;
}

// Verificar si el correo ya existe
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "email_exists";
    exit;
}

$stmt->close();

// Foto por defecto
$photo = "default.png";

// Si el usuario subió una foto
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

    $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

    $allowed = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($extension, $allowed)) {
        echo "invalid_image";
        exit;
    }

    $newName = uniqid("user_") . "." . $extension;

    if (!move_uploaded_file(
        $_FILES["photo"]["tmp_name"],
        "../uploads/profiles/" . $newName
    )) {

        echo "upload_error";
        exit;
    }

    $photo = $newName;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$stmt = $conn->prepare("INSERT INTO users(name,email,password,photo) VALUES(?,?,?,?)");

$stmt->bind_param(
    "ssss",
    $name,
    $email,
    $passwordHash,
    $photo
);

if ($stmt->execute()) {

    $newUserId = $stmt->insert_id;

    // Datos del usuario recién creado
    $user = [
        "id" => $newUserId,
        "name" => $name,
        "email" => $email,
        "photo" => $photo
    ];

    // Cargar plantilla del correo
    ob_start();

    require __DIR__ . "/../includes/templates/welcome.php";

    $body = ob_get_clean();

    // Enviar correo de bienvenida
    sendMail(
        $user["email"],
        "¡Bienvenido a " . APP_NAME . "!",
        $body
    );

    echo "success";
} else {

    echo "database_error";
}

$stmt->close();
$conn->close();

exit;
