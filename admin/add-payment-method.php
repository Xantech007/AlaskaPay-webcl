<?php
session_start();
require '../config/db.php';

try {

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

    $stmt = $pdo->prepare("
        INSERT INTO payment_methods
        (
            country,
            method,
            method_name,
            method_id,
            type
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $country,
        $method,
        $method_name,
        $method_id,
        $type
    ]);

    $_SESSION['success'] = "Payment method added successfully";

} catch (PDOException $e) {

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: payment-settings");
exit();
