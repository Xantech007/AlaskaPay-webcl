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
                    <th>User</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Currency</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Provider</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($deposits as $d): ?>

                <?php
                    $filename = basename($d['proof_file']);
                    $proofPath = "../members/" . $filename;
                ?>

                <tr>
                
                    <td><?= $d['id'] ?></td>
                
                    <td><?= $d['user_id'] ?></td>
                
                    <td><?= htmlspecialchars($d['email']) ?></td>
                
                    <td>
                        <?= htmlspecialchars($d['country'] ?? '-') ?>
                    </td>
                
                    <td>
                        <?= htmlspecialchars($d['currency'] ?? '-') ?>
                    </td>
                
                    <td>
                        <strong>
                            <?= htmlspecialchars($d['currency']) ?>
                            <?= number_format($d['amount'], 2) ?>
                        </strong>
                    </td>
                
                    <td>
                
                        <?php if (($d['is_external'] ?? 'no') === 'yes'): ?>
                
                            <span class="badge bg-info">
                                External
                            </span>
                
                        <?php else: ?>
                
                            <span class="badge bg-secondary">
                                Internal
                            </span>
                
                        <?php endif; ?>
                
                    </td>
                
                    <td>
                
                        <?php if (($d['is_external'] ?? 'no') === 'yes'): ?>
                
                            <?= htmlspecialchars($d['external_name']) ?>
                
                        <?php else: ?>
                
                            -
                            
                        <?php endif; ?>
                
                    </td>
                
                    <!-- PROOF -->
                    <td>
                
                        <?php if (!empty($d['proof_file'])): ?>
                
                            <a href="../members/<?= htmlspecialchars($d['proof_file']) ?>"
                               target="_blank">
                
                                <img src="../members/<?= htmlspecialchars($d['proof_file']) ?>"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                
                            </a>
                
                        <?php else: ?>
                
                            <span class="text-muted">No Receipt (External Payment)</span>
                
                        <?php endif; ?>
                
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
                
                            Manage
                
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
            
                <div class="mb-3">
            
                    <p><strong>ID:</strong> <?= $d['id'] ?></p>
            
                    <p><strong>User ID:</strong> <?= $d['user_id'] ?></p>
            
                    <p><strong>Email:</strong>
                        <?= htmlspecialchars($d['email']) ?>
                    </p>
            
                    <p><strong>Country:</strong>
                        <?= htmlspecialchars($d['country'] ?? '-') ?>
                    </p>
            
                    <p><strong>Currency:</strong>
                        <?= htmlspecialchars($d['currency'] ?? '-') ?>
                    </p>
            
                    <p><strong>Amount:</strong>
                        <?= htmlspecialchars($d['currency']) ?>
                        <?= number_format($d['amount'], 2) ?>
                    </p>
            
                    <p><strong>Type:</strong>
            
                        <?php if (($d['is_external'] ?? 'no') === 'yes'): ?>
            
                            <span class="badge bg-info">
                                External
                            </span>
            
                        <?php else: ?>
            
                            <span class="badge bg-secondary">
                                Internal
                            </span>
            
                        <?php endif; ?>
            
                    </p>

                    <?php if (($d['is_external'] ?? 'no') === 'yes'): ?>
                    
                        <hr>
                    
                        <p><strong>Provider:</strong>
                            <?= htmlspecialchars($d['external_name'] ?? '-') ?>
                        </p>
                    
                        <p><strong>Provider Link:</strong></p>
                    
                        <?php if (!empty($d['external_link'])): ?>
                    
                            <a href="<?= htmlspecialchars($d['external_link']) ?>"
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                Open Provider
                            </a>
                    
                        <?php else: ?>
                    
                            <span class="text-muted">No link available</span>
                    
                        <?php endif; ?>
                    
                    <?php endif; ?>
            
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
