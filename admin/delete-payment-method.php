<?php
session_start();
require '../config/db.php';

try {

    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        throw new Exception("Invalid payment method ID");
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
        DELETE FROM payment_methods
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $_SESSION['success'] = "Payment method deleted successfully";

} catch (PDOException $e) {

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: payment-settings");
exit();
