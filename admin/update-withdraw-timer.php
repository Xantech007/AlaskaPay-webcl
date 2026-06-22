<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {

    /* -----------------------------
       WITHDRAW TIMER (GLOBAL)
    ------------------------------*/

    $duration = isset($_POST['duration']) && $_POST['duration'] !== ''
        ? (int) $_POST['duration']
        : 0;

    if ($duration < 0) {
        throw new Exception("Invalid duration value");
    }

    $stmt = $pdo->prepare("
        UPDATE region_settings SET
            duration = ?
    ");

    $stmt->execute([
        $duration
    ]);

    $_SESSION['success'] = "Withdraw timer updated successfully";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
