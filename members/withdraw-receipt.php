<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid receipt ID.");
}

$stmt = $conn->prepare("
    SELECT w.*, u.email
    FROM withdrawals w
    INNER JOIN users u ON u.id = w.user_id
    WHERE w.id = ? AND w.user_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$withdrawal = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$withdrawal) {
    die("Receipt not found.");
}

$receipt_id = "#" . ($withdrawal['id'] * 27395);

$status = strtolower($withdrawal['status']);

$statusStyle = match ($status) {
    'approved' => [
        'bg' => '#e8f5e9',
        'color' => '#1b5e20',
        'border' => '#4caf50'
    ],
    'pending' => [
        'bg' => '#fff8e1',
        'color' => '#8a6d3b',
        'border' => '#ff9800'
    ],
    'rejected' => [
        'bg' => '#ffebee',
        'color' => '#b71c1c',
        'border' => '#f44336'
    ],
    default => [
        'bg' => '#f5f5f5',
        'color' => '#555',
        'border' => '#999'
    ]
};
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
.receipt-card{
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.receipt-header{
    background:var(--accent);
    color:#fff;
    padding:20px;
}

.receipt-body{
    padding:20px;
}

.receipt-row{
    display:flex;
    justify-content:space-between;
    gap:15px;
    padding:12px 0;
    border-bottom:1px solid #eee;
    align-items:flex-start;
}

.receipt-label{
    font-weight:600;
    color:#555;
    flex:1;
    min-width:120px;
}

.receipt-value{
    color:#111;
    text-align:right;
    flex:2;
    word-break:break-word;
    overflow-wrap:anywhere;
}

/* MOBILE */
@media (max-width: 600px){
    .receipt-row{
        flex-direction:column;
        align-items:flex-start;
        gap:6px;
    }

    .receipt-value{
        text-align:left;
        width:100%;
    }
}

/* STATUS BOX */
.status-box{
    display:inline-block;
    padding:6px 12px;
    border-radius:12px;
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    border:1px solid;
}
</style>

<div class="container">

<div class="loan-form" style="max-width:750px;margin:auto;">

    <h2 style="text-align:center;margin-bottom:10px;">
        Withdrawal Receipt
    </h2>

    <div style="text-align:center;color:#666;margin-bottom:25px;">
        Transaction Reference:
        <strong><?= htmlspecialchars($receipt_id) ?></strong>
    </div>

    <div class="receipt-card">

        <div class="receipt-header">
            <h3 style="margin:0;">Transaction Details</h3>
        </div>

        <div class="receipt-body">

            <?php
            function row($label, $value) {
                echo "
                <div class='receipt-row'>
                    <span class='receipt-label'>$label</span>
                    <span class='receipt-value'>$value</span>
                </div>";
            }
            ?>

            <?php row("Receipt ID", htmlspecialchars($receipt_id)); ?>
            <?php row("Amount (USD)", "$" . number_format($withdrawal['amount'], 2)); ?>

            <?php if (!empty($withdrawal['receive_currency'])): ?>
                <?php row(
                    "Receive Amount",
                    number_format($withdrawal['receive_amount'], 2) . " " . htmlspecialchars($withdrawal['receive_currency'])
                ); ?>
            <?php endif; ?>

            <?php row(
                $withdrawal['method_head'] ?? 'Payment Method',
                htmlspecialchars($withdrawal['method'])
            ); ?>

            <?php row(
                $withdrawal['method_name_head'] ?? 'Account Name',
                htmlspecialchars($withdrawal['account_name'])
            ); ?>

            <?php row(
                $withdrawal['method_id_head'] ?? 'Account ID',
                htmlspecialchars($withdrawal['account_id'])
            ); ?>

            <!-- STATUS -->
            <div class="receipt-row">
                <span class="receipt-label">Status</span>
                <span class="receipt-value">
                    <span class="status-box"
                          style="
                            background: <?= $statusStyle['bg'] ?>;
                            color: <?= $statusStyle['color'] ?>;
                            border-color: <?= $statusStyle['border'] ?>;
                          ">
                        <?= htmlspecialchars($withdrawal['status']) ?>
                    </span>
                </span>
            </div>

            <?php row(
                "Date & Time (GMT+00:00)",
                date('F d, Y h:i A', strtotime($withdrawal['created_at'] . ' +7 hours'))
            ); ?>

            <?php row(
                "Email",
                htmlspecialchars($withdrawal['email'])
            ); ?>

            <div style="text-align:center;margin-top:25px;">
                <a href="dashboard" class="submit-btn" style="text-decoration:none;display:inline-block;">
                    Back to Dashboard
                </a>
            </div>

        </div>
    </div>

    <div style="
        margin-top:20px;
        text-align:center;
        font-size:13px;
        color:#777;
    ">
        This receipt confirms your withdrawal request has been submitted successfully.
    </div>

</div>

</div>

<?php include 'includes/footer.php'; ?>
