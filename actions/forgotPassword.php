<?php

require_once "../includes/config.php";
require_once "../includes/conexion.php";
require_once "../includes/mail.php";

header("Content-Type: text/plain");

/*==================================================
    VALIDAR REQUEST
==================================================*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo "invalid_request";
    exit;
}

/*==================================================
    OBTENER EMAIL
==================================================*/

$email = trim($_POST["email"] ?? "");

/*
    Por seguridad no revelamos si el correo existe
    o no existe en nuestra base de datos.
*/

if (
    empty($email) ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    echo "success";
    exit;
}

/*==================================================
    BUSCAR USUARIO
==================================================*/

$stmt = $conn->prepare("
    SELECT id, name, email
    FROM users
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

/*
    Si el usuario no existe, devolvemos success
    igualmente para no revelar información.
*/

if (!$user) {

    echo "success";
    exit;
}

/*==================================================
    ELIMINAR TOKENS ANTERIORES
==================================================*/

$stmt = $conn->prepare("
    DELETE FROM password_resets
    WHERE user_id = ?
");

$stmt->bind_param("i", $user["id"]);

$stmt->execute();

$stmt->close();

/*==================================================
    GENERAR TOKEN
==================================================*/

$token = bin2hex(random_bytes(32));

$tokenHash = hash(
    "sha256",
    $token
);

/*==================================================
    EXPIRACIÓN
==================================================*/

$expires = date(
    "Y-m-d H:i:s",
    time() + (TOKEN_EXPIRATION_MINUTES * 60)
);

/*==================================================
    GUARDAR TOKEN
==================================================*/

$stmt = $conn->prepare("
    INSERT INTO password_resets
    (
        user_id,
        token,
        expires_at
    )
    VALUES
    (
        ?,
        ?,
        ?
    )
");

$stmt->bind_param(
    "iss",
    $user["id"],
    $tokenHash,
    $expires
);

if (!$stmt->execute()) {

    $stmt->close();

    echo "server_error";
    exit;
}

$stmt->close();

/*==================================================
    CREAR LINK DE RECUPERACIÓN
==================================================*/

$resetLink =
    APP_URL .
    "/resetPassword.php?token=" .
    urlencode($token);

/*==================================================
    CREAR CORREO
==================================================*/

ob_start();

require "../includes/templates/reset-password.php";

$body = ob_get_clean();

/*==================================================
    ENVIAR CORREO
==================================================*/

$sent = sendMail(
    $user["email"],
    "Restablecer contraseña - " . APP_NAME,
    $body
);

if (!$sent) {

    /*
        Si el correo no pudo enviarse,
        eliminamos el token generado.
    */

    $stmt = $conn->prepare("
        DELETE FROM password_resets
        WHERE user_id = ?
    ");

    $stmt->bind_param(
        "i",
        $user["id"]
    );

    $stmt->execute();

    $stmt->close();

    echo "mail_error";
    exit;
}

/*==================================================
    FINALIZAR
==================================================*/

echo "success";

$conn->close();

exit;
