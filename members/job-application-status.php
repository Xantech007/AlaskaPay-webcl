<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit();
}

/* -----------------------------
   FETCH JOB APPLICATION HISTORY
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM job_applications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.history-wrapper {
    padding: 15px;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.history-table th,
.history-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    white-space: nowrap;
}

.history-table th {
    background: #f8fbff;
    color: var(--primary);
    font-weight: 600;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-pending {
    background: #fff8e1;
    color: #f39c12;
}

.badge-accepted {
    background: #eafaf1;
    color: #27ae60;
}

.badge-rejected {
    background: #fdeaea;
    color: #e74c3c;
}

@media (max-width: 600px) {
    .history-table {
        min-width: 850px;
    }
}
</style>

<div class="history-wrapper">

<h2 style="margin-bottom:15px;color:var(--primary);">
    <i class="fas fa-briefcase"></i>
    Job Application History
</h2>

<?php if (empty($applications)): ?>

    <div style="padding:15px;background:#fff;border-radius:10px;">
        No job applications found.
    </div>

    <div style="margin-top:20px;">
        <a href="job-application.php"
           style="
                display:inline-block;
                padding:12px 24px;
                background:var(--primary);
                color:#fff;
                text-decoration:none;
                border-radius:8px;
                font-weight:600;
           ">
            <i class="fas fa-plus-circle"></i>
            Apply For A Job
        </a>
    </div>

<?php else: ?>

    <div class="table-responsive">

        <table class="history-table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Sector</th>
                    <th>Education</th>
                    <th>Experience</th>
                    <th>Expected Salary</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>

            <tbody>

                <?php $i = 1; ?>

                <?php foreach ($applications as $row): ?>

                    <tr>

                        <td>#<?= $i++ ?></td>

                        <td>
                            <?= htmlspecialchars($row['full_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['email']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['phone']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['sector']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['highest_education'] ?: 'N/A') ?>
                        </td>

                        <td>
                            <?= (int)$row['years_of_experience'] ?> Year(s)
                        </td>

                        <td>
                            <?= htmlspecialchars($row['expected_salary'] ?: 'N/A') ?>
                        </td>

                        <td>

                            <?php
                            $status = strtolower($row['status']);

                            if ($status === 'accepted') {
                                echo '<span class="badge badge-accepted">Accepted</span>';
                            } elseif ($status === 'rejected') {
                                echo '<span class="badge badge-rejected">Rejected</span>';
                            } else {
                                echo '<span class="badge badge-pending">Pending</span>';
                            }
                            ?>

                        </td>

                        <td>
                            <?= date('M d, Y h:i A', strtotime($row['created_at'])) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;text-align:center;">

        <a href="job-application.php"
           style="
                display:inline-block;
                padding:12px 24px;
                background:var(--primary);
                color:#fff;
                text-decoration:none;
                border-radius:8px;
                font-weight:600;
           ">
            <i class="fas fa-plus-circle"></i>
            Submit New Application
        </a>

    </div>

<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
