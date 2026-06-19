<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$user_id = $_SESSION['user_id'];

$country = trim($_POST['country'] ?? '');

$verified_method = trim($_POST['method'] ?? '');
$verified_account_name = trim($_POST['method_name'] ?? '');
$verified_account_id = trim($_POST['method_id'] ?? '');

if (
    $verified_method !== '' &&
    $verified_account_name !== '' &&
    $verified_account_id !== ''
) {

    $stmt = $conn->prepare("
        UPDATE users
        SET
            verified_method = ?,
            verified_account_name = ?,
            verified_account_id = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssi",
        $verified_method,
        $verified_account_name,
        $verified_account_id,
        $user_id
    );

    $stmt->execute();
    $stmt->close();
}

$_SESSION['country'] = $country;
