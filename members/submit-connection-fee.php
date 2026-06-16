<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdraw.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Validate Session Data
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['withdraw_data'])) {
    die("Withdrawal session expired. Please start again.");
}

$data = $_SESSION['withdraw_data'];

/*
|--------------------------------------------------------------------------
| Validate Upload
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
    die("Please upload a valid payment receipt.");
}

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $_FILES['receipt']['tmp_name']);
finfo_close($finfo);

if (!isset($allowed[$mime])) {
    die("Only JPG, PNG and WEBP files are allowed.");
}

$extension = $allowed[$mime];

$uploadDir = "../uploads/connection-fees/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fileName = 'receipt_' . time() . '_' . $user_id . '.' . $extension;
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath)) {
    die("Failed to upload receipt.");
}

/*
|--------------------------------------------------------------------------
| Save Withdrawal Request
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO withdrawal_requests (
        user_id,
        amount,
        country,
        payment_type,
        withdraw_method,
        withdraw_method_name,
        withdraw_method_id,
        account_identifier,
        connection_fee_receipt,
        status,
        created_at
    )
    VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW()
    )
");

$stmt->bind_param(
    "idsssssss",
    $user_id,
    $data['amount'],
    $data['country'],
    $data['selected_type'],
    $data['withdraw_method'],
    $data['withdraw_method_name'],
    $data['withdraw_method_id'],
    $data['account'],
    $fileName
);

if (!$stmt->execute()) {
    die("Database error: " . $stmt->error);
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

unset($_SESSION['withdraw_data']);
unset($_SESSION['withdraw_amount']);

/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

$_SESSION['success_message'] =
    "Your payment proof has been submitted successfully. Your withdrawal request is now awaiting review.";

header("Location: withdrawal-success.php");
exit();
