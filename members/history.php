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
   FETCH HISTORY
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM state_claims
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->execute([$user_id]);
$history = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.history-wrapper {
    padding: 15px;
}

/* Mobile-safe table wrapper */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Table styling */
.history-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px; /* prevents squashing */
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
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #eafaf1;
    color: #27ae60;
}

.badge-info {
    background: #e7f3ff;
    color: #2980b9;
}

@media (max-width: 600px) {
    .history-table {
        min-width: 520px;
    }
}
</style>

<div class="history-wrapper">

    <h2 style="margin-bottom:15px;color:var(--primary);">
        <i class="fas fa-history"></i> Transaction History
    </h2>

    <?php if (empty($history)): ?>
        <div style="padding:15px;background:#fff;border-radius:10px;">
            No history found.
        </div>
    <?php else: ?>

        <div class="table-responsive">

            <table class="history-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Region</th>
                        <th>State</th>
                        <th>Amount (USD)</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td>#<?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['region']) ?></td>
                            <td><?= htmlspecialchars($row['state']) ?></td>
                            <td>$<?= number_format($row['amount'], 2) ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <?= htmlspecialchars($row['description'] ?? 'State Claim') ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['created_at'] ?? 'N/A') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<div style="margin-top:20px;text-align:center;">
    <a href="withdrawal-history.php"
       style="
            display:inline-block;
            padding:12px 24px;
            background:var(--primary);
            color:#fff;
            text-decoration:none;
            border-radius:8px;
            font-weight:600;
       ">
        <i class="fas fa-money-bill-wave"></i>
        View Withdrawal History
    </a>
</div>

<?php include 'includes/footer.php'; ?>
