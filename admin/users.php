<?php
// admin/users.php - Modern Users Management (2026)

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require '../config/db.php';

if (isset($_POST['add_user'])) {

    $email = trim($_POST['email']);
    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    $stmt = $pdo->prepare("
        INSERT INTO users
        (
            email,
            password,
            full_name,
            phone,
            status,
            balance,
            is_verified
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            'active',
            0,
            0
        )
    ");

    $stmt->execute([
        $email,
        $passwordHash,
        $full_name,
        $phone
    ]);

    header("Location: users.php");
    exit();
}

if (isset($_POST['update_user'])) {

    $userId = (int)$_POST['user_id'];

    if (!empty($_POST['password'])) {

        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "
        UPDATE users SET
            email=?,
            password=?,
            full_name=?,
            phone=?,
            balance=?,
            is_verified=?,
            status=?,
            verified_method=?,
            verified_account_name=?,
            verified_account_id=?
        WHERE id=?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $_POST['email'],
            $hash,
            $_POST['full_name'],
            $_POST['phone'],
            $_POST['balance'],
            $_POST['is_verified'],
            $_POST['status'],
            $_POST['verified_method'],
            $_POST['verified_account_name'],
            $_POST['verified_account_id'],
            $userId
        ]);

    } else {

        $sql = "
        UPDATE users SET
            email=?,
            full_name=?,
            phone=?,
            balance=?,
            is_verified=?,
            status=?,
            verified_method=?,
            verified_account_name=?,
            verified_account_id=?
        WHERE id=?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $_POST['email'],
            $_POST['full_name'],
            $_POST['phone'],
            $_POST['balance'],
            $_POST['is_verified'],
            $_POST['status'],
            $_POST['verified_method'],
            $_POST['verified_account_name'],
            $_POST['verified_account_id'],
            $userId
        ]);
    }

    header("Location: users.php");
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
            email,
            full_name,
            phone,
            balance,
            is_verified,
            status,
            verified_method,
            verified_account_name,
            verified_account_id,
            created_at
        FROM users
        ORDER BY created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

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
            <button
                class="btn btn-success"
                data-bs-toggle="modal"
                data-bs-target="#addUserModal">
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
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Balance</th>
                    <th>Verified</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th>Account Name</th>
                    <th>Account ID</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                    
                        <td><?= $u['id'] ?></td>
                    
                        <td><?= htmlspecialchars($u['email']) ?></td>
                    
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                    
                        <td><?= htmlspecialchars($u['phone']) ?></td>
                    
                        <td>₦<?= number_format($u['balance'], 2) ?></td>
                    
                        <td>
                            <?php
                            switch ($u['is_verified']) {
                                case 2:
                                    echo '<span class="badge bg-success">Verified</span>';
                                    break;
                                case 1:
                                    echo '<span class="badge bg-warning">Pending</span>';
                                    break;
                                default:
                                    echo '<span class="badge bg-secondary">Not Verified</span>';
                            }
                            ?>
                        </td>
                    
                        <td>
                            <span class="badge bg-<?= $u['status'] == 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                    
                        <td><?= htmlspecialchars($u['verified_method'] ?? '-') ?></td>
                    
                        <td><?= htmlspecialchars($u['verified_account_name'] ?? '-') ?></td>
                    
                        <td><?= htmlspecialchars($u['verified_account_id'] ?? '-') ?></td>
                    
                        <td>
                            <button
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editUser<?= $u['id'] ?>">
                                Edit
                            </button>
                        </td>
                    
                    </tr>

                    <div class="modal fade" id="editUser<?= $u['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                
                            <form method="POST">
                
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                
                                <div class="modal-header">
                                    <h5>Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                
                                <div class="modal-body">
                
                                    <div class="row">
                
                                        <div class="col-md-6 mb-3">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="<?= htmlspecialchars($u['email']) ?>">
                                        </div>
                
                                        <div class="col-md-6 mb-3">
                                            <label>New Password</label>
                                            <input type="password" class="form-control" name="password">
                                            <small>Leave blank to keep current password</small>
                                        </div>
                
                                        <div class="col-md-6 mb-3">
                                            <label>Full Name</label>
                                            <input type="text" class="form-control" name="full_name"
                                                value="<?= htmlspecialchars($u['full_name']) ?>">
                                        </div>
                
                                        <div class="col-md-6 mb-3">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone"
                                                value="<?= htmlspecialchars($u['phone']) ?>">
                                        </div>
                
                                        <div class="col-md-4 mb-3">
                                            <label>Balance</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="balance" value="<?= $u['balance'] ?>">
                                        </div>
                
                                        <div class="col-md-4 mb-3">
                                            <label>Verification</label>
                                            <select class="form-select" name="is_verified">
                                                <option value="0" <?= $u['is_verified']==0?'selected':'' ?>>Not Verified</option>
                                                <option value="1" <?= $u['is_verified']==1?'selected':'' ?>>Pending</option>
                                                <option value="2" <?= $u['is_verified']==2?'selected':'' ?>>Verified</option>
                                            </select>
                                        </div>
                
                                        <div class="col-md-4 mb-3">
                                            <label>Status</label>
                                            <select class="form-select" name="status">
                                                <option value="active" <?= $u['status']=='active'?'selected':'' ?>>Active</option>
                                                <option value="suspended" <?= $u['status']=='suspended'?'selected':'' ?>>Suspended</option>
                                            </select>
                                        </div>
                
                                        <div class="col-md-4 mb-3">
                                            <label>Verified Method</label>
                                            <input type="text" class="form-control"
                                                name="verified_method"
                                                value="<?= htmlspecialchars($u['verified_method']) ?>">
                                        </div>
                
                                        <div class="col-md-4 mb-3">
                                            <label>Verified Account Name</label>
                                            <input type="text" class="form-control"
                                                name="verified_account_name"
                                                value="<?= htmlspecialchars($u['verified_account_name']) ?>">
                                        </div>
                
                                        <div class="col-md-4 mb-3">
                                            <label>Verified Account ID</label>
                                            <input type="text" class="form-control"
                                                name="verified_account_id"
                                                value="<?= htmlspecialchars($u['verified_account_id']) ?>">
                                        </div>
                
                                    </div>
                
                                </div>
                
                                <div class="modal-footer">
                                    <button class="btn btn-primary" name="update_user">
                                        Save Changes
                                    </button>
                                </div>
                
                            </form>
                
                        </div>
                    </div>
                </div>
                    
                <?php endforeach; ?>
                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">
                    <h5>Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" name="add_user">
                        Create User
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
