<?php
// admin/deposits.php - Deposits Management (2026)

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Deposits Management";
include './includes/admin_header.php';

try {

    // =========================
    // KPI STATS
    // =========================
    $totalDeposits = $pdo->query("SELECT COUNT(*) FROM deposits")->fetchColumn();
    $pendingDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn();
    $approvedDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'approved'")->fetchColumn();
    $rejectedDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'rejected'")->fetchColumn();

    // =========================
    // DEPOSITS LIST
    // =========================
    $deposits = $pdo->query("
        SELECT d.*, u.full_name
        FROM deposits d
        LEFT JOIN users u ON u.id = d.user_id
        ORDER BY d.created_at DESC
        LIMIT 300
    ")->fetchAll();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<style>
.proof-img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
}

.proof-img:hover {
    transform: scale(1.1);
}

.table td, .table th {
    vertical-align: middle;
}
</style>

<div class="main p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-wallet"></i> Deposits Management
            </h2>
            <small class="text-muted">Approve or review all user deposits</small>
        </div>
        <small class="text-muted">Last updated: <?= date('M d, Y - h:i A') ?></small>
    </div>

    <!-- KPI -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-primary border-4">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-2x text-primary mb-2"></i>
                    <h6>Total Deposits</h6>
                    <h3><?= number_format($totalDeposits) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-warning border-4">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h6>Pending</h6>
                    <h3><?= number_format($pendingDeposits) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-success border-4">
                <div class="card-body text-center">
                    <i class="fas fa-check fa-2x text-success mb-2"></i>
                    <h6>Approved</h6>
                    <h3><?= number_format($approvedDeposits) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-danger border-4">
                <div class="card-body text-center">
                    <i class="fas fa-times fa-2x text-danger mb-2"></i>
                    <h6>Rejected</h6>
                    <h3><?= number_format($rejectedDeposits) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-lg">

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Deposits</h5>
            <input type="text" id="depositSearch" class="form-control form-control-sm w-25" placeholder="Search deposits...">
        </div>

        <div class="card-body p-0 table-responsive">

            <table class="table table-hover table-striped mb-0" id="depositsTable">

                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($deposits as $d): ?>
                    <tr>

                        <td><?= $d['id'] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($d['full_name'] ?? 'Unknown') ?></strong>
                            <small class="text-muted d-block">ID: <?= $d['user_id'] ?></small>
                        </td>

                        <td><?= htmlspecialchars($d['email']) ?></td>

                        <td>
                            <strong>₦<?= number_format($d['amount'], 2) ?></strong>
                        </td>

                        <td>
                            <a href="../<?= $d['proof_file'] ?>" target="_blank">
                                <img src="../<?= $d['proof_file'] ?>" class="proof-img">
                            </a>
                        </td>

                        <td>
                            <?php if ($d['status'] === 'approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif ($d['status'] === 'pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($d['created_at'])) ?>
                        </td>

                        <td>

                            <!-- Approve -->
                            <a href="deposit_action.php?id=<?= $d['id'] ?>&action=approve"
                               class="btn btn-sm btn-success">
                                Approve
                            </a>

                            <!-- Reject -->
                            <a href="deposit_action.php?id=<?= $d['id'] ?>&action=reject"
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
document.getElementById("depositSearch").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#depositsTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
