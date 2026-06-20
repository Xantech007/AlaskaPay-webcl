<?php
session_start();

<pre>
<?php print_r($withdrawal); ?>
</pre>

require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard");
    exit();
}

$withdrawal_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| Fetch Withdrawal
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT *
    FROM withdrawals
    WHERE id = ?
    AND user_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $withdrawal_id,
    $user_id
);

$stmt->execute();

$withdrawal =
    $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$withdrawal) {
    $_SESSION['error'] =
        "Withdrawal receipt not found.";

    header("Location: history");
    exit();
}

/*
|--------------------------------------------------------------------------
| Status Color
|--------------------------------------------------------------------------
*/
$statusColor =
    $withdrawal['status'] === 'approved'
        ? '#27ae60'
        : (
            $withdrawal['status'] === 'rejected'
            ? '#e74c3c'
            : '#f39c12'
        );

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>

.loan-form{
    max-width:700px;
    margin:auto;
}

.receipt-card{
    background:#fff;
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.08);
    border:1px solid #edf2f7;
}

.receipt-header{
    background:linear-gradient(135deg,#001f3f,#003366);
    color:#fff;
    text-align:center;
    padding:35px 25px;
}

.receipt-header i{
    font-size:70px;
    color:#4db8ff;
    margin-bottom:12px;
}

.receipt-header h2{
    margin:0;
    font-size:30px;
    font-weight:700;
}

.receipt-header p{
    margin-top:8px;
    opacity:.85;
}

.receipt-body{
    padding:30px;
}

.receipt-table{
    width:100%;
    border-collapse:collapse;
}

.receipt-table tr{
    border-bottom:1px solid #eef2f7;
}

.receipt-table tr:last-child{
    border-bottom:none;
}

.receipt-table td{
    padding:18px 0;
}

.receipt-table td:first-child{
    font-weight:600;
    color:#666;
}

.receipt-table td:last-child{
    text-align:right;
    font-weight:700;
    color:#2c3e50;
}

.receipt-id{
    color:#3498db;
    font-size:18px;
}

.amount-usd{
    color:#27ae60;
    font-size:24px;
}

.amount-receive{
    color:#9b59b6;
    font-size:22px;
}

.status-badge{
    display:inline-block;
    padding:8px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
    text-transform:uppercase;
}

.receipt-footer{
    padding:25px;
    background:#f8fbff;
    border-top:1px solid #edf2f7;
}

.receipt-actions{
    display:flex;
    gap:15px;
}

.receipt-actions a{
    flex:1;
    text-decoration:none;
    text-align:center;
}

@media (max-width:768px){

    .container{
        width:100% !important;
        padding:0 !important;
        margin:0 !important;
    }

    .loan-form{
        max-width:100%;
        min-height:85vh;
        display:flex;
        flex-direction:column;
        padding:0;
    }

    .receipt-card{
        min-height:85vh;
        border-radius:0;
        display:flex;
        flex-direction:column;
    }

    .receipt-body{
        flex:1;
        padding:25px;
    }

    .receipt-table td{
        display:block;
        width:100%;
        text-align:left !important;
        padding:6px 0;
    }

    .receipt-table tr{
        display:block;
        padding:16px 0;
    }

    .receipt-header{
        padding:40px 20px;
    }

    .receipt-header i{
        font-size:60px;
    }

    .receipt-header h2{
        font-size:26px;
    }

    .receipt-footer{
        margin-top:auto;
    }

    .receipt-actions{
        flex-direction:column;
    }

}

</style>


<div class="loan-form">

    <div class="receipt-card">

        <div class="receipt-header">

            <i class="fas fa-receipt"></i>

            <h2>Withdrawal Receipt</h2>

            <p>Transaction successfully recorded</p>

        </div>

        <div class="receipt-body">

            <table class="receipt-table">

                <tr>
                    <td>Receipt ID</td>
                    <td class="receipt-id">
                        #<?= ($withdrawal['id'] * 27395) ?>
                    </td>
                </tr>

                <tr>
                    <td>Amount (USD)</td>
                    <td class="amount-usd">
                        $<?= number_format($withdrawal['amount'], 2) ?>
                    </td>
                </tr>

                <?php if (!empty($withdrawal['receive_currency'])): ?>

                <tr>
                    <td>
                        Amount To Receive (<?= htmlspecialchars($withdrawal['receive_currency']) ?>)
                    </td>

                    <td class="amount-receive">
                        <?= htmlspecialchars($withdrawal['receive_currency']) ?>
                        <?= number_format($withdrawal['receive_amount'], 2) ?>
                    </td>
                </tr>

                <?php endif; ?>

                <tr>
                    <td>Status</td>

                    <td>
                        <span
                            class="status-badge"
                            style="
                                background:<?= $statusColor ?>22;
                                color:<?= $statusColor ?>;
                            ">
                            <?= ucfirst($withdrawal['status']) ?>
                        </span>
                    </td>
                </tr>

                <tr>
                    <td>Date & Time</td>

                    <td>
                        <?= date(
                            'd M Y h:i A',
                            strtotime($withdrawal['created_at'])
                        ) ?>
                    </td>
                </tr>

            </table>

        </div>

        <div class="receipt-footer">

            <div class="receipt-actions">

                <a href="dashboard"
                   class="submit-btn">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>

                <a href="history"
                   class="submit-btn"
                   style="background:#555;">
                    <i class="fas fa-history"></i>
                    Withdrawal History
                </a>

            </div>

        </div>

    </div>

</div>


<? php include 'includes/navbar.php'; ?>
