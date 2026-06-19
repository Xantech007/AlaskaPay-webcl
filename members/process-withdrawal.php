<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$amount = (float)($_POST['amount'] ?? 0);

if ($amount <= 0) {
    $_SESSION['error'] = "Invalid withdrawal amount.";
    header("Location: withdraw.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT
        is_verified,
        verified_method,
        verified_account_name,
        verified_account_id
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $user['is_verified'] != 2) {
    $_SESSION['error'] = "Your payment method is not verified.";
    header("Location: withdraw.php");
    exit();
}

$stmt = $conn->prepare("
    INSERT INTO withdrawals (
        user_id,
        amount,
        payment_method,
        account_name,
        account_id,
        status
    )
    VALUES (?, ?, ?, ?, ?, 'pending')
");

$stmt->bind_param(
    "idsss",
    $user_id,
    $amount,
    $user['verified_method'],
    $user['verified_account_name'],
    $user['verified_account_id']
);

$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Withdrawal request submitted successfully and is awaiting approval.";

header("Location: withdraw.php");
exit();
