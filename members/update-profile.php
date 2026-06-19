<?php
session_start();

require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$email = trim($_POST['email'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

if (empty($email)) {
    $_SESSION['error_message'] = "Email address is required.";
    header("Location: profile.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Invalid email address.";
    header("Location: profile.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Check Email Uniqueness
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT id
    FROM users
    WHERE email = ?
    AND id != ?
    LIMIT 1
");
$stmt->execute([$email, $user_id]);

if ($stmt->fetch()) {
    $_SESSION['error_message'] = "Email address already exists.";
    header("Location: profile.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Password Validation
|--------------------------------------------------------------------------
*/
if (!empty($password)) {

    if (strlen($password) < 6) {
        $_SESSION['error_message'] = "Password must be at least 6 characters.";
        header("Location: profile.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: profile.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            email = ?,
            full_name = ?,
            phone = ?,
            password = ?
        WHERE id = ?
    ");

    $success = $stmt->execute([
        $email,
        $full_name,
        $phone,
        $hashedPassword,
        $user_id
    ]);

} else {

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            email = ?,
            full_name = ?,
            phone = ?
        WHERE id = ?
    ");

    $success = $stmt->execute([
        $email,
        $full_name,
        $phone,
        $user_id
    ]);
}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/
if ($success) {

    $_SESSION['success_message'] =
        "Profile updated successfully.";

} else {

    $_SESSION['error_message'] =
        "Unable to update profile.";
}

header("Location: profile.php");
exit();
