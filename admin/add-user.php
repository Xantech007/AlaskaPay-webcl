<?php
session_start();
require '../config/db.php';

function generateUsername($email) {
    $base = explode('@', $email)[0];
    return $base . rand(1000, 9999);
}

try {

    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    // basic validation
    if (!$email || !$passwordRaw || !$full_name) {
        throw new Exception("Email, password and full name are required");
    }

    $password = password_hash($passwordRaw, PASSWORD_BCRYPT);
    $username = generateUsername($email);

    // check duplicate email first (clean error)
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        throw new Exception("Email already exists");
    }

    // insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, full_name, phone, is_verified, status)
        VALUES (?, ?, ?, ?, ?, 0, 'active')
    ");

    $stmt->execute([
        $username,
        $email,
        $password,
        $full_name,
        $phone
    ]);

    $_SESSION['success'] = "User created successfully";

} catch (PDOException $e) {

    // MySQL-specific error handling
    if ($e->getCode() == 23000) {
        $_SESSION['error'] = "Duplicate entry detected (email or username already exists)";
    } else {
        $_SESSION['error'] = "Database error occurred. Please try again.";
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: users");
exit();
