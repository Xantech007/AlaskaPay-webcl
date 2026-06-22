<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {

    /* -----------------------------
       CS NUMBER (GLOBAL ADMIN)
    ------------------------------*/

    $csnumber = isset($_POST['csnumber']) && $_POST['csnumber'] !== ''
        ? trim($_POST['csnumber'])
        : '';

    if ($csnumber === '') {
        throw new Exception("CS number cannot be empty");
    }

    // Optional: basic validation (adjust if needed)
    if (strlen($csnumber) > 50) {
        throw new Exception("CS number is too long");
    }

    $stmt = $pdo->prepare("
        UPDATE admin
        SET cs_number = ?
    ");

    $stmt->execute([
        $csnumber
    ]);

    $_SESSION['success'] = "CS number updated successfully";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
