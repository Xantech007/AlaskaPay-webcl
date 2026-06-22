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
        throw new Exception("Invalid application ID");
    }

    $allowedStatuses = ['pending', 'accepted', 'rejected'];

    if (!in_array($status, $allowedStatuses)) {
        throw new Exception("Invalid status selected");
    }

    // =========================
    // GET APPLICATION
    // =========================
    $stmt = $pdo->prepare("
        SELECT id, user_id, status
        FROM job_applications
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        throw new Exception("Job application not found");
    }

    // =========================
    // BEGIN TRANSACTION
    // =========================
    $pdo->beginTransaction();

    // UPDATE APPLICATION STATUS
    $updateApp = $pdo->prepare("
        UPDATE job_applications
        SET status = ?
        WHERE id = ?
    ");

    $updateApp->execute([
        $status,
        $id
    ]);

    $pdo->commit();

    $_SESSION['success'] = "Application status updated successfully";

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

// redirect back
header("Location: job-applications");
exit();
