<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Job Applications";
include './includes/admin_header.php';

try {

    // STATS
    $totalJobs = $pdo->query("SELECT COUNT(*) FROM job_applications")->fetchColumn();
    $pendingJobs = $pdo->query("SELECT COUNT(*) FROM job_applications WHERE status='pending'")->fetchColumn();
    $acceptedJobs = $pdo->query("SELECT COUNT(*) FROM job_applications WHERE status='accepted'")->fetchColumn();
    $rejectedJobs = $pdo->query("SELECT COUNT(*) FROM job_applications WHERE status='rejected'")->fetchColumn();

    $jobs = $pdo->query("
        SELECT *
        FROM job_applications
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
            <i class="fas fa-briefcase"></i> Job Applications
        </h2>
        <small class="text-muted">Manage all job applications</small>
    </div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow border-start border-primary border-4">
            <div class="card-body text-center">
                <h6>Total Applications</h6>
                <h3><?= number_format($totalJobs) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-warning border-4">
            <div class="card-body text-center">
                <h6>Pending</h6>
                <h3><?= number_format($pendingJobs) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-success border-4">
            <div class="card-body text-center">
                <h6>Accepted</h6>
                <h3><?= number_format($acceptedJobs) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-start border-danger border-4">
            <div class="card-body text-center">
                <h6>Rejected</h6>
                <h3><?= number_format($rejectedJobs) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="card shadow-lg">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Applications</h5>

        <input type="text"
               id="jobSearch"
               class="form-control form-control-sm w-25"
               placeholder="Search applications">
    </div>

    <div class="card-body p-0 table-responsive">

        <table class="table table-hover mb-0" id="jobsTable">

            <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>DOB</th>
                <th>Country Status</th>
                <th>Current Country</th>
                <th>State</th>
                <th>Sector</th>
                <th>Level of Education</th>
                <th>Years of Experience</th>
                <th>Employment Status</th>
                <th>Expected Salary</th>
                <th>Skills</th>
                <th>Cover Letter</th>
                <th>CV</th>
                <th>Visa Required</th>
                <th>Relocation Required</th>
                <th>ID Type</th>
                <th>ID Number</th>
                <th>ID Front</th>
                <th>ID Back</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($jobs as $j): ?>
            <tr>
                <td><?= $j['id'] ?></td>
                <td><?= htmlspecialchars($j['user_id']) ?></td>
                <td><?= htmlspecialchars($j['full_name']) ?></td>
                <td><?= htmlspecialchars($j['email']) ?></td>
                <td><?= htmlspecialchars($j['phone']) ?></td>
            
                <td>
                    <?php
                    $badge =
                        $j['status'] == 'accepted' ? 'success' :
                        ($j['status'] == 'rejected' ? 'danger' : 'warning');
                    ?>
                    <span class="badge bg-<?= $badge ?>">
                        <?= ucfirst($j['status']) ?>
                    </span>
                </td>
            
                <td><?= htmlspecialchars($j['date_of_birth']) ?></td>
                <td><?= htmlspecialchars($j['country_status']) ?></td>
                <td><?= htmlspecialchars($j['current_country']) ?></td>
                <td><?= htmlspecialchars($j['us_state']) ?></td>
                <td><?= htmlspecialchars($j['sector']) ?></td>
                <td><?= htmlspecialchars($j['highest_education']) ?></td>
                <td><?= htmlspecialchars($j['years_of_experience']) ?></td>
                <td><?= htmlspecialchars($j['employment_status']) ?></td>
                <td>$<?= htmlspecialchars($j['expected_salary']) ?></td>
            
                <td><?= htmlspecialchars($j['skills']) ?></td>
            
                <td><?= htmlspecialchars($j['cover_letter']) ?></td>
            
                <td>
                    <?php if ($j['resume_path']): ?>
                        <button class="btn btn-sm btn-outline-primary preview-file"
                                data-file="<?= htmlspecialchars($j['resume_path']) ?>"
                                data-type="pdf">
                            Preview CV
                        </button>
                    <?php else: ?>-
                    <?php endif; ?>
                </td>
            
                <td>
                    <?= $j['visa_required'] == '1' ? 'Yes' : 'No' ?>
                </td>
                
                <td>
                    <?= $j['relocation_willing'] == '1' ? 'Yes' : 'No' ?>
                </td>
            
                <td><?= htmlspecialchars($j['id_type']) ?></td>
                <td><?= htmlspecialchars($j['id_number']) ?></td>
            
                <td>
                    <?php if ($j['id_front']): ?>
                        <button class="btn btn-sm btn-outline-success preview-file"
                                data-file="<?= htmlspecialchars($j['id_front']) ?>"
                                data-type="image">
                            ID Front
                        </button>
                    <?php else: ?>-
                    <?php endif; ?>
                </td>
                
                <td>
                    <?php if ($j['id_back']): ?>
                        <button class="btn btn-sm btn-outline-success preview-file"
                                data-file="<?= htmlspecialchars($j['id_back']) ?>"
                                data-type="image">
                            ID Back
                        </button>
                    <?php else: ?>-
                    <?php endif; ?>
                </td>
            
                <td>
                    <?= date('d M Y h:i A', strtotime($j['created_at'])) ?>
                </td>
            
                <td>
                    <button class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#editJob<?= $j['id'] ?>">
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

<!-- MODALS -->
<?php foreach ($jobs as $j): ?>

<div class="modal fade"
     id="editJob<?= $j['id'] ?>"
     tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
              action="update-job-applications-status"
              class="modal-content">

            <input type="hidden" name="id" value="<?= $j['id'] ?>">

            <div class="modal-header">
                <h5 class="modal-title">
                    Application #<?= $j['id'] ?>
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p><strong>Name:</strong> <?= htmlspecialchars($j['full_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($j['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($j['phone']) ?></p>

                <p><strong>Sector:</strong> <?= htmlspecialchars($j['sector']) ?></p>

                <p><strong>Country:</strong> <?= htmlspecialchars($j['current_country'] ?? '-') ?></p>

                <p><strong>Experience:</strong> <?= htmlspecialchars($j['years_of_experience'] ?? 0) ?> years</p>

                <hr>

                <label class="form-label">Application Status</label>

                <select name="status" class="form-control">

                    <option value="pending" <?= $j['status']=='pending'?'selected':'' ?>>
                        Pending
                    </option>

                    <option value="accepted" <?= $j['status']=='accepted'?'selected':'' ?>>
                        Accepted
                    </option>

                    <option value="rejected" <?= $j['status']=='rejected'?'selected':'' ?>>
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
document.getElementById("jobSearch").addEventListener("input", function () {

    let value = this.value.toLowerCase();

    document.querySelectorAll("#jobsTable tbody tr").forEach(row => {
        row.style.display =
            row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";
    });

});
</script>

<?php include './includes/admin_footer.php'; ?>
