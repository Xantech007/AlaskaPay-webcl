<?php
// admin/payment-settings.php - Payment Methods Management (2026)

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Payment Settings";
include './includes/admin_header.php';

try {

    // =========================
    // KPI STATS
    // =========================
    $totalMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods")->fetchColumn();
    $bankMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE type = 'bank'")->fetchColumn();
    $momoMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE type = 'momo'")->fetchColumn();
    $cryptoMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE type = 'crypto'")->fetchColumn();

    // =========================
    // DATA LIST
    // =========================
    $methods = $pdo->query("
        SELECT *
        FROM payment_methods
        ORDER BY country ASC, type ASC
    ")->fetchAll();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<style>
.method-badge {
    font-size: 12px;
    padding: 5px 10px;
}

.table td, .table th {
    vertical-align: middle;
}

.small-text {
    font-size: 13px;
    color: #666;
}
</style>

<div class="main p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-credit-card"></i> Payment Settings
            </h2>
            <small class="text-muted">Manage supported payment methods by country</small>
        </div>
        <small class="text-muted">Updated: <?= date('M d, Y - h:i A') ?></small>
    </div>

    <!-- KPI -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-primary border-4">
                <div class="card-body text-center">
                    <i class="fas fa-list fa-2x text-primary mb-2"></i>
                    <h6>Total Methods</h6>
                    <h3><?= number_format($totalMethods) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-success border-4">
                <div class="card-body text-center">
                    <i class="fas fa-university fa-2x text-success mb-2"></i>
                    <h6>Bank</h6>
                    <h3><?= number_format($bankMethods) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-warning border-4">
                <div class="card-body text-center">
                    <i class="fas fa-mobile-alt fa-2x text-warning mb-2"></i>
                    <h6>Mobile Money</h6>
                    <h3><?= number_format($momoMethods) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-info border-4">
                <div class="card-body text-center">
                    <i class="fab fa-bitcoin fa-2x text-info mb-2"></i>
                    <h6>Crypto</h6>
                    <h3><?= number_format($cryptoMethods) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-lg">

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-cogs"></i> Payment Methods</h5>
            <input type="text" id="methodSearch" class="form-control form-control-sm w-25" placeholder="Search methods...">
        </div>

        <div class="card-body p-0 table-responsive">

            <table class="table table-hover table-striped mb-0" id="methodsTable">

                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Country</th>
                        <th>Type</th>
                        <th>Method</th>
                        <th>Name</th>
                        <th>Account / Address</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($methods as $m): ?>
                    <tr>

                        <td><?= $m['id'] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($m['country']) ?></strong>
                        </td>

                        <td>
                            <?php if ($m['type'] === 'bank'): ?>
                                <span class="badge bg-primary method-badge">Bank</span>
                            <?php elseif ($m['type'] === 'momo'): ?>
                                <span class="badge bg-warning text-dark method-badge">Mobile Money</span>
                            <?php else: ?>
                                <span class="badge bg-info method-badge">Crypto</span>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($m['method']) ?></td>

                        <td><?= htmlspecialchars($m['method_name']) ?></td>

                        <td class="small-text">
                            <?= htmlspecialchars($m['method_id']) ?>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($m['created_at'])) ?>
                        </td>

                        <td>

                            <a href="edit_payment_method.php?id=<?= $m['id'] ?>"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="delete_payment_method.php?id=<?= $m['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this payment method?')">
                                Delete
                            </a>

                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<!-- SEARCH -->
<script>
document.getElementById("methodSearch").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#methodsTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
