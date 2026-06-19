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

    // STATS
    $totalDeposits = $pdo->query("SELECT COUNT(*) FROM deposits")->fetchColumn();
    $pendingDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status='pending'")->fetchColumn();
    $approvedDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status='approved'")->fetchColumn();
    $rejectedDeposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status='rejected'")->fetchColumn();

    $deposits = $pdo->query("
        SELECT *
        FROM deposits
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
            <i class="fas fa-money-bill-wave"></i> Deposits Management
        </h2>
        <small class="text-muted">Manage all user deposits</small>
    </div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Deposits</h6>
                <h3><?= number_format($totalDeposits) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-warning border-4">
            <div class="card-body text-center">
                <h6>Pending</h6>
                <h3><?= number_format($pendingDeposits) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-success border-4">
            <div class="card-body text-center">
                <h6>Approved</h6>
                <h3><?= number_format($approvedDeposits) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-danger border-4">
            <div class="card-body text-center">
                <h6>Rejected</h6>
                <h3><?= number_format($rejectedDeposits) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="card shadow-lg">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Deposits</h5>

        <input type="text"
               id="depositSearch"
               class="form-control form-control-sm w-25"
               placeholder="Search deposits">
    </div>

    <div class="card-body p-0 table-responsive">

        <table class="table table-hover mb-0" id="depositsTable">

            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($deposits as $d): ?>

                <?php
                    $filename = basename($d['proof_file']);
                    $proofPath = "../members/uploads/deposits/" . $filename;
                ?>

                <tr>

                    <td><?= $d['id'] ?></td>

                    <td><?= $d['user_id'] ?></td>

                    <td><?= htmlspecialchars($d['email']) ?></td>

                    <td>
                        <strong>₦<?= number_format($d['amount'], 2) ?></strong>
                    </td>

                    <!-- PROOF -->
                    <td>
                        <a href="../<?= htmlspecialchars($d['proof_file']) ?>" target="_blank">
                            <img src="../members/<?= htmlspecialchars($d['proof_file']) ?>"
                                 style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                        </a>
                    </td>

                    <td>
                        <?php
                        $badge =
                            $d['status'] == 'approved' ? 'success' :
                            ($d['status'] == 'rejected' ? 'danger' : 'warning');
                        ?>

                        <span class="badge bg-<?= $badge ?>">
                            <?= ucfirst($d['status']) ?>
                        </span>
                    </td>

                    <td>
                        <?= date('d M Y h:i A', strtotime($d['created_at'])) ?>
                    </td>

                    <td>

                        <button class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editDeposit<?= $d['id'] ?>">
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
<?php foreach ($deposits as $d): ?>

<div class="modal fade"
     id="editDeposit<?= $d['id'] ?>"
     tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
              action="update-deposit-status"
              class="modal-content">

            <input type="hidden"
                   name="id"
                   value="<?= $d['id'] ?>">

            <div class="modal-header">
                <h5 class="modal-title">
                    Deposit #<?= $d['id'] ?>
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p><strong>User:</strong> <?= htmlspecialchars($d['email']) ?></p>

                <p><strong>Amount:</strong>
                    ₦<?= number_format($d['amount'],2) ?>
                </p>

                <?php
                    $filename = basename($d['proof_file']);
                    $proofPath = "../members/uploads/deposits/" . $filename;
                ?>

                <p>
                    <a href="<?= $proofPath ?>"
                       target="_blank"
                       class="btn btn-info btn-sm">
                        View Proof
                    </a>
                </p>

                <label class="form-label">
                    Deposit Status
                </label>

                <select name="status"
                        class="form-control">

                    <option value="pending"
                        <?= $d['status']=='pending'?'selected':'' ?>>
                        Pending
                    </option>

                    <option value="approved"
                        <?= $d['status']=='approved'?'selected':'' ?>>
                        Approved
                    </option>

                    <option value="rejected"
                        <?= $d['status']=='rejected'?'selected':'' ?>>
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
document.getElementById("depositSearch").addEventListener("input", function () {

    let value = this.value.toLowerCase();

    document.querySelectorAll("#depositsTable tbody tr")
        .forEach(row => {

            row.style.display =
                row.innerText.toLowerCase().includes(value)
                ? ""
                : "none";

        });

});
</script>

<?php include './includes/admin_footer.php'; ?>
