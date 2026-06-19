<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Manage Users";
include './includes/admin_header.php';

try {

    // STATS
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $verifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 2")->fetchColumn();
    $unverifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 0")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
    $suspendedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'suspended'")->fetchColumn();

    // USERS
    $users = $pdo->query("
        SELECT *
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

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus"></i> Add New User
        </button>
    </div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Users</h6>
                <h3><?= number_format($totalUsers) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-success border-4">
            <div class="card-body text-center">
                <h6>Verified</h6>
                <h3><?= number_format($verifiedUsers) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-warning border-4">
            <div class="card-body text-center">
                <h6>Unverified</h6>
                <h3><?= number_format($unverifiedUsers) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-danger border-4">
            <div class="card-body text-center">
                <h6>Suspended</h6>
                <h3><?= number_format($suspendedUsers) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="card shadow-lg">
    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">All Users</h5>
        <input type="text" id="userSearch" class="form-control form-control-sm w-25" placeholder="Search...">
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0" id="usersTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>

                    <td>
                        <strong><?= htmlspecialchars($u['full_name'] ?? '-') ?></strong><br>
                        <small>@<?= htmlspecialchars($u['username']) ?></small>
                    </td>

                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>

                    <td>₦<?= number_format($u['balance'], 2) ?></td>

                    <td>
                        <span class="badge bg-<?= $u['status']=='active'?'success':'danger' ?>">
                            <?= ucfirst($u['status']) ?>
                        </span>
                    </td>

                    <td>
                        <?php
                            $verifyBadge =
                                $u['is_verified'] == 2 ? 'success' :
                                ($u['is_verified'] == 1 ? 'warning text-dark' : 'danger');
                    
                            $verifyLabel =
                                $u['is_verified'] == 2 ? 'Verified' :
                                ($u['is_verified'] == 1 ? 'Pending' : 'Not Verified');
                        ?>
                    
                        <span class="badge bg-<?= $verifyBadge ?>">
                            <?= $verifyLabel ?>
                        </span>
                    </td>

                    <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>

                    <td>
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editUser<?= $u['id'] ?>">
                            Edit
                        </button>

                        <a href="delete-user?id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete user?')">
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

<!-- ================= ADD USER MODAL ================= -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <form method="POST" action="add-user" class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input class="form-control mb-2" name="email" placeholder="Email" required>
        <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>
        <input class="form-control mb-2" name="full_name" placeholder="Full Name">
        <input class="form-control mb-2" name="phone" placeholder="Phone">
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary">Create</button>
      </div>

    </form>
  </div>
</div>

<!-- ================= EDIT MODALS ================= -->
<?php foreach ($users as $u): ?>
<div class="modal fade" id="editUser<?= $u['id'] ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <form method="POST" action="edit-user" class="modal-content">

      <input type="hidden" name="id" value="<?= $u['id'] ?>">

      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- EMAIL -->
        <label class="form-label">Email</label>
        <input class="form-control mb-3" name="email"
               value="<?= htmlspecialchars($u['email'] ?? '') ?>">

        <!-- PASSWORD -->
        <label class="form-label">Password</label>
        <input class="form-control mb-3" name="password" type="password"
               placeholder="Leave blank to keep current password">

        <!-- FULL NAME -->
        <label class="form-label">Full Name</label>
        <input class="form-control mb-3" name="full_name"
               value="<?= htmlspecialchars($u['full_name'] ?? '') ?>">

        <!-- PHONE -->
        <label class="form-label">Phone</label>
        <input class="form-control mb-3" name="phone"
               value="<?= htmlspecialchars($u['phone'] ?? '') ?>">

        <!-- BALANCE -->
        <label class="form-label">Balance</label>
        <input class="form-control mb-3" name="balance"
               value="<?= htmlspecialchars($u['balance'] ?? 0) ?>">

        <!-- VERIFICATION STATUS -->
        <label class="form-label">Verification Status</label>
        <select class="form-control mb-3" name="is_verified">
            <option value="0" <?= ($u['is_verified'] ?? 0)==0?'selected':'' ?>>Not Verified</option>
            <option value="1" <?= ($u['is_verified'] ?? 0)==1?'selected':'' ?>>Pending</option>
            <option value="2" <?= ($u['is_verified'] ?? 0)==2?'selected':'' ?>>Verified</option>
        </select>

        <!-- STATUS -->
        <label class="form-label">Account Status</label>
        <select class="form-control mb-3" name="status">
            <option value="active" <?= ($u['status'] ?? '')=='active'?'selected':'' ?>>Active</option>
            <option value="suspended" <?= ($u['status'] ?? '')=='suspended'?'selected':'' ?>>Suspended</option>
        </select>

        <!-- VERIFIED METHOD -->
        <label class="form-label">Verification Method</label>
        <input class="form-control mb-3" name="verified_method"
               value="<?= htmlspecialchars($u['verified_method'] ?? '') ?>">

        <!-- VERIFIED ACCOUNT NAME -->
        <label class="form-label">Verified Account Name</label>
        <input class="form-control mb-3" name="verified_account_name"
               value="<?= htmlspecialchars($u['verified_account_name'] ?? '') ?>">

        <!-- VERIFIED ACCOUNT ID -->
        <label class="form-label">Verified Account ID</label>
        <input class="form-control mb-3" name="verified_account_id"
               value="<?= htmlspecialchars($u['verified_account_id'] ?? '') ?>">

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
            Cancel
        </button>
        <button class="btn btn-success">
            Update User
        </button>
      </div>

    </form>
  </div>
</div>
<?php endforeach; ?>

<!-- SEARCH -->
<script>
document.getElementById("userSearch").addEventListener("input", function () {
    let val = this.value.toLowerCase();
    document.querySelectorAll("#usersTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
