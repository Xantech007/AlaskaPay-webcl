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
   FETCH WITHDRAWAL HISTORY
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM withdrawals
    WHERE user_id = ?
    ORDER BY id ASC
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
    min-width: 850px;
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

.badge-pending {
    background: #fff8e1;
    color: #f39c12;
}

.badge-approved {
    background: #eafaf1;
    color: #27ae60;
}

.badge-rejected {
    background: #fdecea;
    color: #e74c3c;
}

@media (max-width: 600px) {
    .history-table {
        min-width: 850px;
    }
}

.receipt-btn{
    display:inline-block;
    padding:6px 12px;
    border-radius:8px;
    background:#0d6efd;
    color:#fff;
    text-decoration:none;
    font-size:12px;
    font-weight:600;
    transition:0.2s ease;
    white-space:nowrap;
}

.receipt-btn:hover{
    background:#0b5ed7;
    transform:translateY(-1px);
}

@media (max-width:600px){
    .receipt-btn{
        display:block;
        text-align:center;
        width:100%;
    }
}
    
</style>

<div class="history-wrapper">

    <h2 style="margin-bottom:15px;color:var(--primary);">
        <i class="fas fa-money-bill-wave"></i> Withdrawal History
    </h2>

    <?php if (empty($history)): ?>

        <div style="padding:15px;background:#fff;border-radius:10px;">
            No withdrawal history found.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="history-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount (USD)</th>
                        <th>Actual Amount</th>
                
                        <th><?= htmlspecialchars($user['method'] ?? 'Method') ?></th>
                        <th><?= htmlspecialchars($user['method_name'] ?? 'Account Name') ?></th>
                        <th><?= htmlspecialchars($user['method_id'] ?? 'Account ID') ?></th>
                
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $i = 1; ?>

                    <?php foreach ($history as $row): ?>

                        <?php
                        $status = strtolower($row['status'] ?? 'pending');

                        $badgeClass = match($status) {
                            'approved' => 'badge-approved',
                            'rejected' => 'badge-rejected',
                            default => 'badge-pending'
                        };
                        ?>

                        <tr>
                        
                            <td>#<?= $row['id'] ?></td>
                        
                            <td>
                                $<?= number_format((float)$row['amount'], 2) ?>
                            </td>
                        
                            <td>
                                <?php if (!empty($row['receive_currency'])): ?>
                                    <?= htmlspecialchars($row['receive_currency']) ?>
                                    <?= number_format((float)$row['receive_amount'], 2) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        
                            <td><?= htmlspecialchars($row['method']) ?></td>
                            <td><?= htmlspecialchars($row['account_name']) ?></td>
                            <td><?= htmlspecialchars($row['account_id']) ?></td>
                        
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                        
                            <td>
                                <?= date('d M Y h:i A', strtotime($row['created_at'])) ?>
                            </td>
                        
                            <!-- ACTION -->
                            <td>
                                <a href="withdrawal-receipt?id=<?= (int)$row['id'] ?>"
                                   class="receipt-btn">
                                    View Receipt
                                </a>
                            </td>
                        
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
