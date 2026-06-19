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

    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $verifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 2")->fetchColumn();
    $unverifiedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 0")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
    $suspendedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'suspended'")->fetchColumn();

    $users = $pdo->query("
        SELECT id, email, full_name, phone, balance, is_verified, status,
               verified_method, verified_account_name, verified_account_id, created_at
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
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="fw-bold text-primary">Users Management</h2>
    </div>

    <!-- 🔥 ADD NEW USER BUTTON -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        + Add New User
    </button>
</div>

<!-- USERS TABLE -->
<div class="card shadow-lg">

    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">All Users</h5>
        <input type="text" id="userSearch" class="form-control form-control-sm w-25" placeholder="Search...">
    </div>

    <div class="card-body p-0 table-responsive">

        <table class="table table-striped" id="usersTable">
            <thead class="table-primary">
                <tr>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Balance</th>
                    <th>Verified</th>
                    <th>Status</th>
                    <th>Verified Method</th>
                    <th>Account Name</th>
                    <th>Account ID</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['phone']) ?></td>
                    <td>₦<?= number_format($u['balance'], 2) ?></td>

                    <td>
                        <?php if ($u['is_verified'] == 2): ?>
                            <span class="badge bg-success">Verified</span>
                        <?php elseif ($u['is_verified'] == 1): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Not Verified</span>
                        <?php endif; ?>
                    </td>

                    <td><?= $u['status'] ?></td>
                    <td><?= htmlspecialchars($u['verified_method'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['verified_account_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['verified_account_id'] ?? '-') ?></td>

                    <td>
                        <button class="btn btn-sm btn-info"
                            onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                            Edit
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>

    </div>
</div>
</div>

<!-- ================= ADD USER MODAL ================= -->
<div class="modal fade" id="addUserModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="users_backend.php" method="POST">
        <div class="modal-header">
          <h5>Add User</h5>
        </div>

        <div class="modal-body">

            <input type="hidden" name="action" value="add_user">

            <input name="email" class="form-control mb-2" placeholder="Email" required>
            <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
            <input name="full_name" class="form-control mb-2" placeholder="Full Name" required>
            <input name="phone" class="form-control mb-2" placeholder="Phone" required>

        </div>

        <div class="modal-footer">
          <button class="btn btn-primary">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ================= EDIT USER MODAL ================= -->
<div class="modal fade" id="editUserModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form action="users_backend.php" method="POST">
        <div class="modal-header">
          <h5>Edit User</h5>
        </div>

        <div class="modal-body">

            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="id" id="edit_id">

            <input name="email" id="edit_email" class="form-control mb-2">
            <input name="password" class="form-control mb-2" placeholder="New Password (optional)">

            <input name="full_name" id="edit_full_name" class="form-control mb-2">
            <input name="phone" id="edit_phone" class="form-control mb-2">

            <input name="balance" id="edit_balance" class="form-control mb-2">

            <select name="is_verified" id="edit_is_verified" class="form-control mb-2">
                <option value="0">Not Verified</option>
                <option value="1">Pending</option>
                <option value="2">Verified</option>
            </select>

            <input name="status" id="edit_status" class="form-control mb-2">

            <input name="verified_method" id="edit_verified_method" class="form-control mb-2">
            <input name="verified_account_name" id="edit_verified_account_name" class="form-control mb-2">
            <input name="verified_account_id" id="edit_verified_account_id" class="form-control mb-2">

        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Update</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
function editUser(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_full_name').value = data.full_name;
    document.getElementById('edit_phone').value = data.phone;
    document.getElementById('edit_balance').value = data.balance;
    document.getElementById('edit_is_verified').value = data.is_verified;
    document.getElementById('edit_status').value = data.status;
    document.getElementById('edit_verified_method').value = data.verified_method;
    document.getElementById('edit_verified_account_name').value = data.verified_account_name;
    document.getElementById('edit_verified_account_id').value = data.verified_account_id;

    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
</script>

<?php include './includes/admin_footer.php'; ?>
