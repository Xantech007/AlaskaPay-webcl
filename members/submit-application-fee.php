<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* -----------------------------
   USER INFO
------------------------------*/
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$email = $user['email'] ?? '';

/* -----------------------------
   POST DATA
------------------------------*/
$application_id = $_POST['application_id'] ?? null;
$amount         = $_POST['amount'] ?? 0;
$currency       = $_POST['currency'] ?? 'USD';
$country        = $_POST['country'] ?? '';
$description    = $_POST['description'] ?? 'job application fee';

/* -----------------------------
   VALIDATION
------------------------------*/
if (!$application_id || !$amount) {
    $_SESSION['error'] = "Invalid payment submission.";
    header("Location: application-fee.php");
    exit();
}

/* -----------------------------
   UPLOAD RECEIPT
------------------------------*/
$receipt_path = null;

if (!empty($_FILES['receipt']['name'])) {

    $targetDir = "../uploads/job-fees/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["receipt"]["name"]);
    $receipt_path = $targetDir . $fileName;

    move_uploaded_file($_FILES["receipt"]["tmp_name"], $receipt_path);
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
        currency,
        country,
        status,
        description,
        receipt_path,
        created_at
    )
    VALUES
    (?, ?, ?, ?, ?, 'pending', ?, ?, NOW())
");

$stmt->execute([
    $user_id,
    $email,
    $amount,
    $currency,
    $country,
    $description,
    $receipt_path
]);

$deposit_id = $pdo->lastInsertId();

/* -----------------------------
   LINK TO JOB APPLICATION
------------------------------*/
$stmt = $pdo->prepare("
    UPDATE job_applications
    SET payment_status = 'pending',
        deposit_id = ?
    WHERE id = ?
");

$stmt->execute([
    $deposit_id,
    $application_id
]);

/* -----------------------------
   RESPONSE
------------------------------*/
$_SESSION['success_message'] = "Payment proof submitted successfully. Awaiting verification.";

header("Location: dashboard);
exit();
