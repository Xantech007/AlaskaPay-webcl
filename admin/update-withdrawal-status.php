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
        throw new Exception("Invalid withdrawal ID");
    }

    $allowedStatuses = ['pending', 'approved', 'rejected'];

    if (!in_array($status, $allowedStatuses)) {
        throw new Exception("Invalid status selected");
    }

    // =========================
    // GET WITHDRAWAL
    // =========================
    $stmt = $pdo->prepare("
        SELECT id, user_id, amount
        FROM withdrawals
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $withdrawal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$withdrawal) {
        throw new Exception("Withdrawal not found");
    }

    // =========================
    // BEGIN TRANSACTION
    // =========================
    $pdo->beginTransaction();

    // UPDATE WITHDRAWAL STATUS
    $updateWithdrawal = $pdo->prepare("
        UPDATE withdrawals
        SET status = ?
        WHERE id = ?
    ");

    $updateWithdrawal->execute([
        $status,
        $id
    ]);

    $pdo->commit();

    $_SESSION['success'] = "Withdrawal status updated successfully";

} catch (PDOException $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = $e->getMessage();
}

header("Location: withdrawals");
exit();
