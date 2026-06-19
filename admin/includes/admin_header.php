<?php
// admin_header.php - Top Navigation Admin Layout (2026)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin Dashboard'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background: #f4f6f9;
        }

        /* FIX NAVBAR OVERLAP */
        .app-wrapper {
            padding-top: 75px;
        }

        /* TOP NAVBAR */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.85);
            font-weight: 500;
            margin-right: 10px;
            transition: 0.2s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }

        /* REMOVE DEFAULT BUTTON COLOR CONFLICTS */
        .btn {
            border-radius: 8px;
        }
    </style>

</head>

<body>

<div class="app-wrapper">

<!-- TOP NAVIGATION BAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3">

    <!-- LOGO -->
    <a class="navbar-brand text-white" href="./dashboard.php">
        <i class="fas fa-coins text-warning me-2"></i>
        AlaskaPay
    </a>

    <!-- MOBILE TOGGLE -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- MENU -->
    <div class="collapse navbar-collapse" id="topNav">

        <!-- CENTER LINKS -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
        
            <li class="nav-item">
                <a class="nav-link" href="./dashboard.php">
                    <i class="fa-solid fa-chart-line me-1"></i> Overview
                </a>
            </li>
        
            <li class="nav-item">
                <a class="nav-link" href="./users.php">
                    <i class="fa-solid fa-user-group me-1"></i> Users
                </a>
            </li>
        
            <li class="nav-item">
                <a class="nav-link" href="./deposits.php">
                    <i class="fa-solid fa-arrow-down-wide-short me-1"></i> Deposits
                </a>
            </li>
        
            <li class="nav-item">
                <a class="nav-link" href="./withdrawals.php">
                    <i class="fa-solid fa-arrow-up-wide-short me-1"></i> Withdrawals
                </a>
            </li>
        
            <li class="nav-item">
                <a class="nav-link" href="./payment-settings.php">
                    <i class="fa-solid fa-credit-card me-1"></i> Payment Settings
                </a>
            </li>
        
            <li class="nav-item">
                <a class="nav-link" href="./region-settings.php">
                    <i class="fa-solid fa-globe me-1"></i> Region Settings
                </a>
            </li>
        
        </ul>

        <!-- RIGHT SIDE -->
        <div class="d-flex">
            <a href="./logout.php" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>
                Logout
            </a>
        </div>

    </div>
</nav>
