<?php

require '../config/db.php';

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);

header("Location: users.php");
