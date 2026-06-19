<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Manage Deposits";
include './includes/admin_header.php';

try {

    // ================= STATS =================
    $totalDeposits = $pdo->query("SELECT COUNT(*) FROM deposits")->fetchColumn();
    $pendingDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn();
    $approvedDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'approved'")->fetchColumn();
    $rejectedDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'rejected'")->fetchColumn();

    // ================= UPDATE STATUS =================
    if (isset($_POST['update_status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];

        $stmt = $pdo->prepare("UPDATE deposits SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        header("Location: deposits.php");
        exit();
    }

    // ================= DEPOSITS =================
    $deposits = $pdo->query("
        SELECT *
        FROM deposits
        ORDER BY created_at DESC
        LIMIT 300
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
            <i class="fas fa-wallet"></i> Deposits Management
        </h2>
        <small class="text-muted">Review and manage all user deposits</small>
    </div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Deposits</h6>
                <h3><?= number_format($totalDeposits) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-warning border-4">
            <div class="card-body text-center">
                <h6>Pending</h6>
                <h3><?= number_format($pendingDeposits) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-success border-4">
            <div class="card-body text-center">
                <h6>Approved</h6>
                <h3><?= number_format($approvedDeposits) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-lg border-start border-danger border-4">
            <div class="card-body text-center">
                <h6>Rejected</h6>
                <h3><?= number_format($rejectedDeposits) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="card shadow-lg">
    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">All Deposits</h5>
        <input type="text" id="depositSearch" class="form-control form-control-sm w-25" placeholder="Search...">
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0" id="depositsTable">
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

                    <td>User #<?= $d['user_id'] ?></td>

                    <td><?= htmlspecialchars($d['email']) ?></td>

                    <td>₦<?= number_format($d['amount'], 2) ?></td>

                    <!-- PROOF -->
                    <td>
                        <a href="../<?= htmlspecialchars($d['proof_file']) ?>" target="_blank">
                            <img src="../members/<?= htmlspecialchars($d['proof_file']) ?>"
                                 style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                        </a>
                    </td>

                    <!-- STATUS -->
                    <td>
                        <span class="badge bg-<?=
                            $d['status']=='approved' ? 'success' :
                            ($d['status']=='rejected' ? 'danger' : 'warning')
                        ?>">
                            <?= ucfirst($d['status']) ?>
                        </span>
                    </td>

                    <!-- DATE + TIME -->
                    <td>
                        <?= date('M d, Y H:i A', strtotime($d['created_at'])) ?>
                    </td>

                    <!-- ACTION -->
                    <td>
                        <button class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#status<?= $d['id'] ?>">
                            Update
                        </button>
                    </td>
                </tr>

                <!-- STATUS MODAL -->
                <div class="modal fade" id="status<?= $d['id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <form method="POST" class="modal-content">

                        <input type="hidden" name="id" value="<?= $d['id'] ?>">

                        <div class="modal-header">
                            <h5 class="modal-title">Update Deposit Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" <?= $d['status']=='pending'?'selected':'' ?>>Pending</option>
                                <option value="approved" <?= $d['status']=='approved'?'selected':'' ?>>Approved</option>
                                <option value="rejected" <?= $d['status']=='rejected'?'selected':'' ?>>Rejected</option>
                            </select>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button class="btn btn-success" name="update_status">
                                Save Changes
                            </button>
                        </div>

                    </form>
                  </div>
                </div>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- SEARCH -->
<script>
document.getElementById("depositSearch").addEventListener("input", function () {
    let val = this.value.toLowerCase();
    document.querySelectorAll("#depositsTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
    });
});
</script>

<?php include './includes/admin_footer.php'; ?>
