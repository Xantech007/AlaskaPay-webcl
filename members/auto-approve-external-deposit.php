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
       APPROVE DEPOSIT
    ------------------------------*/
    $update = $pdo->prepare("
        UPDATE deposits
        SET status = 'approved'
        WHERE id = ?
    ");
    $update->execute([$deposit['id']]);

    /* -----------------------------
       SUCCESS
    ------------------------------*/
    $_SESSION['success_message'] = "External deposit approved successfully.";
    header("Location: withdraw");
    exit();

} catch (Exception $e) {
    $_SESSION['error_message'] = "System error: " . $e->getMessage();
    header("Location: dashboard");
    exit();
}
