<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| SAVE PAYMENT DETAILS
|--------------------------------------------------------------------------
*/

$method                 = trim($_POST['method_label'] ?? '');
$method_name            = trim($_POST['method_name_label'] ?? '');
$method_id              = trim($_POST['method_id_label'] ?? '');

$verified_method        = trim($_POST['verified_method'] ?? '');
$verified_account_name  = trim($_POST['verified_account_name'] ?? '');
$verified_account_id    = trim($_POST['verified_account_id'] ?? '');

/* For USA form, use fixed labels */
if (($_POST['type'] ?? '') === 'usa') {
    $method = 'Payment Method';
    $method_name = 'Account Name';
    $method_id = 'Account Identifier';
}

$stmt = $conn->prepare("
    UPDATE users
    SET
        method = ?,
        method_name = ?,
        method_id = ?,
        verified_method = ?,
        verified_account_name = ?,
        verified_account_id = ?,
        is_verified = 1,
        verified_at = NOW()
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $method,
    $method_name,
    $method_id,
    $verified_method,
    $verified_account_name,
    $verified_account_id,
    $user_id
);

$stmt->execute();
$stmt->close();

/*
|--------------------------------------------------------------------------
| REDIRECT AFTER SAVING
|--------------------------------------------------------------------------
*/
header("Location: connection-fee");
exit();
