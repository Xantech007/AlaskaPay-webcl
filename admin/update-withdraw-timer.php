<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $duration = (int) $_POST['duration'];

    try {

        // ❗ THIS is intentionally GLOBAL update (no WHERE clause)
        $stmt = $pdo->prepare("
            UPDATE region_settings
            SET duration = :duration
        ");

        $stmt->execute([
            ':duration' => $duration
        ]);

        header("Location: region-settings?success=timer_updated");
        exit();

    } catch (Exception $e) {
        die("Error updating timer: " . $e->getMessage());
    }
}
