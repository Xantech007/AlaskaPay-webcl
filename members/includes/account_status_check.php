<?php
// includes/account_status_check.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$status = $stmt->fetchColumn();

/* If user not found */
if (!$status) {
    header("Location: ../logout.php");
    exit();
}

/* BLOCK SUSPENDED USERS GLOBALLY */
if ($status === 'suspended') {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Account Suspended</title>
        <style>
            body{
                margin:0;
                height:100vh;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#0f0f0f;
                color:#fff;
                font-family:Inter, sans-serif;
                text-align:center;
            }
            .box{
                padding:30px;
                border:1px solid #ff4d4d;
                border-radius:12px;
                background:#1a1a1a;
            }
            h1{
                color:#ff4d4d;
                margin-bottom:10px;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>Account Suspended</h1>
            <p>Contact Support.</p>
        </div>
    </body>
    </html>';
    exit();
}
