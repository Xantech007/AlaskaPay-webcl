<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {

    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        throw new Exception("Invalid region ID");
    }

    $stmt = $pdo->prepare("DELETE FROM region_settings WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Region deleted successfully";

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
