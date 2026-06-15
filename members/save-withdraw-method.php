<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$method = $_POST['method'] ?? '';
$account = $_POST['account'] ?? '';

if (!$method || !$account) {
    die("Invalid request");
}

$stmt = $pdo->prepare("
    UPDATE users
    SET withdraw_method = ?, withdraw_account = ?
    WHERE id = ?
");

$stmt->execute([$method, $account, $user_id]);

$_SESSION['success_message'] = "Withdrawal method saved successfully.";

header("Location: dashboard.php");
exit();
