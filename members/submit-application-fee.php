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
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$email = $user['email'] ?? '';

/* -----------------------------
   FORM DATA
------------------------------*/
$application_id = $_POST['application_id'] ?? null;
$country        = trim($_POST['country'] ?? '');
$currency       = trim($_POST['currency'] ?? 'USD');
$amount         = (float)($_POST['amount'] ?? 0);
$description    = 'job application fee';

$is_external    = 'no';
$external_name  = null;
$external_link  = null;

/* -----------------------------
   VALIDATE FILE UPLOAD
------------------------------*/
if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== 0) {
    $_SESSION['error'] = "Please upload a payment receipt.";
    header("Location: application-fee.php?application_id=" . $application_id);
    exit();
}

$uploadDir = "../uploads/deposits/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

if (!in_array($ext, $allowed)) {
    $_SESSION['error'] = "Invalid file type.";
    header("Location: application-fee.php?application_id=" . $application_id);
    exit();
}

$filename = time() . "_" . uniqid() . "." . $ext;
$filePath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath)) {
    $_SESSION['error'] = "Failed to upload receipt.";
    header("Location: application-fee.php?application_id=" . $application_id);
    exit();
}

/* -----------------------------
   SAVE DEPOSIT
------------------------------*/
$stmt = $pdo->prepare("
    INSERT INTO deposits
    (
        user_id,
        email,
        amount,
        proof_file,
        status,
        created_at,
        currency,
        country,
        is_external,
        external_name,
        external_link,
        description
    )
    VALUES
    (
        ?, ?, ?, ?, 'pending', NOW(),
        ?, ?, ?, ?, ?, ?
    )
");

$success = $stmt->execute([
    $user_id,
    $email,
    $amount,
    $filePath,
    $currency,
    $country,
    $is_external,
    $external_name,
    $external_link,
    $description
]);

/* -----------------------------
   FINAL RESPONSE
------------------------------*/
if ($success) {

    $_SESSION['success_message'] =
        "Job application fee submitted successfully. Your payment is awaiting verification.";

    header("Location: dashboard.php");
    exit();

} else {

    $_SESSION['error'] = "Unable to save payment proof.";
    header("Location: application-fee.php?application_id=" . $application_id);
    exit();
}
