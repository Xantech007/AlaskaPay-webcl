<?php

require '../config/db.php';

$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
INSERT INTO users (
email,
password,
full_name,
phone,
balance,
is_verified,
status,
verified_method,
verified_account_name,
verified_account_id,
method,
method_name,
method_id
)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->execute([
    $_POST['email'],
    $password,
    $_POST['full_name'],
    $_POST['phone'],
    $_POST['balance'],
    $_POST['is_verified'],
    $_POST['status'],
    $_POST['verified_method'],
    $_POST['verified_account_name'],
    $_POST['verified_account_id'],
    $_POST['method'],
    $_POST['method_name'],
    $_POST['method_id']
]);

header("Location: users.php");
