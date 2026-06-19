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
        <small class="text-muted">Manage fees, currency, and payment routing</small>
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
    <h5 class="mb-0">All Region Settings</h5>

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

    <th>Method</th>
    <th>Method Name</th>
    <th>Method ID</th>

    <th>Method Value</th>
    <th>Method Name Value</th>
    <th>Method ID Value</th>

    <th>Ignore</th>
    <th>Alternate</th>

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

    <td><?= htmlspecialchars($r['method'] ?? '-') ?></td>
    <td><?= htmlspecialchars($r['method_name'] ?? '-') ?></td>
    <td><?= htmlspecialchars($r['method_id'] ?? '-') ?></td>

    <td><?= htmlspecialchars($r['method_value'] ?? '-') ?></td>
    <td><?= htmlspecialchars($r['method_name_value'] ?? '-') ?></td>
    <td><?= htmlspecialchars($r['method_id_value'] ?? '-') ?></td>

    <td>
        <span class="badge bg-<?= $r['ignore_location']=='yes'?'danger':'success' ?>">
            <?= strtoupper($r['ignore_location']) ?>
        </span>
    </td>

    <td><?= htmlspecialchars($r['alternate_country'] ?? '-') ?></td>

    <td><?= date('d M Y h:i A', strtotime($r['created_at'])) ?></td>

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

<!-- ================= ADD ================= -->
<div class="modal fade" id="addRegion">
<div class="modal-dialog">

<form method="POST" action="add-region" class="modal-content">

<div class="modal-header">
    <h5>Add Region</h5>
</div>

<div class="modal-body">

<!-- COUNTRY -->
<label class="form-label">Country</label>
<select class="form-control mb-3" name="country" required>
    <option value="">Select Country</option>
    <?php foreach ($countries as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
    <?php endforeach; ?>
</select>

<!-- FEES -->
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

<!-- PAYMENT METHODS -->
<label class="form-label">Method</label>
<input class="form-control mb-3" name="method">

<label class="form-label">Method Name</label>
<input class="form-control mb-3" name="method_name">

<label class="form-label">Method ID</label>
<input class="form-control mb-3" name="method_id">

<label class="form-label">Method Value</label>
<input class="form-control mb-3" name="method_value">

<label class="form-label">Method Name Value</label>
<input class="form-control mb-3" name="method_name_value">

<label class="form-label">Method ID Value</label>
<textarea class="form-control mb-3" name="method_id_value"></textarea>

<!-- REGION CONTROL -->
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

<!-- ================= EDIT ================= -->
<?php foreach ($regions as $r): ?>

<div class="modal fade" id="editRegion<?= $r['id'] ?>">
<div class="modal-dialog">

<form method="POST" action="edit-region" class="modal-content">

<input type="hidden" name="id" value="<?= $r['id'] ?>">

<div class="modal-header">
    <h5 class="modal-title">Edit Region #<?= $r['id'] ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<!-- COUNTRY -->
<label class="form-label">Country</label>
<select class="form-control mb-3" name="country">
    <?php foreach ($countries as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>"
            <?= $r['country']==$c?'selected':'' ?>>
            <?= htmlspecialchars($c) ?>
        </option>
    <?php endforeach; ?>
</select>

<!-- FEE -->
<label class="form-label">Fee</label>
<input class="form-control mb-3" name="fee"
       value="<?= $r['fee'] ?>">

<!-- CURRENCY -->
<label class="form-label">Currency</label>
<input class="form-control mb-3" name="currency"
       value="<?= htmlspecialchars($r['currency']) ?>">

<!-- RATE -->
<label class="form-label">Rate</label>
<input class="form-control mb-3" name="rate"
       value="<?= htmlspecialchars($r['rate']) ?>">

<!-- CONVERT CURRENCY -->
<label class="form-label">Convert Currency</label>
<select class="form-control mb-3" name="convert_currency">
    <option value="no" <?= $r['convert_currency']=='no'?'selected':'' ?>>No</option>
    <option value="yes" <?= $r['convert_currency']=='yes'?'selected':'' ?>>Yes</option>
</select>

<hr>

<!-- PAYMENT FIELDS -->
<label class="form-label">Method</label>
<input class="form-control mb-3" name="method"
       value="<?= htmlspecialchars($r['method']) ?>">

<label class="form-label">Method Name</label>
<input class="form-control mb-3" name="method_name"
       value="<?= htmlspecialchars($r['method_name']) ?>">

<label class="form-label">Method ID</label>
<input class="form-control mb-3" name="method_id"
       value="<?= htmlspecialchars($r['method_id']) ?>">

<label class="form-label">Method Value</label>
<input class="form-control mb-3" name="method_value"
       value="<?= htmlspecialchars($r['method_value']) ?>">

<label class="form-label">Method Name Value</label>
<input class="form-control mb-3" name="method_name_value"
       value="<?= htmlspecialchars($r['method_name_value']) ?>">

<label class="form-label">Method ID Value</label>
<textarea class="form-control mb-3" name="method_id_value"><?= htmlspecialchars($r['method_id_value']) ?></textarea>

<hr>

<!-- LOCATION SETTINGS -->
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
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
