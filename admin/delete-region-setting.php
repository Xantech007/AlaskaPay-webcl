<?php
session_start();
require '../config/db.php';

try {

    $id = (int) $_GET['id'];

    if ($id <= 0) {
        throw new Exception("Invalid ID");
    }

    $stmt = $pdo->prepare("DELETE FROM region_settings WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Region setting deleted successfully";

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
