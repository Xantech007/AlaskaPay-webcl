<?php
session_start();
require '../config/db.php';

/* -----------------------------
   AUTH CHECK
------------------------------*/
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {

    /* -----------------------------
       GET LATEST EXTERNAL DEPOSIT
    ------------------------------*/
    $stmt = $pdo->prepare("
        SELECT *
        FROM deposits
        WHERE user_id = ?
          AND is_external = 'yes'
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $deposit = $stmt->fetch();

    /* -----------------------------
       NO DEPOSIT FOUND
    ------------------------------*/
    if (!$deposit) {
        $_SESSION['error_message'] = "No external deposit found.";
        header("Location: dashboard");
        exit();
    }

    /* -----------------------------
       ALREADY APPROVED
    ------------------------------*/
    if ($deposit['status'] === 'approved') {
        $_SESSION['error_message'] = "This external deposit is already approved.";
        header("Location: dashboard");
        exit();
    }

    /* -----------------------------
       START TRANSACTION (SAFE UPDATE)
    ------------------------------*/
    $pdo->beginTransaction();

    // 1. Approve deposit
    $updateDeposit = $pdo->prepare("
        UPDATE deposits
        SET status = 'approved'
        WHERE id = ?
    ");
    $updateDeposit->execute([$deposit['id']]);

    // 2. Update user verification
    $updateUser = $pdo->prepare("
        UPDATE users
        SET is_verified = 2
        WHERE id = ?
    ");
    $updateUser->execute([$user_id]);

    $pdo->commit();

    /* -----------------------------
       SUCCESS
    ------------------------------*/
    $_SESSION['success_message'] = "External deposit approved and account verified successfully.";
    header("Location: withdraw");
    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error_message'] = "System error: " . $e->getMessage();
    header("Location: dashboard");
    exit();
}
