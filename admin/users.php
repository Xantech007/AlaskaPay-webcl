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
        SELECT
            id,
            username,
            email,
            password,
            verified,
            full_name,
            phone,
            balance,
            is_verified,
            status,
            verified_method,
            verified_account_name,
            verified_account_id,
            method,
            method_name,
            method_id,
            country,
            state,
            created_at
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
    
        <div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
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
                    <th>Email</th>
                    <th>Password</th>
                    <th>Verified</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Balance</th>
                    <th>is_verified</th>
                    <th>Status</th>
                    <th>Verified Method</th>
                    <th>Verified Account Name</th>
                    <th>Verified Account ID</th>
                    <th>Method</th>
                    <th>Method Name</th>
                    <th>Method ID</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                
                    <td><?= $u['id'] ?></td>
                
                    <td><?= htmlspecialchars($u['email']) ?></td>
                
                    <td>
                        <small class="text-danger">
                            <?= htmlspecialchars($u['password']) ?>
                        </small>
                    </td>
                
                    <td><?= htmlspecialchars($u['verified']) ?></td>
                
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                
                    <td><?= htmlspecialchars($u['phone']) ?></td>
                
                    <td>₦<?= number_format($u['balance'], 2) ?></td>
                
                    <td>
                        <?php
                        switch($u['is_verified']){
                            case 0:
                                echo '<span class="badge bg-danger">Not Verified</span>';
                                break;
                            case 1:
                                echo '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 2:
                                echo '<span class="badge bg-success">Verified</span>';
                                break;
                        }
                        ?>
                    </td>
                
                    <td><?= htmlspecialchars($u['status']) ?></td>
                
                    <td><?= htmlspecialchars($u['verified_method']) ?></td>
                
                    <td><?= htmlspecialchars($u['verified_account_name']) ?></td>
                
                    <td><?= htmlspecialchars($u['verified_account_id']) ?></td>
                
                    <td><?= htmlspecialchars($u['method']) ?></td>
                
                    <td><?= htmlspecialchars($u['method_name']) ?></td>
                
                    <td><?= htmlspecialchars($u['method_id']) ?></td>
                
                    <td>
                        <button
                            class="btn btn-primary btn-sm editUserBtn"
                            data-id="<?= $u['id'] ?>"
                            data-email="<?= htmlspecialchars($u['email']) ?>"
                            data-full_name="<?= htmlspecialchars($u['full_name']) ?>"
                            data-phone="<?= htmlspecialchars($u['phone']) ?>"
                            data-balance="<?= $u['balance'] ?>"
                            data-status="<?= htmlspecialchars($u['status']) ?>"
                            data-is_verified="<?= $u['is_verified'] ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                
                        <a href="delete_user.php?id=<?= $u['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this user?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                
                </tr>
                <?php endforeach; ?>
                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="modal fade" id="addUserModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form action="save_user.php" method="POST">

                <div class="modal-header">
                    <h5>Add New User</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Password</label>
                            <input type="text" name="password" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Balance</label>
                            <input type="number" step="0.01" name="balance" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>is_verified</label>
                            <select name="is_verified" class="form-control">
                                <option value="0">Not Verified</option>
                                <option value="1">Pending</option>
                                <option value="2">Verified</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Status</label>
                            <input type="text" name="status" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Verified Method</label>
                            <input type="text" name="verified_method" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Verified Account Name</label>
                            <input type="text" name="verified_account_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Verified Account ID</label>
                            <input type="text" name="verified_account_id" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Method</label>
                            <input type="text" name="method" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Method Name</label>
                            <input type="text" name="method_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Method ID</label>
                            <input type="text" name="method_id" class="form-control">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">
                        Save User
                    </button>
                </div>

            </form>

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
