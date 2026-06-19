<?php

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
    $_SESSION['error'] = "Withdrawal session expired. Please start again.";
    header("Location: withdraw.php");
    exit();
}

$data = $_SESSION['withdraw_data'];

/*
|--------------------------------------------------------------------------
| FIX: SAFE AMOUNT HANDLING (NO STRICT DEPENDENCY)
|--------------------------------------------------------------------------
*/

$amount = $data['amount'] ?? null;

/* Optional fallback (if you ever store it separately) */
if (!$amount && isset($_SESSION['withdraw_amount'])) {
    $amount = $_SESSION['withdraw_amount'];
}

/* Final validation */
if (!$amount || $amount <= 0) {
    $_SESSION['error'] = "Withdrawal amount missing. Please restart the withdrawal process.";
    header("Location: withdraw.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validate Upload
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Please upload a valid payment receipt.";
    header("Location: withdraw.php");
    exit();
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
    $_SESSION['error'] = "Only JPG, PNG and WEBP files are allowed.";
    header("Location: withdraw.php");
    exit();
}

$extension = $allowed[$mime];

$uploadDir = "../uploads/connection-fees/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fileName = 'receipt_' . time() . '_' . $user_id . '.' . $extension;
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath)) {
    $_SESSION['error'] = "Failed to upload receipt.";
    header("Location: withdraw.php");
    exit();
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
    $amount,
    $data['country'],
    $data['selected_type'],
    $data['withdraw_method'],
    $data['withdraw_method_name'],
    $data['withdraw_method_id'],
    $data['account'],
    $fileName
);

if (!$stmt->execute()) {
    $_SESSION['error'] = "Database error: " . $stmt->error;
    header("Location: withdraw.php");
    exit();
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Cleanup Session
|--------------------------------------------------------------------------
*/

unset($_SESSION['withdraw_data']);
unset($_SESSION['withdraw_amount']);

/*
|--------------------------------------------------------------------------
| Success Redirect
|--------------------------------------------------------------------------
*/

$_SESSION['success_message'] =
    "Your payment proof has been submitted successfully. Your withdrawal request is now under review.";

header("Location: dashboard.php");
exit();
