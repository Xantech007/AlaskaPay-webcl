<?php
session_start();
require '../config/db.php';

try {

    // =========================
    // VALIDATE INPUT
    // =========================
    $id = (int) $_POST['id'];

    if ($id <= 0) {
        throw new Exception("Invalid region ID");
    }

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

    // =========================
    // CHECK EXISTENCE
    // =========================
    $check = $pdo->prepare("SELECT id FROM region_settings WHERE id = ?");
    $check->execute([$id]);

    if (!$check->fetch()) {
        throw new Exception("Region not found");
    }

    // =========================
    // UPDATE
    // =========================
    $stmt = $pdo->prepare("
        UPDATE region_settings SET
            country = ?,
            fee = ?,
            method = ?,
            method_name = ?,
            method_id = ?,
            method_value = ?,
            method_name_value = ?,
            method_id_value = ?,
            ignore_location = ?,
            alternate_country = ?
        WHERE id = ?
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
        $alternate_country ?: null,
        $id
    ]);

    $_SESSION['success'] = "Region settings updated successfully";

} catch (PDOException $e) {

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
