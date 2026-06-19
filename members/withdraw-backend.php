<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* -----------------------------
   GET POST DATA
------------------------------*/
$type = $_POST['type'] ?? '';
$country = $_POST['country'] ?? '';

/* USER ENTERED DATA */
$user_method = trim($_POST['method'] ?? '');
$user_method_name = trim($_POST['method_name'] ?? '');
$user_method_id = trim($_POST['method_id'] ?? '');

/* DB LABEL DATA (HIDDEN FIELDS) */
$db_method = trim($_POST['method_label'] ?? '');
$db_method_name = trim($_POST['method_name_label'] ?? '');
$db_method_id = trim($_POST['method_id_label'] ?? '');

/* OPTIONAL VALIDATION */
if ($type === 'fallback') {

    if ($user_method === '' || $user_method_name === '' || $user_method_id === '') {
        $_SESSION['error'] = "All fields are required.";
        header("Location: withdrawal.php");
        exit();
    }

} elseif ($type === 'usa') {

    // USA form uses different field name
    $user_method = trim($_POST['method'] ?? '');
    $user_method_name = ''; // not provided
    $user_method_id = trim($_POST['account'] ?? '');

    $db_method = $_POST['method'] ?? '';
    $db_method_name = 'Account Name';
    $db_method_id = 'Account ID';

    if ($user_method === '' || $user_method_id === '') {
        $_SESSION['error'] = "All fields are required.";
        header("Location: withdrawal.php");
        exit();
    }
}

/* -----------------------------
   UPDATE USERS TABLE
------------------------------*/
$stmt = $conn->prepare("
    UPDATE users
    SET
        method = ?,
        method_name = ?,
        method_id = ?,

        verified_method = ?,
        verified_account_name = ?,
        verified_account_id = ?,

        verified_at = NOW(),
        is_verified = 1

    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $db_method,
    $db_method_name,
    $db_method_id,

    $user_method,
    $user_method_name,
    $user_method_id,

    $user_id
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Payment method submitted successfully and is now under review.";
} else {
    $_SESSION['error'] = "Failed to save payment method. Please try again.";
}

$stmt->close();

header("Location: withdrawal.php");
exit();
