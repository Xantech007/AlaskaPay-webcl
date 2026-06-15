<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="nav-tabs">
    <button class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>"
        onclick="location.href='dashboard.php'">
        Dashboard
    </button>

    <button class="<?= $currentPage == 'apply-loan.php' ? 'active' : '' ?>"
        onclick="location.href='apply-loan.php'">
        Apply Loan
    </button>

    <button class="<?= $currentPage == 'withdrawal-history.php' ? 'active' : '' ?>"
        onclick="location.href='withdrawal-history.php'">
        Withdrawal History
    </button>

    <button class="<?= $currentPage == 'profile.php' ? 'active' : '' ?>"
        onclick="location.href='profile.php'">
        Profile
    </button>

    <button onclick="logout()">
        Logout
    </button>
</div>
