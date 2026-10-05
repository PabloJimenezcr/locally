<?php

session_start();

require_once("../includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit;
}

$email = trim($_POST["email"]);
$password = trim($_POST["password"]);

if (empty($email) || empty($password)) {
    echo "empty_fields";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "user_not_found";
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["password"])) {
    echo "wrong_password";
    exit;
}

$_SESSION["id"] = $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["email"] = $user["email"];
$_SESSION["photo"] = $user["photo"];
$_SESSION["role"] = $user["role"];

echo "success";

$stmt->close();
$conn->close();

exit;
