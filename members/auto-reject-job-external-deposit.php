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
       GET LATEST PENDING APPLICATION
    ------------------------------*/
    $stmt = $pdo->prepare("
        SELECT *
        FROM job_applications
        WHERE user_id = ?
          AND status = 'pending'
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $application = $stmt->fetch();

    /* -----------------------------
       NO APPLICATION FOUND
    ------------------------------*/
    if (!$application) {
        $_SESSION['error'] = "No pending application found.";
        header("Location: application-fee");
        exit();
    }

    /* -----------------------------
       START TRANSACTION
    ------------------------------*/
    $pdo->beginTransaction();

    // 1. Mark as rejected
    $update = $pdo->prepare("
        UPDATE job_applications
        SET status = 'rejected'
        WHERE id = ?
    ");
    $update->execute([$application['id']]);

    $pdo->commit();

    /* -----------------------------
       STORE DATA IN SESSION (FIXED)
    ------------------------------*/
    $_SESSION['error'] = "Payment Failed, Please try again.";

    $_SESSION['application_data'] = [
        'application_id'  => $application['id'],
        'full_name'       => $application['full_name'],
        'sector'          => $application['sector'],
        'expected_salary' => $application['expected_salary']
    ];

    /* -----------------------------
       REDIRECT
    ------------------------------*/
    header("Location: application-fee");
    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = "System error: " . $e->getMessage();
    header("Location: application-fee");
    exit();
}
