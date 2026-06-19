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
            Manage country rules, fees & overrides
        </small>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addRegion">
        <i class="fas fa-plus"></i> Add Region
    </button>

</div>

<!-- KPI -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
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
        <h5 class="mb-0">Region Settings</h5>

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
                    <th>Method</th>
                    <th>Method Name</th>
                    <th>Method ID</th>
                    <th>Method Value</th>
                    <th>Method Name Value</th>
                    <th>Method ID Value</th>
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

                    <td>
                        <span class="badge bg-primary">
                            <?= number_format($r['fee'], 2) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($r['method']) ?></td>

                    <td><?= htmlspecialchars($r['method_name']) ?></td>

                    <td><?= htmlspecialchars($r['method_id']) ?></td>

                    <td><?= htmlspecialchars($r['method_value']) ?></td>

                    <td><?= htmlspecialchars($r['method_name_value']) ?></td>

                    <td><?= htmlspecialchars($r['method_id_value']) ?></td>

                    <td>
                        <span class="badge bg-<?= $r['ignore_location'] == 'yes' ? 'success' : 'danger' ?>">
                            <?= strtoupper($r['ignore_location']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($r['alternate_country']) ?></td>

                    <td>
                        <?= date('d M Y h:i A', strtotime($r['created_at'])) ?>
                    </td>

                    <td>

                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editRegion<?= $r['id'] ?>">
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

<!-- ADD REGION -->
<div class="modal fade" id="addRegion">
<div class="modal-dialog">

<form method="POST"
      action="add-region-settings"
      class="modal-content">

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
    <input class="form-control mb-3" name="fee" type="number" step="0.01" required>

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
    <input class="form-control mb-3" name="method_id_value">

    <label class="form-label">Ignore Location</label>
    <select class="form-control mb-3" name="ignore_location">
        <option value="no">NO</option>
        <option value="yes">YES</option>
    </select>

    <label class="form-label">Alternate Country</label>
    <select class="form-control" name="alternate_country">
        <option value="">Select Country</option>
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

<!-- EDIT REGION MODALS -->
<?php foreach ($regions as $r): ?>

<div class="modal fade" id="editRegion<?= $r['id'] ?>">
<div class="modal-dialog">

<form method="POST"
      action="edit-region-settings"
      class="modal-content">

<input type="hidden" name="id" value="<?= $r['id'] ?>">

<div class="modal-header">
    <h5 class="modal-title">Edit Region #<?= $r['id'] ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <label class="form-label">Country</label>
    <select class="form-control mb-3" name="country" required>
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"
                <?= $r['country'] == $c ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label class="form-label">Fee</label>
    <input class="form-control mb-3" name="fee"
           value="<?= htmlspecialchars($r['fee']) ?>">

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
    <input class="form-control mb-3" name="method_id_value"
           value="<?= htmlspecialchars($r['method_id_value']) ?>">

    <label class="form-label">Ignore Location</label>
    <select class="form-control mb-3" name="ignore_location">
        <option value="no" <?= $r['ignore_location']=='no'?'selected':'' ?>>NO</option>
        <option value="yes" <?= $r['ignore_location']=='yes'?'selected':'' ?>>YES</option>
    </select>

    <label class="form-label">Alternate Country</label>
    <select class="form-control" name="alternate_country">
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"
                <?= $r['alternate_country'] == $c ? 'selected' : '' ?>>
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
document.getElementById("regionSearch").addEventListener("input", function() {

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
