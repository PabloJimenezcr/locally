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

$currentPassword = trim($_POST["currentPassword"]);
$newPassword = trim($_POST["newPassword"]);
$confirmPassword = trim($_POST["confirmPassword"]);

if (
    empty($currentPassword) ||
    empty($newPassword) ||
    empty($confirmPassword)
) {
    echo "empty_fields";
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo "password_mismatch";
    exit;
}

if ($currentPassword === $newPassword) {
    echo "same_password";
    exit;
}

if (
    strlen($newPassword) < 8 ||
    !preg_match('/[A-Z]/', $newPassword) ||
    !preg_match('/[0-9]/', $newPassword)
) {

    echo "weak_password";
    exit;
}

$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (!$user) {

    echo "user_not_found";
    exit;
}

if (!password_verify($currentPassword, $user["password"])) {

    echo "wrong_password";
    exit;
}

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $newHash, $_SESSION["id"]);

if ($stmt->execute()) {

    echo "success";
} else {

    echo "server_error";
}

$stmt->close();
$conn->close();

exit;
