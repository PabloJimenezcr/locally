<?php

session_start();

require_once("../includes/conexion.php");

/*==============================
VALIDAR PETICIÓN
==============================*/

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit;
}

if (!isset($_SESSION["id"])) {
    echo "not_logged";
    exit;
}

/*==============================
OBTENER DATOS
==============================*/

$id = $_SESSION["id"];

$password = trim($_POST["password"] ?? "");

if (empty($password)) {

    echo "empty_password";
    exit;
}

/*==============================
BUSCAR USUARIO
==============================*/

$stmt = $conn->prepare("
SELECT password, photo
FROM users
WHERE id=?
LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$user) {

    echo "user_not_found";
    exit;
}


/*==============================
VALIDAR CONTRASEÑA
==============================*/

if (!password_verify($password, $user["password"])) {

    echo "wrong_password";
    exit;
}

/*==============================
ELIMINAR FOTO
==============================*/

if ($user["photo"] != "default.png") {

    $photoPath = dirname(__DIR__) .
        "/uploads/profiles/" .
        $user["photo"];

    if (file_exists($photoPath)) {

        unlink($photoPath);
    }
}

/*==============================
ELIMINAR USUARIO
==============================*/

$stmt = $conn->prepare("
DELETE FROM users
WHERE id=?
");

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {

    echo "server_error";
    exit;
}

$stmt->close();

/*==============================
DESTRUIR SESIÓN
==============================*/

$_SESSION = [];

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

echo "success";

$conn->close();

exit;
