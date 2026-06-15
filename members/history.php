<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user (safety check)
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit();
}

// Get history
$stmt = $pdo->prepare("
    SELECT *
    FROM state_claims
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$history = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div class="section active">

        <div class="loan-form">

            <h2 style="text-align:center;margin-bottom:25px;color:var(--primary);">
                <i class="fas fa-history"></i> Claim History
            </h2>

            <?php if (count($history) > 0): ?>

                <!-- ✅ SCROLLABLE WRAPPER -->
                <div style="
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    border-radius: 10px;
                ">

                    <table class="table table-striped table-hover"
                           style="min-width: 900px;">

                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Region</th>
                                <th>State / Source</th>
                                <th>Amount (USD)</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($history as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>

                                    <td>
                                        <?php if ($row['state'] === 'CODE REDEEM'): ?>
                                            <span class="badge bg-warning">Code</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">State</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($row['region'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['state']) ?></td>
                                    <td>USD <?= number_format($row['amount'], 2) ?></td>

                                    <td>
                                        <?= htmlspecialchars($row['description'] ?? 'N/A') ?>
                                        <?php if (!empty($row['code'])): ?>
                                            <br>
                                            <small>Code: <?= htmlspecialchars($row['code']) ?></small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= date('M d, Y h:i A', strtotime($row['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div style="
                    text-align:center;
                    padding:40px;
                    background:#f8f9fa;
                    border-radius:12px;
                    color:#666;
                ">
                    <i class="fas fa-inbox fa-2x mb-3"></i>
                    <p>No history found yet.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
