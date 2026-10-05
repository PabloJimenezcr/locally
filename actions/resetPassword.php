<?php

session_start();

require_once "../includes/config.php";
require_once "../includes/conexion.php";

header("Content-Type: text/plain");

// =========================
// VALIDAR REQUEST
// =========================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "invalid_request";
    exit;
}

// =========================
// OBTENER DATOS
// =========================

$token = trim($_POST["token"] ?? "");
$newPassword = trim($_POST["password"] ?? "");
$confirmPassword = trim($_POST["confirmPassword"] ?? "");

if (
    empty($token) ||
    empty($newPassword) ||
    empty($confirmPassword)
) {
    echo "empty_fields";
    exit;
}

// =========================
// VALIDAR CONTRASEÑAS
// =========================

if ($newPassword !== $confirmPassword) {
    echo "password_mismatch";
    exit;
}

if (strlen($newPassword) < 8) {
    echo "weak_password";
    exit;
}

if (!preg_match('/[A-Z]/', $newPassword)) {
    echo "weak_password";
    exit;
}

if (!preg_match('/[0-9]/', $newPassword)) {
    echo "weak_password";
    exit;
}

// =========================
// HASH DEL TOKEN
// =========================

$tokenHash = hash("sha256", $token);

// =========================
// BUSCAR TOKEN
// =========================

$stmt = $conn->prepare("
    SELECT
        id,
        user_id,
        expires_at
    FROM password_resets
    WHERE token = ?
    LIMIT 1
");

$stmt->bind_param("s", $tokenHash);

$stmt->execute();

$result = $stmt->get_result();

$reset = $result->fetch_assoc();

$stmt->close();

if (!$reset) {
    echo "invalid_token";
    exit;
}

// =========================
// VALIDAR EXPIRACIÓN
// =========================

if (strtotime($reset["expires_at"]) < time()) {

    $stmt = $conn->prepare("
        DELETE FROM password_resets
        WHERE id = ?
    ");

    $stmt->bind_param("i", $reset["id"]);
    $stmt->execute();
    $stmt->close();

    echo "expired_token";
    exit;
}

// =========================
// CREAR NUEVO HASH
// =========================

$passwordHash = password_hash(
    $newPassword,
    PASSWORD_DEFAULT
);

// =========================
// ACTUALIZAR CONTRASEÑA
// =========================

$stmt = $conn->prepare("
    UPDATE users
    SET password = ?
    WHERE id = ?
");

$stmt->bind_param(
    "si",
    $passwordHash,
    $reset["user_id"]
);

if (!$stmt->execute()) {

    $stmt->close();

    echo "server_error";
    exit;
}

$stmt->close();

// =========================
// ELIMINAR TOKEN
// =========================

$stmt = $conn->prepare("
    DELETE FROM password_resets
    WHERE id = ?
");

$stmt->bind_param("i", $reset["id"]);

$stmt->execute();

$stmt->close();

$conn->close();

// =========================
// FINAL
// =========================

echo "success";

exit;
