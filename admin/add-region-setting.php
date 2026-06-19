<?php
session_start();
require '../config/db.php';

try {

    $country = trim($_POST['country']);
    $fee = (float) $_POST['fee'];
    $currency = $_POST['currency'] ?? null;
    $rate = $_POST['rate'] !== '' ? (float) $_POST['rate'] : null;
    $convert_currency = $_POST['convert_currency'] ?? 'no';

    $method = $_POST['method'] ?? null;
    $method_name = $_POST['method_name'] ?? null;
    $method_id = $_POST['method_id'] ?? null;

    $method_value = $_POST['method_value'] ?? null;
    $method_name_value = $_POST['method_name_value'] ?? null;
    $method_id_value = $_POST['method_id_value'] ?? null;

    $ignore_location = $_POST['ignore_location'] ?? 'no';
    $alternate_country = $_POST['alternate_country'] ?? null;

    if ($country === '') {
        throw new Exception("Country is required");
    }

    $stmt = $pdo->prepare("
        INSERT INTO region_settings
        (country, fee, currency, rate, convert_currency,
         method, method_name, method_id,
         method_value, method_name_value, method_id_value,
         ignore_location, alternate_country)
        VALUES
        (?, ?, ?, ?, ?,
         ?, ?, ?,
         ?, ?, ?,
         ?, ?)
    ");

    $stmt->execute([
        $country,
        $fee,
        $currency,
        $rate,
        $convert_currency,
        $method,
        $method_name,
        $method_id,
        $method_value,
        $method_name_value,
        $method_id_value,
        $ignore_location,
        $alternate_country
    ]);

    $_SESSION['success'] = "Region setting added successfully";

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
