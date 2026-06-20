<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* -----------------------------
   USER EMAIL
------------------------------*/
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$email = $user['email'] ?? '';

/* -----------------------------
   COUNTRY SAFETY CHECK
------------------------------*/
$country = $_SESSION['conn_fee']['country']
    ?? $_SESSION['country']
    ?? '';

if (!$country) {
    $_SESSION['error'] = "Country not found in session.";
    header("Location: withdraw.php");
    exit();
}

/* -----------------------------
   REGION SETTINGS
------------------------------*/
$stmt = $conn->prepare("
    SELECT fee, currency, use_external, external_name, external_link
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

$amount = (float) $region['fee'];
$currency = $region['currency'] ?? 'USD';
$is_external = $region['use_external'] ?? 'no';
$external_name = $region['external_name'] ?? null;
$external_link = $region['external_link'] ?? null;

/* -----------------------------
   HANDLE FILE UPLOAD (internal only)
------------------------------*/
$filePath = '';

if ($is_external === 'no') {

    if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== 0) {
        $_SESSION['error'] = "Please upload a payment receipt.";
        header("Location: connection-fee.php");
        exit();
    }

    $uploadDir = "../uploads/deposits/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];

    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = "Invalid file type.";
        header("Location: connection-fee.php");
        exit();
    }

    $filename = time() . "_" . uniqid() . "." . $ext;
    $filePath = $uploadDir . $filename;

    move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath);
}

/* -----------------------------
   INSERT INTO DEPOSITS (FULL FIXED)
------------------------------*/
$stmt = $conn->prepare("
    INSERT INTO deposits
    (
        user_id,
        email,
        amount,
        proof_file,
        status,
        currency,
        country,
        is_external,
        external_name,
        external_link,
        created_at
    )
    VALUES
    (?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "isdssssss",
    $user_id,
    $email,
    $amount,
    $filePath,
    $currency,
    $country,
    $is_external,
    $external_name,
    $external_link
);

$stmt->execute();
$stmt->close();

/* -----------------------------
   SUCCESS FLOW
------------------------------*/
$_SESSION['success'] = "Connection fee submitted successfully.";

if ($is_external === 'yes') {
    header("Location: " . $external_link);
    exit();
}

header("Location: dashboard.php");
exit();
?>
