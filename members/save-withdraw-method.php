<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type'] ?? '';

if ($type === 'usa') {

    $method = trim($_POST['method'] ?? '');
    $account = trim($_POST['account'] ?? '');

    if (empty($method) || empty($account)) {
        die('Invalid request');
    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET withdraw_method = ?,
            withdraw_account = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $method,
        $account,
        $user_id
    ]);

} elseif ($type === 'fallback') {

    $paymentType = trim($_POST['payment_type'] ?? '');
    $method = trim($_POST['method'] ?? '');
    $methodName = trim($_POST['method_name'] ?? '');
    $methodId = trim($_POST['method_id'] ?? '');

    if (
        empty($method) ||
        empty($methodName) ||
        empty($methodId)
    ) {
        die('Invalid request');
    }

    $account = json_encode([
        'payment_type' => $paymentType,
        'method'       => $method,
        'method_name'  => $methodName,
        'method_id'    => $methodId
    ]);

    $stmt = $pdo->prepare("
        UPDATE users
        SET withdraw_method = ?,
            withdraw_account = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $paymentType,
        $account,
        $user_id
    ]);

} else {

    die('Invalid request');
}

$_SESSION['success_message'] =
    'Withdrawal method saved successfully.';

header('Location: dashboard.php');
exit();
