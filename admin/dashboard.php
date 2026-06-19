<?php
// admin/dashboard.php - Modern Admin Dashboard (Enhanced 2026)

session_start();
require '../config/db.php';

// Admin authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
include './includes/admin_header.php';

<style>
/* ===========================
   QUICK ACTIONS - CLEAN FIX
   Overrides broken global .btn styles
=========================== */

.quick-actions {
    margin-top: 40px;
}

.quick-actions h4 {
    font-weight: 700;
    margin-bottom: 20px;
}

/* Force buttons to ignore global admin.css */
.quick-action-btn {
    display: flex !important;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    padding: 28px 15px !important;
    border-radius: 14px !important;

    text-decoration: none !important;

    font-weight: 600;
    font-size: 15px;

    color: #fff !important;

    transition: all 0.25s ease-in-out;

    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

/* ICON FIX */
.quick-action-btn i {
    font-size: 32px;
    margin-bottom: 10px;
    color: #fff !important;
}

/* COLORS (override Bootstrap + your .btn) */
.qa-users {
    background: #0d6efd !important;
}

.qa-deposits {
    background: #198754 !important;
}

.qa-withdrawals {
    background: #dc3545 !important;
}

.qa-settings {
    background: #fd7e14 !important;
}

/* HOVER EFFECT */
.quick-action-btn:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    filter: brightness(1.05);
}

/* MOBILE FIX */
@media (max-width: 768px) {
    .quick-action-btn {
        padding: 22px 10px !important;
        font-size: 14px;
    }

    .quick-action-btn i {
        font-size: 26px;
    }
}
</style>

try {

    // =========================
    // CORE PLATFORM STATISTICS
    // =========================
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    $totalDeposits = $pdo->query("SELECT COUNT(*) FROM deposits")->fetchColumn();

    $totalWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals")->fetchColumn();

    // Supported countries (adjust column name if needed: country / country_code)
    $totalCountries = $pdo->query("
        SELECT COUNT(DISTINCT country) 
        FROM region_settings
    ")->fetchColumn();

    $totalPaymentMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods")->fetchColumn();


    // =========================
    // OPTIONAL: RECENT ACTIVITY
    // =========================
    $recentDeposits = $pdo->query("
        SELECT d.*, u.full_name 
        FROM deposits d
        LEFT JOIN users u ON u.id = d.user_id
        ORDER BY d.created_at DESC
        LIMIT 5
    ")->fetchAll();

    $recentWithdrawals = $pdo->query("
        SELECT w.*, u.full_name 
        FROM withdrawals w
        LEFT JOIN users u ON u.id = w.user_id
        ORDER BY w.created_at DESC
        LIMIT 5
    ")->fetchAll();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="main p-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-chart-line"></i> Platform Overview
            </h2>
            <small class="text-muted">Real-time system statistics & activity</small>
        </div>
        <small class="text-muted">
            Last updated: <?= date('M d, Y - h:i A') ?>
        </small>
    </div>

    <p class="lead text-muted mb-4">
        Welcome back, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></strong>.
        Here’s what’s happening across your platform.
    </p>

    <!-- KPI DASHBOARD -->
    <div class="row g-4 mb-4">

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-lg border-start border-primary border-4">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h6 class="text-muted">Users</h6>
                    <h3 class="fw-bold"><?= number_format($totalUsers) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-lg border-start border-success border-4">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-2x text-success mb-2"></i>
                    <h6 class="text-muted">Deposits</h6>
                    <h3 class="fw-bold"><?= number_format($totalDeposits) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card shadow-lg border-start border-danger border-4">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x text-danger mb-2"></i>
                    <h6 class="text-muted">Withdrawals</h6>
                    <h3 class="fw-bold"><?= number_format($totalWithdrawals) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-3">
            <div class="card shadow-lg border-start border-info border-4">
                <div class="card-body text-center">
                    <i class="fas fa-globe fa-2x text-info mb-2"></i>
                    <h6 class="text-muted">Supported Countries</h6>
                    <h3 class="fw-bold"><?= number_format($totalCountries) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-3">
            <div class="card shadow-lg border-start border-warning border-4">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-2x text-warning mb-2"></i>
                    <h6 class="text-muted">Payment Methods</h6>
                    <h3 class="fw-bold"><?= number_format($totalPaymentMethods) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- RECENT ACTIVITY -->
    <div class="row g-4">

        <!-- Recent Deposits -->
        <div class="col-lg-6">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-down"></i> Recent Deposits</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentDeposits): ?>
                                    <?php foreach ($recentDeposits as $d): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($d['full_name'] ?? 'System') ?></td>
                                            <td><strong>₦<?= number_format($d['amount'], 2) ?></strong></td>
                                            <td><?= date('M d, Y', strtotime($d['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No deposits</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Withdrawals -->
        <div class="col-lg-6">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-up"></i> Recent Withdrawals</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentWithdrawals): ?>
                                    <?php foreach ($recentWithdrawals as $w): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($w['full_name'] ?? 'System') ?></td>
                                            <td><strong>₦<?= number_format($w['amount'], 2) ?></strong></td>
                                            <td><?= date('M d, Y', strtotime($w['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No withdrawals</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div class="mt-5 text-center">
        <h4 class="text-primary mb-4">Quick Actions</h4>

        <div class="row justify-content-center g-3">

            <div class="col-md-3">
                <a href="users.php" class="btn btn-outline-primary btn-lg w-100 py-4 shadow">
                    <i class="fas fa-users fa-2x mb-2"></i><br>
                    Manage Users
                </a>
            </div>

            <div class="col-md-3">
                <a href="deposits.php" class="btn btn-outline-success btn-lg w-100 py-4 shadow">
                    <i class="fas fa-wallet fa-2x mb-2"></i><br>
                    View Deposits
                </a>
            </div>

            <div class="col-md-3">
                <a href="withdrawals.php" class="btn btn-outline-danger btn-lg w-100 py-4 shadow">
                    <i class="fas fa-hand-holding-usd fa-2x mb-2"></i><br>
                    Manage Withdrawals
                </a>
            </div>

            <div class="col-md-3">
                <a href="settings.php" class="btn btn-outline-warning btn-lg w-100 py-4 shadow">
                    <i class="fas fa-cogs fa-2x mb-2"></i><br>
                    System Settings
                </a>
            </div>

        </div>
    </div>

</div>

<?php include './includes/admin_footer.php'; ?>
