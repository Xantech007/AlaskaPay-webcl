<?php
// includes/account_status_check.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch only status (lightweight check) */
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userStatus = $stmt->fetchColumn();

if (!$userStatus) {
    header("Location: ../logout.php");
    exit();
}

/* BLOCK SUSPENDED USERS */
if ($userStatus === 'suspended') {

    echo '
    <div style="
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:#111;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-direction:column;
        z-index:999999;
        text-align:center;
        padding:20px;
    ">
        <h1 style="color:#ff4d4d;margin-bottom:10px;">
            Account Suspended
        </h1>

        <p style="font-size:18px;">
            Contact Support.
        </p>
    </div>
    ';

    exit();
}
