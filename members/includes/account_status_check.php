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

/* Fetch only status */
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
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Account Suspended</title>

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
            }

            body {
                background: linear-gradient(135deg, #0f0f0f, #1c1c1c);
                color: #fff;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 20px;
            }

            .box {
                background: #1a1a1a;
                border: 1px solid #ff4d4d;
                border-radius: 16px;
                padding: 40px;
                max-width: 500px;
                width: 100%;
                box-shadow: 0 10px 30px rgba(0,0,0,0.6);
                animation: fadeIn 0.4s ease-in-out;
            }

            h1 {
                color: #ff4d4d;
                font-size: 2rem;
                margin-bottom: 10px;
            }

            p {
                font-size: 1.1rem;
                opacity: 0.9;
                line-height: 1.5;
            }

            .icon {
                font-size: 50px;
                margin-bottom: 15px;
                color: #ff4d4d;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(15px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* MOBILE RESPONSIVE */
            @media (max-width: 600px) {

                .box {
                    padding: 25px;
                    border-radius: 12px;
                }

                h1 {
                    font-size: 1.6rem;
                }

                p {
                    font-size: 1rem;
                }

                .icon {
                    font-size: 40px;
                }
            }
        </style>
    </head>

    <body>

        <div class="box">

            <div class="icon">⚠️</div>

            <h1>Account Suspended</h1>

            <p>
                Your account has been suspended.<br>
                Please contact support to restore access.
            </p>

        </div>

    </body>
    </html>
    ';

    exit();
}
