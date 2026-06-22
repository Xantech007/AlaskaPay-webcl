<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

/* -----------------------------
   FETCH USER
------------------------------*/
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit();
}

/* -----------------------------
   FETCH LATEST APPROVED APPLICATION
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM job_applications
    WHERE user_id = ?
    AND status = 'accepted'
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$application = $stmt->fetch();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.check-wrapper {
    padding: 20px;
}

.card-box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--primary);
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.info-item {
    background: #f8fbff;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
}

.instructions {
    background: #fff8e1;
    padding: 15px;
    border-left: 5px solid #f39c12;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.6;
}

.btn-back {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    background: var(--primary);
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}
</style>

<div class="check-wrapper">

<h2 style="margin-bottom:15px;color:var(--primary);">
    <i class="fas fa-briefcase"></i>
    Job Application Status Check
</h2>

<?php if (!$application): ?>

    <div class="card-box">
        <p style="margin:0;">
            No approved application found yet.
            Please check back later or ensure your application has been accepted.
        </p>
    </div>

    <a href="job-application-status.php" class="btn-back">
        Back to Applications
    </a>

<?php else: ?>

    <!-- APPLICATION PREVIEW -->
    <div class="card-box">

        <div class="section-title">Approved Application Preview</div>

        <div class="info-grid">

            <div class="info-item"><strong>Full Name:</strong><br><?= htmlspecialchars($application['full_name']) ?></div>

            <div class="info-item"><strong>Email:</strong><br><?= htmlspecialchars($application['email']) ?></div>

            <div class="info-item"><strong>Phone:</strong><br><?= htmlspecialchars($application['phone']) ?></div>

            <div class="info-item"><strong>Sector:</strong><br><?= htmlspecialchars($application['sector']) ?></div>

            <div class="info-item"><strong>Education:</strong><br><?= htmlspecialchars($application['highest_education'] ?: 'N/A') ?></div>

            <div class="info-item"><strong>Experience:</strong><br><?= (int)$application['years_of_experience'] ?> Year(s)</div>

            <div class="info-item"><strong>Expected Salary:</strong><br><?= htmlspecialchars($application['expected_salary'] ?: 'N/A') ?></div>

            <div class="info-item"><strong>Country Status:</strong><br><?= htmlspecialchars($application['country_status']) ?></div>

            <div class="info-item"><strong>State:</strong><br><?= htmlspecialchars($application['us_state'] ?: 'N/A') ?></div>

            <div class="info-item"><strong>Date Submitted:</strong><br>
                <?= date('M d, Y h:i A', strtotime($application['created_at'])) ?>
            </div>

        </div>
    </div>

    <!-- INSTRUCTIONS -->
    <div class="card-box">

        <div class="section-title">Next Steps</div>

        <div class="instructions">

            Congratulations! Your job application has been officially <strong>approved</strong>.

            <br><br>

            Please carefully check your <strong>email inbox</strong> (and spam/junk folder) for further instructions regarding:

            <ul style="margin-top:10px;">
                <li>Interview scheduling</li>
                <li>Verification process</li>
                <li>Onboarding details</li>
                <li>Further documentation requirements</li>
            </ul>

            <br>

            Ensure your email and phone remain active and reachable. Failure to respond may delay your processing.

        </div>

    </div>

    <!-- BACK BUTTON -->
    <a href="index.php" class="btn-back">
        ← Back to Dashboard
    </a>

<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
