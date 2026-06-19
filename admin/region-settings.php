<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Region Settings";
include './includes/admin_header.php';
include './includes/countries.php';

try {

    $totalRegions = $pdo->query("SELECT COUNT(*) FROM region_settings")->fetchColumn();

    $regions = $pdo->query("
        SELECT *
        FROM region_settings
        ORDER BY created_at DESC
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
            <i class="fas fa-globe"></i> Region Settings
        </h2>
        <small class="text-muted">
            Manage regional fees, currency, and overrides
        </small>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addRegion">
        <i class="fas fa-plus"></i> Add Region
    </button>

</div>

<!-- STATS -->
<div class="row g-4 mb-4">

    <div class="col-md-12">
        <div class="card shadow border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Regions</h6>
                <h3><?= number_format($totalRegions) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="card shadow-lg">

    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">All Regions</h5>

        <input type="text"
               id="regionSearch"
               class="form-control form-control-sm w-25"
               placeholder="Search...">
    </div>

    <div class="card-body p-0 table-responsive">

        <table class="table table-hover mb-0" id="regionTable">

            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Country</th>
                    <th>Fee</th>
                    <th>Currency</th>
                    <th>Rate</th>
                    <th>Convert</th>
                    <th>Ignore Location</th>
                    <th>Alternate Country</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($regions as $r): ?>

                <tr>

                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['country']) ?></td>

                    <td>₦<?= number_format($r['fee'],2) ?></td>

                    <td><?= htmlspecialchars($r['currency'] ?? '-') ?></td>

                    <td><?= htmlspecialchars($r['rate'] ?? '-') ?></td>

                    <td>
                        <span class="badge bg-<?= $r['convert_currency']=='yes'?'success':'secondary' ?>">
                            <?= strtoupper($r['convert_currency']) ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-<?= $r['ignore_location']=='yes'?'danger':'success' ?>">
                            <?= strtoupper($r['ignore_location']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($r['alternate_country'] ?? '-') ?></td>

                    <td>
                        <?= date('d M Y h:i A', strtotime($r['created_at'])) ?>
                    </td>

                    <td>

                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editRegion<?= $r['id'] ?>">
                            Edit
                        </button>

                        <a href="delete-region?id=<?= $r['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete region?')">
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

<!-- ADD REGION -->
<div class="modal fade" id="addRegion">
<div class="modal-dialog">

<form method="POST" action="add-region" class="modal-content">

<div class="modal-header">
    <h5>Add Region</h5>
</div>

<div class="modal-body">

    <label class="form-label">Country</label>
    <select class="form-control mb-3" name="country" required>
        <option value="">Select Country</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
    </select>

    <label class="form-label">Fee</label>
    <input class="form-control mb-3" name="fee" type="number" step="0.01">

    <label class="form-label">Currency</label>
    <input class="form-control mb-3" name="currency">

    <label class="form-label">Rate</label>
    <input class="form-control mb-3" name="rate" type="number" step="0.0001">

    <label class="form-label">Convert Currency</label>
    <select class="form-control mb-3" name="convert_currency">
        <option value="no">No</option>
        <option value="yes">Yes</option>
    </select>

    <label class="form-label">Ignore Location</label>
    <select class="form-control mb-3" name="ignore_location">
        <option value="no">No</option>
        <option value="yes">Yes</option>
    </select>

    <label class="form-label">Alternate Country</label>
    <select class="form-control mb-3" name="alternate_country">
        <option value="">None</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
    </select>

</div>

<div class="modal-footer">
    <button class="btn btn-success">Save</button>
</div>

</form>

</div>
</div>

<!-- EDIT REGION -->
<?php foreach ($regions as $r): ?>

<div class="modal fade" id="editRegion<?= $r['id'] ?>">
<div class="modal-dialog">

<form method="POST" action="edit-region" class="modal-content">

<input type="hidden" name="id" value="<?= $r['id'] ?>">

<div class="modal-header">
    <h5 class="modal-title">Edit Region #<?= $r['id'] ?></h5>
</div>

<div class="modal-body">

    <label class="form-label">Country</label>
    <select class="form-control mb-3" name="country" required>
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"
                <?= $r['country']==$c?'selected':'' ?>>
                <?= htmlspecialchars($c) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label class="form-label">Fee</label>
    <input class="form-control mb-3" name="fee"
           value="<?= $r['fee'] ?>" type="number" step="0.01">

    <label class="form-label">Currency</label>
    <input class="form-control mb-3" name="currency"
           value="<?= htmlspecialchars($r['currency']) ?>">

    <label class="form-label">Rate</label>
    <input class="form-control mb-3" name="rate"
           value="<?= htmlspecialchars($r['rate']) ?>" type="number" step="0.0001">

    <label class="form-label">Convert Currency</label>
    <select class="form-control mb-3" name="convert_currency">
        <option value="no" <?= $r['convert_currency']=='no'?'selected':'' ?>>No</option>
        <option value="yes" <?= $r['convert_currency']=='yes'?'selected':'' ?>>Yes</option>
    </select>

    <label class="form-label">Ignore Location</label>
    <select class="form-control mb-3" name="ignore_location">
        <option value="no" <?= $r['ignore_location']=='no'?'selected':'' ?>>No</option>
        <option value="yes" <?= $r['ignore_location']=='yes'?'selected':'' ?>>Yes</option>
    </select>

    <label class="form-label">Alternate Country</label>
    <select class="form-control mb-3" name="alternate_country">
        <option value="">None</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"
                <?= $r['alternate_country']==$c?'selected':'' ?>>
                <?= htmlspecialchars($c) ?>
            </option>
        <?php endforeach; ?>
    </select>

</div>

<div class="modal-footer">
    <button class="btn btn-success">Update</button>
</div>

</form>

</div>
</div>

<?php endforeach; ?>

<script>
document.getElementById("regionSearch").addEventListener("input", function () {

    let value = this.value.toLowerCase();

    document.querySelectorAll("#regionTable tbody tr").forEach(row => {

        row.style.display =
            row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";

    });

});
</script>

<?php include './includes/admin_footer.php'; ?>
