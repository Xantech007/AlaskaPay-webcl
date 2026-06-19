<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$user_id = $_SESSION['user_id'];

$country = trim($_POST['country'] ?? '');

/*
|--------------------------------------------------------------------------
| USER INPUT (fallback override from form)
|--------------------------------------------------------------------------
*/
$verified_method = trim($_POST['method'] ?? '');
$verified_account_name = trim($_POST['method_name'] ?? '');
$verified_account_id = trim($_POST['method_id'] ?? '');

/*
|--------------------------------------------------------------------------
| FETCH DEFAULT METHOD FROM payment_methods (BY COUNTRY)
|--------------------------------------------------------------------------
*/
$method = '';
$method_name = '';
$method_id = '';

if (!empty($country)) {

    $stmt = $conn->prepare("
        SELECT method, method_name, method_id
        FROM payment_methods
        WHERE country = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $country);
    $stmt->execute();
    $result = $stmt->get_result();
    $pm = $result->fetch_assoc();
    $stmt->close();

    if ($pm) {
        $method = $pm['method'] ?? '';
        $method_name = $pm['method_name'] ?? '';
        $method_id = $pm['method_id'] ?? '';
    }
}

/*
|--------------------------------------------------------------------------
| PRIORITY: USER INPUT OVER DATABASE DEFAULT
|--------------------------------------------------------------------------
*/
$final_method = $verified_method !== '' ? $verified_method : $method;
$final_method_name = $verified_account_name !== '' ? $verified_account_name : $method_name;
$final_method_id = $verified_account_id !== '' ? $verified_account_id : $method_id;

/*
|--------------------------------------------------------------------------
| UPDATE USERS TABLE
|--------------------------------------------------------------------------
*/
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
    $final_method,
    $final_method_name,
    $final_method_id,

    $verified_method,
    $verified_account_name,
    $verified_account_id,

    $user_id
);

$stmt->execute();
$stmt->close();

/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/
$_SESSION['country'] = $country;
