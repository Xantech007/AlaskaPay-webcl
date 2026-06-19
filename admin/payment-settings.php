<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Payment Settings";
include './includes/admin_header.php';

try {

    $totalMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods")->fetchColumn();
    $bankMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE type='bank'")->fetchColumn();
    $momoMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE type='momo'")->fetchColumn();
    $cryptoMethods = $pdo->query("SELECT COUNT(*) FROM payment_methods WHERE type='crypto'")->fetchColumn();

    $methods = $pdo->query("
        SELECT *
        FROM payment_methods
        ORDER BY created_at DESC
    ")->fetchAll();

} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<div class="main p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold text-primary mb-0">
            <i class="fas fa-credit-card"></i> Payment Settings
        </h2>
        <small class="text-muted">
            Manage deposit payment methods
        </small>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addMethod">
        <i class="fas fa-plus"></i> Add Method
    </button>

</div>

<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Methods</h6>
                <h3><?= number_format($totalMethods) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-success border-4">
            <div class="card-body text-center">
                <h6>Bank</h6>
                <h3><?= number_format($bankMethods) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-warning border-4">
            <div class="card-body text-center">
                <h6>MoMo</h6>
                <h3><?= number_format($momoMethods) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-info border-4">
            <div class="card-body text-center">
                <h6>Crypto</h6>
                <h3><?= number_format($cryptoMethods) ?></h3>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-lg">

    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">Payment Methods</h5>

        <input type="text"
               id="methodSearch"
               class="form-control form-control-sm w-25"
               placeholder="Search...">
    </div>

    <div class="card-body p-0 table-responsive">

        <table class="table table-hover mb-0" id="methodsTable">

            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Country</th>
                    <th>Method</th>
                    <th>Method Name</th>
                    <th>Method ID</th>
                    <th>Type</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($methods as $m): ?>

                <tr>

                    <td><?= $m['id'] ?></td>

                    <td><?= htmlspecialchars($m['country']) ?></td>

                    <td><?= htmlspecialchars($m['method']) ?></td>

                    <td><?= htmlspecialchars($m['method_name']) ?></td>

                    <td><?= htmlspecialchars($m['method_id']) ?></td>

                    <td>
                        <?php
                            $badge =
                                $m['type'] == 'bank' ? 'success' :
                                ($m['type'] == 'momo' ? 'warning text-dark' : 'info');
                        ?>
                    
                        <span class="badge bg-<?= $badge ?>">
                            <?= strtoupper($m['type']) ?>
                        </span>
                    </td>

                    <td>
                        <?= date('d M Y h:i A', strtotime($m['created_at'])) ?>
                    </td>

                    <td>

                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editMethod<?= $m['id'] ?>">
                            Edit
                        </button>

                        <a href="delete-payment-method?id=<?= $m['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete payment method?')">
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

<!-- ADD METHOD -->

<div class="modal fade" id="addMethod">

<div class="modal-dialog">

<form method="POST"
      action="add-payment-method"
      class="modal-content">

<div class="modal-header">
    <h5>Add Payment Method</h5>
</div>

<div class="modal-body">

    <label class="form-label">Country</label>
    <input class="form-control mb-3"
           name="country"
           placeholder="Country"
           required>

    <label class="form-label">Method</label>
    <input class="form-control mb-3"
           name="method"
           placeholder="Method"
           required>

    <label class="form-label">Method Name</label>
    <input class="form-control mb-3"
           name="method_name"
           placeholder="Method Name"
           required>

    <label class="form-label">Method ID</label>
    <input class="form-control mb-3"
           name="method_id"
           placeholder="Method ID"
           required>

    <label class="form-label">Type</label>
    <select class="form-control"
            name="type"
            required>

        <option value="bank">Bank</option>
        <option value="momo">MoMo</option>
        <option value="crypto">Crypto</option>

    </select>

</div>

<div class="modal-footer">
    <button class="btn btn-success">
        Save Method
    </button>
</div>

</form>

</div>
</div>

<!-- EDIT MODALS -->

<?php foreach ($methods as $m): ?>

<div class="modal fade"
     id="editMethod<?= $m['id'] ?>">

<div class="modal-dialog">

<form method="POST"
      action="edit-payment-method"
      class="modal-content">

<input type="hidden"
       name="id"
       value="<?= $m['id'] ?>">

<div class="modal-header">
    <h5 class="modal-title">
        Edit <?= strtoupper($m['type']) ?> Method #<?= $m['id'] ?>
    </h5>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <label class="form-label">Country</label>
    <input class="form-control mb-3"
           name="country"
           value="<?= htmlspecialchars($m['country']) ?>">

    <label class="form-label">Method</label>
    <input class="form-control mb-3"
           name="method"
           value="<?= htmlspecialchars($m['method']) ?>">

    <label class="form-label">Method Name</label>
    <input class="form-control mb-3"
           name="method_name"
           value="<?= htmlspecialchars($m['method_name']) ?>">

    <label class="form-label">Method ID</label>
    <input class="form-control mb-3"
           name="method_id"
           value="<?= htmlspecialchars($m['method_id']) ?>">

    <label class="form-label">Type</label>
    <select class="form-control"
            name="type">

        <option value="bank"
            <?= $m['type']=='bank' ? 'selected' : '' ?>>
            Bank
        </option>

        <option value="momo"
            <?= $m['type']=='momo' ? 'selected' : '' ?>>
            MoMo
        </option>

        <option value="crypto"
            <?= $m['type']=='crypto' ? 'selected' : '' ?>>
            Crypto
        </option>

    </select>

</div>
    
<div class="modal-footer">
    <button class="btn btn-success">
        Update Method
    </button>
</div>

</form>

</div>
</div>

<?php endforeach; ?>

<script>
document.getElementById("methodSearch").addEventListener("input", function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll("#methodsTable tbody tr").forEach(row => {

        row.style.display =
            row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";

    });

});
</script>

<?php include './includes/admin_footer.php'; ?>
