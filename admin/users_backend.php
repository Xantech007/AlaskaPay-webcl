<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$action = $_POST['action'] ?? '';

/* =========================
   ADD USER
========================= */
if ($action === 'add_user') {

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, full_name, phone, balance, is_verified, status)
        VALUES (?, ?, ?, ?, 0, 0, 'active')
    ");

    $stmt->execute([$email, $password, $full_name, $phone]);

    header("Location: users.php");
    exit();
}

/* =========================
   UPDATE USER
========================= */
if ($action === 'update_user') {

    $id = $_POST['id'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $balance = $_POST['balance'];
    $is_verified = $_POST['is_verified'];
    $status = $_POST['status'];
    $verified_method = $_POST['verified_method'];
    $verified_account_name = $_POST['verified_account_name'];
    $verified_account_id = $_POST['verified_account_id'];

    // password optional
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            UPDATE users SET
            email=?, password=?, full_name=?, phone=?, balance=?,
            is_verified=?, status=?, verified_method=?,
            verified_account_name=?, verified_account_id=?
            WHERE id=?
        ");

        $stmt->execute([
            $email, $password, $full_name, $phone, $balance,
            $is_verified, $status, $verified_method,
            $verified_account_name, $verified_account_id, $id
        ]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE users SET
            email=?, full_name=?, phone=?, balance=?,
            is_verified=?, status=?, verified_method=?,
            verified_account_name=?, verified_account_id=?
            WHERE id=?
        ");

        $stmt->execute([
            $email, $full_name, $phone, $balance,
            $is_verified, $status, $verified_method,
            $verified_account_name, $verified_account_id, $id
        ]);
    }

    header("Location: users.php");
    exit();
}
