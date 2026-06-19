<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Manage Withdrawals";
include './includes/admin_header.php';

try {

    // STATS
    $totalWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals")->fetchColumn();
    $pendingWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status='pending'")->fetchColumn();
    $approvedWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status='approved'")->fetchColumn();
    $rejectedWithdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status='rejected'")->fetchColumn();

    $withdrawals = $pdo->query("
        SELECT *
        FROM withdrawals
        ORDER BY created_at DESC
        LIMIT 500
    ")->fetchAll();

} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<div class="main p-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-primary mb-0">
            <i class="fas fa-wallet"></i> Withdrawals Management
        </h2>
        <small class="text-muted">Manage all withdrawal requests</small>
    </div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Withdrawals</h6>
                <h3><?= number_format($totalWithdrawals) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-warning border-4">
            <div class="card-body text-center">
                <h6>Pending</h6>
                <h3><?= number_format($pendingWithdrawals) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-success border-4">
            <div class="card-body text-center">
                <h6>Approved</h6>
                <h3><?= number_format($approvedWithdrawals) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-danger border-4">
            <div class="card-body text-center">
                <h6>Rejected</h6>
                <h3><?= number_format($rejectedWithdrawals) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="card shadow-lg">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Withdrawals</h5>

        <input type="text"
               id="withdrawalSearch"
               class="form-control form-control-sm w-25"
               placeholder="Search withdrawals">
    </div>

    <div class="card-body p-0 table-responsive">

        <table class="table table-hover mb-0" id="withdrawalsTable">

            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Account Name</th>
                    <th>Account ID</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($withdrawals as $w): ?>

                <tr>

                    <td><?= $w['id'] ?></td>

                    <td><?= $w['user_id'] ?></td>

                    <td>
                        <strong>₦<?= number_format($w['amount'], 2) ?></strong>
                    </td>

                    <td><?= htmlspecialchars($w['method']) ?></td>

                    <td><?= htmlspecialchars($w['account_name']) ?></td>

                    <td><?= htmlspecialchars($w['account_id']) ?></td>

                    <td>
                        <?php
                        $badge =
                            $w['status'] == 'approved' ? 'success' :
                            ($w['status'] == 'rejected' ? 'danger' : 'warning');
                        ?>

                        <span class="badge bg-<?= $badge ?>">
                            <?= ucfirst($w['status']) ?>
                        </span>
                    </td>

                    <td>
                        <?= date('d M Y h:i A', strtotime($w['created_at'])) ?>
                    </td>

                    <td>

                        <button class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editWithdrawal<?= $w['id'] ?>">
                            Update
                        </button>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>
</div>

</div>

<!-- STATUS MODALS -->
<?php foreach ($withdrawals as $w): ?>

<div class="modal fade"
     id="editWithdrawal<?= $w['id'] ?>"
     tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
              action="update-withdrawal-status"
              class="modal-content">

            <input type="hidden"
                   name="id"
                   value="<?= $w['id'] ?>">

            <div class="modal-header">
                <h5 class="modal-title">
                    Withdrawal #<?= $w['id'] ?>
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p><strong>User ID:</strong> <?= $w['user_id'] ?></p>

                <p><strong>Amount:</strong>
                    ₦<?= number_format($w['amount'],2) ?>
                </p>

                <p><strong>Method:</strong>
                    <?= htmlspecialchars($w['method']) ?>
                </p>

                <p><strong>Account Name:</strong>
                    <?= htmlspecialchars($w['account_name']) ?>
                </p>

                <p><strong>Account ID:</strong>
                    <?= htmlspecialchars($w['account_id']) ?>
                </p>

                <label class="form-label">
                    Withdrawal Status
                </label>

                <select name="status" class="form-control">

                    <option value="pending"
                        <?= $w['status']=='pending'?'selected':'' ?>>
                        Pending
                    </option>

                    <option value="approved"
                        <?= $w['status']=='approved'?'selected':'' ?>>
                        Approved
                    </option>

                    <option value="rejected"
                        <?= $w['status']=='rejected'?'selected':'' ?>>
                        Rejected
                    </option>

                </select>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Close
                </button>

                <button class="btn btn-success">
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

<?php endforeach; ?>

<script>
document.getElementById("withdrawalSearch").addEventListener("input", function () {

    let value = this.value.toLowerCase();

    document.querySelectorAll("#withdrawalsTable tbody tr")
        .forEach(row => {

            row.style.display =
                row.innerText.toLowerCase().includes(value)
                ? ""
                : "none";

        });

});
</script>

<?php include './includes/admin_footer.php'; ?>
