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
    // GET DEPOSIT
    // =========================
    $stmt = $pdo->prepare("
        SELECT id, user_id
        FROM deposits
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $deposit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deposit) {
        throw new Exception("Deposit not found");
    }

    // =========================
    // MAP STATUS TO VERIFIED
    // =========================
    switch ($status) {
        case 'approved':
            $is_verified = 2;
            break;

        case 'pending':
            $is_verified = 1;
            break;

        case 'rejected':
        default:
            $is_verified = 0;
            break;
    }

    // =========================
    // BEGIN TRANSACTION
    // =========================
    $pdo->beginTransaction();

    // UPDATE DEPOSIT STATUS
    $updateDeposit = $pdo->prepare("
        UPDATE deposits
        SET status = ?
        WHERE id = ?
    ");

    $updateDeposit->execute([
        $status,
        $id
    ]);

    // UPDATE USER VERIFICATION STATUS
    $updateUser = $pdo->prepare("
        UPDATE users
        SET
            is_verified = ?,
            verified_at = CASE
                WHEN ? = 2 THEN NOW()
                ELSE verified_at
            END
        WHERE id = ?
    ");
    
    $updateUser->execute([
        $is_verified,
        $is_verified,
        $deposit['user_id']
    ]);

    $pdo->commit();

    $_SESSION['success'] = "Deposit status updated successfully";

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = "Database error occurred. Please try again.";

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = $e->getMessage();
}

header("Location: deposits");
exit();
