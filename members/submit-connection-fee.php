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
| USER EMAIL
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
| REGION DATA
|--------------------------------------------------------------------------
*/
$country = $_SESSION['conn_fee']['country'] ?? '';

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
    header("Location: withdraw");
    exit();
}

$amount = (float)$region['fee'];
$currency = $region['currency'] ?? 'USD';
$is_external = $region['use_external'] ?? 'no';
$external_name = $region['external_name'] ?? null;
$external_link = $region['external_link'] ?? null;

/*
|--------------------------------------------------------------------------
| FILE UPLOAD
|--------------------------------------------------------------------------
*/
if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== 0) {
    $_SESSION['error'] = "Please upload a payment receipt.";
    header("Location: connection-fee");
    exit();
}

$uploadDir = "../uploads/deposits/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    $_SESSION['error'] = "Invalid file type.";
    header("Location: connection-fee");
    exit();
}

$filename = time() . "_" . uniqid() . "." . $ext;
$filePath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $filePath)) {
    $_SESSION['error'] = "Failed to upload receipt.";
    header("Location: connection-fee");
    exit();
}

/*
|--------------------------------------------------------------------------
| INSERT DEPOSIT (UPDATED FULL STRUCTURE)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO deposits (
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
    (
        ?, ?, ?, ?, 'pending',
        ?, ?, ?, ?, ?, NOW()
    )
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

if ($stmt->execute()) {

    /*
    |--------------------------------------------------------------------------
    | USER VERIFICATION UPDATE
    |--------------------------------------------------------------------------
    */
    $update = $conn->prepare("
        UPDATE users
        SET is_verified = 1,
            verified_at = NOW()
        WHERE id = ?
    ");

    $update->bind_param("i", $user_id);
    $update->execute();
    $update->close();

    $_SESSION['success_message'] =
        "Payment proof submitted successfully. Your payment is awaiting verification.";

    header("Location: dashboard");
    exit();

} else {

    $_SESSION['error'] = "Unable to save payment proof.";
    header("Location: connection-fee");
    exit();
}
?>
