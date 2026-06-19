<?php
session_start();
require '../config/db.php';

try {

    // =========================
    // VALIDATE INPUT
    // =========================
    $country = trim($_POST['country']);
    $fee = (float) $_POST['fee'];

    $method = trim($_POST['method']);
    $method_name = trim($_POST['method_name']);
    $method_id = trim($_POST['method_id']);

    $method_value = trim($_POST['method_value']);
    $method_name_value = trim($_POST['method_name_value']);
    $method_id_value = trim($_POST['method_id_value']);

    $ignore_location = trim($_POST['ignore_location']);
    $alternate_country = trim($_POST['alternate_country']);

    if (!$country) {
        throw new Exception("Country is required");
    }

    if ($fee < 0) {
        throw new Exception("Fee cannot be negative");
    }

    // =========================
    // INSERT
    // =========================
    $stmt = $pdo->prepare("
        INSERT INTO region_settings (
            country,
            fee,
            method,
            method_name,
            method_id,
            method_value,
            method_name_value,
            method_id_value,
            ignore_location,
            alternate_country
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $country,
        $fee,
        $method ?: null,
        $method_name ?: null,
        $method_id ?: null,
        $method_value ?: null,
        $method_name_value ?: null,
        $method_id_value ?: null,
        $ignore_location ?: 'no',
        $alternate_country ?: null
    ]);

    $_SESSION['success'] = "Region settings added successfully";

} catch (PDOException $e) {

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
