<?php
session_start();
require '../config/db.php';

try {

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, full_name, phone, is_verified, status)
        VALUES (?, ?, ?, ?, 0, 'active')
    ");

    $stmt->execute([$email, $password, $full_name, $phone]);

    $_SESSION['success'] = "User created successfully";

} catch (Exception $e) {
    $_SESSION['error'] = "Error creating user: " . $e->getMessage();
}

header("Location: users.php");
exit();
