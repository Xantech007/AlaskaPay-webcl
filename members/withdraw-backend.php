<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$user_id = $_SESSION['user_id'];

$country = trim($_POST['country'] ?? '');

/* USER ENTERED VALUES */
$verified_method = trim($_POST['method'] ?? '');
$verified_account_name = trim($_POST['method_name'] ?? '');
$verified_account_id = trim($_POST['method_id'] ?? '');

/* DB LABEL VALUES (hidden fields from fallback form) */
$db_method = trim($_POST['method_label'] ?? '');
$db_method_name = trim($_POST['method_name_label'] ?? '');
$db_method_id = trim($_POST['method_id_label'] ?? '');

if (
    $verified_method !== '' &&
    $verified_account_name !== '' &&
    $verified_account_id !== ''
) {

    $stmt = $conn->prepare("
        UPDATE users
        SET
            method = ?,
            method_name = ?,
            method_id = ?,

            verified_method = ?,
            verified_account_name = ?,
            verified_account_id = ?

        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssssi",
        $db_method,
        $db_method_name,
        $db_method_id,

        $verified_method,
        $verified_account_name,
        $verified_account_id,

        $user_id
    );

    $stmt->execute();
    $stmt->close();
}

$_SESSION['country'] = $country;
