<?php
// admin/users.php - Modern Users Management (2026)

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Manage Users";
include './includes/admin_header.php';

try {

    // =========================
    // USER STATISTICS
    // =========================
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $verifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 1")->fetchColumn();
    $unverifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 0")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
    $suspendedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'suspended'")->fetchColumn();

    // =========================
    // USERS LIST
    // =========================
    $users = $pdo->query("
        SELECT id, username, email, full_name, phone, balance, is_verified, status, country, state, created_at
        FROM users
        ORDER BY created_at DESC
        LIMIT 200
    ")->fetchAll();

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="main p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                <i class="fas fa-users"></i> Users Management
            </h2>
            <small class="text-muted">Manage all registered users</small>
        </div>
        <small class="text-muted">
            Last updated: <?= date('M d, Y - h:i A') ?>
        </small>
    </div>

    <!-- KPI CARDS -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-primary border-4">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h6 class="text-muted">Total Users</h6>
                    <h3 class="fw-bold"><?= number_format($totalUsers) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-success border-4">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h6 class="text-muted">Verified</h6>
                    <h3 class="fw-bold"><?= number_format($verifiedUsers) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-warning border-4">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h6 class="text-muted">Unverified</h6>
                    <h3 class="fw-bold"><?= number_format($unverifiedUsers) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-lg border-start border-danger border-4">
                <div class="card-body text-center">
                    <i class="fas fa-user-slash fa-2x text-danger mb-2"></i>
                    <h6 class="text-muted">Suspended</h6>
                    <h3 class="fw-bold"><?= number_format($suspendedUsers) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- USERS TABLE -->
    <div class="card shadow-lg">

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table"></i> All Users</h5>

            <input type="text" id="userSearch" class="form-control form-control-sm w-25" placeholder="Search users...">
        </div>

        <div class="card-body p-0 table-responsive">

            <table class="table table-hover table-striped mb-0" id="usersTable">

                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Verified</th>
                        <th>Location</th>
                        <th>Joined</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>

                        <td><?= htmlspecialchars($u['id']) ?></td>

                        <td>
                            <strong><?= htmlspecialchars($u['full_name'] ?? 'N/A') ?></strong><br>
                            <small class="text-muted">@<?= htmlspecialchars($u['username']) ?></small>
                        </td>

                        <td><?= htmlspecialchars($u['email']) ?></td>

                        <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>

                        <td>
                            <strong>₦<?= number_format($u['balance'], 2) ?></strong>
                        </td>

                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Suspended</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($u['is_verified']): ?>
                                <span class="badge bg-primary">Verified</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Unverified</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($u['country'] ?? '-') ?>
                            <small class="text-muted d-block"><?= htmlspecialchars($u['state'] ?? '-') ?></small>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($u['created_at'])) ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- SIMPLE SEARCH FILTER -->
<script>
document.getElementById("userSearch").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#usersTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
