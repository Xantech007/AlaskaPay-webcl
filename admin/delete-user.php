<?php
session_start();
require '../config/db.php';

try {

    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "User deleted successfully";

} catch (Exception $e) {
    $_SESSION['error'] = "Delete failed: " . $e->getMessage();
}

header("Location: users.php");
exit();
