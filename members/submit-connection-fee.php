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
| GET USER EMAIL
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$email = $user['email'] ?? '';

/*
|--------------------------------------------------------------------------
| GET CONNECTION FEE
|--------------------------------------------------------------------------
*/
$country = $_SESSION['conn_fee']['country'] ?? '';

$stmt = $conn->prepare("
    SELECT fee
    FROM region_settings
    WHERE country = ?
    LIMIT 1
");
$stmt->bind_param("s", $country);
$stmt->execute();
$region = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$region) {
    $_SESSION['error'] = "Region settings not found.";
    header("Location: withdraw.php");
    exit();
}

$amount = (float)$region['fee'];

/*
|--------------------------------------------------------------------------
| HANDLE FILE UPLOAD
|--------------------------------------------------------------------------
*/
if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== 0) {
    $_SESSION['error'] = "Please upload a payment receipt.";
    header("Location: connection-fee.php");
    exit();
}

$uploadDir = "uploads/deposits/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    $_SESSION['error'] = "Invalid file type.";
    header("Location: connection-fee.php");
    exit();
}

$filename = time() . "_" . uniqid() . "." . $ext;
$filePath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath)) {
    $_SESSION['error'] = "Failed to upload receipt.";
    header("Location: connection-fee.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| SAVE TO DEPOSITS TABLE
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO deposits
    (
        user_id,
        email,
        amount,
        proof_file,
        status,
        created_at
    )
    VALUES
    (
        ?, ?, ?, ?, 'pending', NOW()
    )
");

$stmt->bind_param(
    "isds",
    $user_id,
    $email,
    $amount,
    $filePath
);

if ($stmt->execute()) {

    $_SESSION['success_message'] =
        "Payment proof submitted successfully. Your payment is awaiting verification.";

    header("Location: dashboard.php");
    exit();

} else {

    $_SESSION['error'] = "Unable to save payment proof.";

    header("Location: connection-fee.php");
    exit();
}
