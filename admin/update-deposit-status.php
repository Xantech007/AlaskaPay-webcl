<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit('Unauthorized');
}

$id = (int)$_POST['id'];
$status = $_POST['status'];

$allowed = ['pending', 'approved', 'rejected'];

if (!in_array($status, $allowed)) {
    exit('Invalid status');
}

$stmt = $pdo->prepare("
    UPDATE deposits
    SET status = ?
    WHERE id = ?
");

$stmt->execute([$status, $id]);

header("Location: deposits");
exit();
