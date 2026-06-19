<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$user_id = $_SESSION['user_id'];

$country = trim($_POST['country'] ?? '');

/*
|--------------------------------------------------------------------------
| GET SELECTED PAYMENT METHOD INDEX
|--------------------------------------------------------------------------
*/
$selected_index = $_POST['payment_type'] ?? '';

if ($selected_index === '') {
    return;
}

/*
|--------------------------------------------------------------------------
| FETCH PAYMENT METHOD FROM DATABASE
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT method, method_name, method_id
    FROM payment_methods
    WHERE country = ?
    ORDER BY type
");

$stmt->bind_param("s", $country);
$stmt->execute();
$result = $stmt->get_result();

$methods = [];

while ($row = $result->fetch_assoc()) {
    $methods[] = $row;
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| VALIDATE SELECTED METHOD EXISTS
|--------------------------------------------------------------------------
*/
if (!isset($methods[$selected_index])) {
    return;
}

$selected = $methods[$selected_index];

$method = trim($selected['method'] ?? '');
$method_name = trim($selected['method_name'] ?? '');
$method_id = trim($selected['method_id'] ?? '');

/*
|--------------------------------------------------------------------------
| SAVE TO USERS TABLE
|--------------------------------------------------------------------------
*/
if ($method !== '' && $method_name !== '' && $method_id !== '') {

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
        $method,
        $method_name,
        $method_id,
        $user_id
    );

    $stmt->execute();
    $stmt->close();
}

$_SESSION['country'] = $country;
