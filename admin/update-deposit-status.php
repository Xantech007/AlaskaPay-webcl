<?php
session_start();
require '../config/db.php';

try {

    // =========================
    // VALIDATE INPUT
    // =========================
    $id = (int) $_POST['id'];
    $status = trim($_POST['status']);

    if ($id <= 0) {
        throw new Exception("Invalid deposit ID");
    }

    $allowedStatuses = ['pending', 'approved', 'rejected'];

    if (!in_array($status, $allowedStatuses)) {
        throw new Exception("Invalid status selected");
    }

    // =========================
    // CHECK IF DEPOSIT EXISTS
    // =========================
    $checkDeposit = $pdo->prepare("
        SELECT id
        FROM deposits
        WHERE id = ?
    ");

    $checkDeposit->execute([$id]);

    if (!$checkDeposit->fetch()) {
        throw new Exception("Deposit not found");
    }

    // =========================
    // UPDATE STATUS
    // =========================
    $stmt = $pdo->prepare("
        UPDATE deposits
        SET status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $status,
        $id
    ]);

    $_SESSION['success'] = "Deposit status updated successfully";

} catch (PDOException $e) {

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();

}

header("Location: deposits");
exit();
