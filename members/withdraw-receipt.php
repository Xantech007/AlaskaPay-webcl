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

/*
|--------------------------------------------------------------------------
| Get Withdrawal
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        w.*,
        u.email
    FROM withdrawals w
    INNER JOIN users u ON u.id = w.user_id
    WHERE w.id = ?
    AND w.user_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$withdrawal = $result->fetch_assoc();
$stmt->close();

if (!$withdrawal) {
    die("Receipt not found.");
}

$receipt_id = "#" . ($withdrawal['id'] * 27395);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Withdrawal Receipt</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f9;
    margin:0;
    padding:30px;
}

.receipt{
    max-width:700px;
    margin:auto;
    background:#fff;
    border-radius:10px;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
    overflow:hidden;
}

.header{
    background:#0d6efd;
    color:#fff;
    padding:25px;
}

.header h2{
    margin:0;
}

.content{
    padding:25px;
}

.row{
    display:flex;
    justify-content:space-between;
    padding:12px 0;
    border-bottom:1px solid #eee;
}

.row:last-child{
    border-bottom:none;
}

.label{
    font-weight:600;
    color:#555;
}

.value{
    color:#111;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
    text-transform:uppercase;
}

.pending{
    background:#fff3cd;
    color:#856404;
}

.completed{
    background:#d1e7dd;
    color:#0f5132;
}

.rejected{
    background:#f8d7da;
    color:#842029;
}

.footer{
    text-align:center;
    padding:20px;
    color:#777;
    font-size:13px;
    background:#fafafa;
}

.btn{
    display:inline-block;
    margin-top:20px;
    padding:10px 20px;
    background:#0d6efd;
    color:#fff;
    text-decoration:none;
    border-radius:5px;
}
</style>
</head>
<body>

<div class="receipt">

    <div class="header">
        <h2>Withdrawal Receipt</h2>
        <p>Transaction Reference: <?= htmlspecialchars($receipt_id) ?></p>
    </div>

    <div class="content">

        <div class="row">
            <span class="label">Receipt ID</span>
            <span class="value"><?= htmlspecialchars($receipt_id) ?></span>
        </div>

        <div class="row">
            <span class="label">Amount (USD)</span>
            <span class="value">$<?= number_format($withdrawal['amount'], 2) ?></span>
        </div>

        <?php if (!empty($withdrawal['receive_currency'])): ?>
        <div class="row">
            <span class="label">Receive Amount</span>
            <span class="value">
                <?= number_format($withdrawal['receive_amount'], 2) ?>
                <?= htmlspecialchars($withdrawal['receive_currency']) ?>
            </span>
        </div>
        <?php endif; ?>

        <div class="row">
            <span class="label">
                <?= htmlspecialchars($withdrawal['method_head'] ?? 'Payment Method') ?>
            </span>
            <span class="value">
                <?= htmlspecialchars($withdrawal['method']) ?>
            </span>
        </div>
        
        <div class="row">
            <span class="label">
                <?= htmlspecialchars($withdrawal['method_name_head'] ?? 'Account Name') ?>
            </span>
            <span class="value">
                <?= htmlspecialchars($withdrawal['account_name']) ?>
            </span>
        </div>
        
        <div class="row">
            <span class="label">
                <?= htmlspecialchars($withdrawal['method_id_head'] ?? 'Account ID') ?>
            </span>
            <span class="value">
                <?= htmlspecialchars($withdrawal['account_id']) ?>
            </span>
        </div>

        <div class="row">
            <span class="label">Status</span>
            <span class="value">
                <span class="status <?= strtolower($withdrawal['status']) ?>">
                    <?= htmlspecialchars($withdrawal['status']) ?>
                </span>
            </span>
        </div>

        <div class="row">
            <span class="label">Date & Time</span>
            <span class="value">
                <?= date('F d, Y h:i A', strtotime($withdrawal['created_at'])) ?>
            </span>
        </div>

        <div class="row">
            <span class="label">Email</span>
            <span class="value"><?= htmlspecialchars($withdrawal['email']) ?></span>
        </div>

        <center>
            <a href="withdraw" class="btn">Back</a>
        </center>

    </div>

    <div class="footer">
        This receipt confirms that your withdrawal request has been submitted successfully.
    </div>

</div>

</body>
</html>
