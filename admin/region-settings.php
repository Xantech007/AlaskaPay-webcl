<?php
// admin/region-settings.php - Region Settings Management (2026)

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Region Settings";
include './includes/admin_header.php';

try {

    // =========================
    // KPI STATS
    // =========================
    $totalCountries = $pdo->query("SELECT COUNT(DISTINCT country) FROM region_settings")->fetchColumn();
    $avgFee = $pdo->query("SELECT COALESCE(AVG(fee),0) FROM region_settings")->fetchColumn();

    $totalSettings = $pdo->query("SELECT COUNT(*) FROM region_settings")->fetchColumn();

    // =========================
    // DATA LIST
    // =========================
    $regions = $pdo->query("
        SELECT *
        FROM region_settings
        ORDER BY country ASC
    ")->fetchAll();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<style>
.region-badge {
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

.fee-box {
    font-weight: bold;
}
</style>

<div class="main p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-globe"></i> Region Settings
            </h2>
            <small class="text-muted">Manage country fees and payment configurations</small>
        </div>
        <small class="text-muted">Updated: <?= date('M d, Y - h:i A') ?></small>
    </div>

    <!-- KPI -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card shadow-lg border-start border-primary border-4">
                <div class="card-body text-center">
                    <i class="fas fa-flag fa-2x text-primary mb-2"></i>
                    <h6>Total Countries</h6>
                    <h3><?= number_format($totalCountries) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-lg border-start border-success border-4">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-2x text-success mb-2"></i>
                    <h6>Average Fee</h6>
                    <h3><?= number_format($avgFee, 2) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-lg border-start border-info border-4">
                <div class="card-body text-center">
                    <i class="fas fa-sliders-h fa-2x text-info mb-2"></i>
                    <h6>Total Rules</h6>
                    <h3><?= number_format($totalSettings) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-lg">

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-map-marked-alt"></i> Region Configuration</h5>
            <input type="text" id="regionSearch" class="form-control form-control-sm w-25" placeholder="Search country...">
        </div>

        <div class="card-body p-0 table-responsive">

            <table class="table table-hover table-striped mb-0" id="regionTable">

                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Country</th>
                        <th>Fee</th>
                        <th>Method</th>
                        <th>Method Name</th>
                        <th>Method ID</th>
                        <th>Value</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($regions as $r): ?>
                    <tr>

                        <td><?= $r['id'] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($r['country']) ?></strong>
                        </td>

                        <td>
                            <span class="fee-box text-success">
                                <?= number_format($r['fee'], 2) ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-primary region-badge">
                                <?= htmlspecialchars($r['method']) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($r['method_name']) ?></td>

                        <td><?= htmlspecialchars($r['method_id']) ?></td>

                        <td class="small-text">
                            <?= htmlspecialchars($r['method_value']) ?>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($r['created_at'])) ?>
                        </td>

                        <td>

                            <a href="edit_region.php?id=<?= $r['id'] ?>"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="delete_region.php?id=<?= $r['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this region setting?')">
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
document.getElementById("regionSearch").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#regionTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
