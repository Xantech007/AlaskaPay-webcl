<?php
session_start();
require '../config/db.php';

try {

    $id = (int) $_POST['id'];

    if ($id <= 0) {
        throw new Exception("Invalid payment method ID");
    }

    $country = trim($_POST['country']);
    $method = trim($_POST['method']);
    $method_name = trim($_POST['method_name']);
    $method_id = trim($_POST['method_id']);
    $type = trim($_POST['type']);

    if (
        empty($country) ||
        empty($method) ||
        empty($method_name) ||
        empty($method_id) ||
        empty($type)
    ) {
        throw new Exception("All fields are required");
    }

    $allowedTypes = ['bank', 'momo', 'crypto'];

    if (!in_array($type, $allowedTypes)) {
        throw new Exception("Invalid payment type");
    }

    $check = $pdo->prepare("
        SELECT id
        FROM payment_methods
        WHERE id = ?
    ");

    $check->execute([$id]);

    if (!$check->fetch()) {
        throw new Exception("Payment method not found");
    }

    $stmt = $pdo->prepare("
        UPDATE payment_methods
        SET
            country = ?,
            method = ?,
            method_name = ?,
            method_id = ?,
            type = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $country,
        $method,
        $method_name,
        $method_id,
        $type,
        $id
    ]);

    $_SESSION['success'] = "Payment method updated successfully";

} catch (PDOException $e) {

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: payment-settings");
exit();
