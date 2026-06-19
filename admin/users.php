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
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php

try {

    // =========================
    // USER STATISTICS
    // =========================
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $verifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 2")->fetchColumn();
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
            <h2 class="fw-bold text-primary mb-0">
                <i class="fas fa-users"></i> Users Management
            </h2>
            <small class="text-muted">Manage all registered users</small>
        </div>
    
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted">
                Last updated: <?= date('M d, Y - h:i A') ?>
            </small>
    
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus"></i> Add New User
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
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Verified</th>
                        <th>Location</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
            
                        <td><?= htmlspecialchars($u['id']) ?></td>
            
                        <td>
                            <strong><?= htmlspecialchars($u['full_name'] ?? 'N/A') ?></strong><br>
                            <small class="text-muted">@<?= htmlspecialchars($u['username'] ?? '-') ?></small>
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
                            <?php if ((int)$u['is_verified'] === 2): ?>
                                <span class="badge bg-primary">Verified</span>
                            <?php elseif ((int)$u['is_verified'] === 1): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not Verified</span>
                            <?php endif; ?>
                        </td>
            
                        <td>
                            <?= htmlspecialchars($u['country'] ?? '-') ?>
                            <small class="text-muted d-block"><?= htmlspecialchars($u['state'] ?? '-') ?></small>
                        </td>
            
                        <td>
                            <?= date('M d, Y', strtotime($u['created_at'])) ?>
                        </td>
            
                        <td>
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editUser<?= $u['id'] ?>">
                                Edit
                            </button>
            
                            <a href="delete-user.php?id=<?= $u['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this user?')">
                                Delete
                            </a>
                        </td>
            
                    </tr>
                <?php endforeach; ?>
                </tbody>
            
            </table>
    
            <?php foreach ($users as $u): ?>
            
            <div class="modal fade" id="editUser<?= $u['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <form method="POST" action="edit-user.php" class="modal-content">
            
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
            
                  <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
            
                  <div class="modal-body">
                    
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control mb-2"
                               value="<?= htmlspecialchars($u['email']) ?>" required>
                    
                        <label class="form-label">Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control mb-2"
                               placeholder="New password">
                    
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control mb-2"
                               value="<?= htmlspecialchars($u['full_name']) ?>">
                    
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control mb-2"
                               value="<?= htmlspecialchars($u['phone']) ?>">
                    
                        <label class="form-label">Balance</label>
                        <input type="number" name="balance" class="form-control mb-2"
                               value="<?= $u['balance'] ?>">
                    
                        <label class="form-label">Verification Status</label>
                        <select name="is_verified" class="form-control mb-2">
                            <option value="0" <?= $u['is_verified']==0?'selected':'' ?>>Not Verified</option>
                            <option value="1" <?= $u['is_verified']==1?'selected':'' ?>>Pending</option>
                            <option value="2" <?= $u['is_verified']==2?'selected':'' ?>>Verified</option>
                        </select>
                    
                        <label class="form-label">Account Status</label>
                        <input type="text" name="status" class="form-control mb-2"
                        <select name="status" class="form-control mb-2">
                            <option value="active" <?= $u['status']==active?'selected':'' ?>>Active</option>
                            <option value="suspended" <?= $u['status']==suspended?'selected':'' ?>>Suspended</option>
                        </select>
                    
                        <label class="form-label">Verification Method</label>
                        <input type="text" name="verified_method" class="form-control mb-2"
                               value="<?= htmlspecialchars($u['verified_method'] ?? '') ?>">
                    
                        <label class="form-label">Verified Account Name</label>
                        <input type="text" name="verified_account_name" class="form-control mb-2"
                               value="<?= htmlspecialchars($u['verified_account_name'] ?? '') ?>">
                    
                        <label class="form-label">Verified Account ID</label>
                        <input type="text" name="verified_account_id" class="form-control mb-2"
                               value="<?= htmlspecialchars($u['verified_account_id'] ?? '') ?>">
                    
                  </div>
            
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Update</button>
                  </div>
            
                </form>
              </div>
            </div>
            
            <?php endforeach; ?>

        </div>

    </div>

</div>

<div class="modal fade" id="addUserModal">
  <div class="modal-dialog">
    <form method="POST" action="add-user.php" class="modal-content">

        <div class="modal-header">
        <h5 class="modal-title">Add New User</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
        
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control mb-2" required>
        
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control mb-2" required>
        
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control mb-2" required>
        
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control mb-2">
        
        </div>

        <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary">Create User</button>
        </div>

    </form>
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
