<?php
// admin/withdrawals.php - Withdrawals Management (2026)

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Withdrawals Management";
include './includes/admin_header.php';

try {

    // =========================
    // KPI STATS
    // =========================
    $totalWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals")->fetchColumn();
    $pendingWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn();
    $approvedWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'approved'")->fetchColumn();
    $rejectedWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'rejected'")->fetchColumn();

    // =========================
    // WITHDRAWALS LIST
    // =========================
    $withdrawals = $pdo->query("
        SELECT w.*, u.full_name, u.email
        FROM withdrawals w
        LEFT JOIN users u ON u.id = w.user_id
        ORDER BY w.created_at DESC
        LIMIT 300
    ")->fetchAll();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<style>
.table td, .table th {
    vertical-align: middle;
}

.account-box {
    font-size: 13px;
    line-height: 1.3;
}

.badge-status {
    font-size: 12px;
    padding: 6px 10px;
}
</style>

<div class="main p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-hand-holding-dollar"></i> Withdrawals Management
            </h2>
            <small class="text-muted">Approve or reject user withdrawal requests</small>
        </div>
        <small class="text-muted">Last updated: <?= date('M d, Y - h:i A') ?></small>
    </div>

    <!-- KPI -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-primary border-4">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-2x text-primary mb-2"></i>
                    <h6>Total</h6>
                    <h3><?= number_format($totalWithdrawals) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-warning border-4">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h6>Pending</h6>
                    <h3><?= number_format($pendingWithdrawals) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-success border-4">
                <div class="card-body text-center">
                    <i class="fas fa-check fa-2x text-success mb-2"></i>
                    <h6>Approved</h6>
                    <h3><?= number_format($approvedWithdrawals) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-danger border-4">
                <div class="card-body text-center">
                    <i class="fas fa-times fa-2x text-danger mb-2"></i>
                    <h6>Rejected</h6>
                    <h3><?= number_format($rejectedWithdrawals) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-lg">

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Withdrawals</h5>
            <input type="text" id="withdrawSearch" class="form-control form-control-sm w-25" placeholder="Search withdrawals...">
        </div>

        <div class="card-body p-0 table-responsive">

            <table class="table table-hover table-striped mb-0" id="withdrawTable">

                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Account Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($withdrawals as $w): ?>
                    <tr>

                        <td><?= $w['id'] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($w['full_name'] ?? 'Unknown') ?></strong>
                            <small class="text-muted d-block"><?= htmlspecialchars($w['email'] ?? '') ?></small>
                        </td>

                        <td>
                            <strong>₦<?= number_format($w['amount'], 2) ?></strong>
                        </td>

                        <td>
                            <span class="badge bg-info text-dark">
                                <?= htmlspecialchars($w['method']) ?>
                            </span>
                        </td>

                        <td class="account-box">
                            <strong><?= htmlspecialchars($w['account_name']) ?></strong><br>
                            <small><?= htmlspecialchars($w['account_id']) ?></small>
                        </td>

                        <td>
                            <?php if ($w['status'] === 'approved'): ?>
                                <span class="badge bg-success badge-status">Approved</span>
                            <?php elseif ($w['status'] === 'pending'): ?>
                                <span class="badge bg-warning text-dark badge-status">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger badge-status">Rejected</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($w['created_at'])) ?>
                        </td>

                        <td>

                            <!-- Approve -->
                            <a href="withdrawal_action.php?id=<?= $w['id'] ?>&action=approve"
                               class="btn btn-sm btn-success">
                                Approve
                            </a>

                            <!-- Reject -->
                            <a href="withdrawal_action.php?id=<?= $w['id'] ?>&action=reject"
                               class="btn btn-sm btn-danger">
                                Reject
                            </a>

                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<!-- SEARCH SCRIPT -->
<script>
document.getElementById("withdrawSearch").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#withdrawTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
