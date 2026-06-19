<?php
// admin/dashboard.php - Modern Admin Dashboard (Enhanced 2026)

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
include './includes/admin_header.php';

try {

    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalDeposits = $pdo->query("SELECT COUNT(*) FROM deposits")->fetchColumn();
    $totalWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals")->fetchColumn();

    $totalCountries = $pdo->query("
        SELECT COUNT(DISTINCT country) 
        FROM region_settings
    ")->fetchColumn();

    $totalPaymentMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods")->fetchColumn();

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

<style>
/* =========================
   FIXED QUICK ACTION BUTTONS
   ========================= */

.quick-actions .action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    padding: 28px 15px;
    border-radius: 16px;

    font-weight: 600;
    text-decoration: none;

    transition: all 0.25s ease;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);

    color: #fff !important;
}

.quick-actions .action-btn i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #fff !important;
}

/* Hover animation */
.quick-actions .action-btn:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.18);
}

/* Color themes */
.action-primary { background: #0d6efd; }
.action-success { background: #198754; }
.action-danger  { background: #dc3545; }
.action-warning { background: #ffc107; color:#000 !important; }
.action-warning i { color:#000 !important; }
</style>

<div class="main p-4">

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
                    <h6 class="text-muted">Countries</h6>
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

        <div class="col-lg-6">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-down"></i> Recent Deposits</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentDeposits as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['full_name'] ?? 'System') ?></td>
                                <td><strong>₦<?= number_format($d['amount'], 2) ?></strong></td>
                                <td><?= date('M d, Y', strtotime($d['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-up"></i> Recent Withdrawals</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentWithdrawals as $w): ?>
                            <tr>
                                <td><?= htmlspecialchars($w['full_name'] ?? 'System') ?></td>
                                <td><strong>₦<?= number_format($w['amount'], 2) ?></strong></td>
                                <td><?= date('M d, Y', strtotime($w['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS (FIXED) -->
    <div class="mt-5 text-center quick-actions">

        <h4 class="text-primary mb-4">Quick Actions</h4>

        <div class="row g-3 justify-content-center">

            <div class="col-md-3">
                <a href="users.php" class="action-btn action-primary">
                    <i class="fas fa-users"></i>
                    Manage Users
                </a>
            </div>

            <div class="col-md-3">
                <a href="deposits.php" class="action-btn action-success">
                    <i class="fas fa-wallet"></i>
                    View Deposits
                </a>
            </div>

            <div class="col-md-3">
                <a href="withdrawals.php" class="action-btn action-danger">
                    <i class="fas fa-hand-holding-usd"></i>
                    Withdrawals
                </a>
            </div>

            <div class="col-md-3">
                <a href="settings.php" class="action-btn action-warning">
                    <i class="fas fa-cogs"></i>
                    Settings
                </a>
            </div>

        </div>
    </div>

</div>

<?php include './includes/admin_footer.php'; ?>
