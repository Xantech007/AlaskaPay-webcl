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
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

<div class="loan-form" style="max-width:750px;margin:auto;">

    <h2 style="text-align:center;margin-bottom:10px;">
        Withdrawal Receipt
    </h2>

    <div style="text-align:center;color:#666;margin-bottom:25px;">
        Transaction Reference: <strong><?= htmlspecialchars($receipt_id) ?></strong>
    </div>

    <!-- RECEIPT CARD -->
    <div style="
        background:#fff;
        border-radius:15px;
        overflow:hidden;
        box-shadow:0 5px 20px rgba(0,0,0,0.08);
    ">

        <!-- HEADER -->
        <div style="
            background:var(--accent);
            color:#fff;
            padding:20px;
        ">
            <h3 style="margin:0;">Transaction Details</h3>
        </div>

        <div style="padding:20px;">

            <?php
            function row($label, $value) {
                echo "
                <div style='
                    display:flex;
                    justify-content:space-between;
                    padding:12px 0;
                    border-bottom:1px solid #eee;
                '>
                    <span style='font-weight:600;color:#555;'>$label</span>
                    <span style='color:#111;text-align:right;max-width:60%;'>
                        $value
                    </span>
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

            <?php
            $status = strtolower($withdrawal['status']);
            $statusColor = match ($status) {
                'completed' => '#4caf50',
                'pending'   => '#ff9800',
                'rejected'  => '#f44336',
                default     => '#777'
            };
            ?>

            <div style="
                display:flex;
                justify-content:space-between;
                padding:12px 0;
                border-bottom:1px solid #eee;
            ">
                <span style="font-weight:600;color:#555;">Status</span>
                <span style="
                    padding:5px 12px;
                    border-radius:20px;
                    font-size:12px;
                    font-weight:bold;
                    background:<?= $statusColor ?>22;
                    color:<?= $statusColor ?>;
                    text-transform:uppercase;
                ">
                    <?= htmlspecialchars($withdrawal['status']) ?>
                </span>
            </div>

            <?php row(
                "Date & Time",
                date('F d, Y h:i A', strtotime($withdrawal['created_at']))
            ); ?>

            <?php row("Email", htmlspecialchars($withdrawal['email'])); ?>

            <!-- BUTTON -->
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
