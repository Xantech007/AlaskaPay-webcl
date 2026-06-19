<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

require '../config/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../login.php");
    exit();
}

$country = trim($_POST['country'] ?? '');
$type = trim($_POST['type'] ?? '');

/*
|--------------------------------------------------------------------------
| INPUT VALUES
|--------------------------------------------------------------------------
*/

// FALLBACK FORM INPUTS
$verified_method = trim($_POST['method'] ?? '');
$verified_account_name = trim($_POST['method_name'] ?? '');
$verified_account_id = trim($_POST['method_id'] ?? '');

// USA FORM INPUTS (optional handling if needed later)
$usa_method = trim($_POST['method'] ?? '');
$usa_account = trim($_POST['account'] ?? '');

/*
|--------------------------------------------------------------------------
| FINAL VALUES LOGIC
|--------------------------------------------------------------------------
*/

// DEFAULTS FROM DB (optional fallback)
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
| DETERMINE WHAT TO SAVE
|--------------------------------------------------------------------------
*/

if ($type === 'fallback') {

    // USER OVERRIDES (SAVE THESE INTO VERIFIED FIELDS)
    $final_verified_method = $verified_method;
    $final_verified_name = $verified_account_name;
    $final_verified_id = $verified_account_id;

} elseif ($type === 'usa') {

    // USA FLOW (store differently if needed)
    $final_verified_method = $usa_method;
    $final_verified_name = 'USA Account';
    $final_verified_id = $usa_account;

} else {

    $final_verified_method = $method;
    $final_verified_name = $method_name;
    $final_verified_id = $method_id;
}

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
    $method,
    $method_name,
    $method_id,

    $final_verified_method,
    $final_verified_name,
    $final_verified_id,

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

/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

header("Location: connection-fee");
exit();
