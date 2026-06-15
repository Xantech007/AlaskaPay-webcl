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
   FLASH SUCCESS MESSAGE
------------------------------*/
$message = '';

if (!empty($_SESSION['success_message'])) {
    $message = '
        <div class="alert-success" style="margin-bottom:20px;">
            ' . htmlspecialchars($_SESSION['success_message']) . '
        </div>
    ';
    unset($_SESSION['success_message']);
}

/* -----------------------------
   FETCH STATE CLAIM HISTORY
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM state_claims
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->execute([$user_id]);
$claims = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <?= $message ?>

    <div id="dashboard" class="section active">

        <div class="cards-grid">

            <div class="card balance">
                <i class="fas fa-wallet" style="color:#2ecc71;"></i>
                <h3>Account Balance</h3>
                <p>USD <?= number_format($user['balance'] ?? 0, 2) ?></p>
            </div>

            <?php if ((int)$user['state_status'] === 1): ?>

                <a href="choose-state.php" style="text-decoration:none;color:inherit;">
                    <div class="card loans" style="cursor:pointer;">
                        <i class="fas fa-map-marker-alt" style="color:#3498db;"></i>
                        <h3>Choose Your State Of Origin</h3>
                        <p>Claim Allowance</p>
                    </div>
                </a>

            <?php else: ?>

                <div class="card loans">
                    <i class="fas fa-map-marker-alt" style="color:#3498db;"></i>
                    <h3>State Of Origin</h3>
                    <p><?= htmlspecialchars($user['state'] ?? 'Not Set') ?></p>
                </div>

            <?php endif; ?>

            <a href="redeem-code.php" style="text-decoration:none;color:inherit;">
                <div class="card pending" style="cursor:pointer;">
                    <i class="fas fa-gift" style="color:#9b59b6;"></i>
                    <h3>Redeem Monthly Allowance</h3>
                    <p>Redeem Code</p>
                </div>
            </a>

            <a href="withdraw.php" style="text-decoration:none;color:inherit;">
                <div class="card approved" style="cursor:pointer;">
                    <i class="fas fa-money-bill-transfer" style="color:#e67e22;"></i>
                    <h3>Withdraw Funds</h3>
                    <p>Withdraw</p>
                </div>
            </a>

        </div>

        <!-- =========================
             HISTORY TABLE
        ========================== -->

        <div class="loan-form" style="margin-top:30px;">

            <h2 style="text-align:center;color:var(--primary);margin-bottom:20px;">
                <i class="fas fa-history"></i> State Claim History
            </h2>

            <?php if (count($claims) > 0): ?>

                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;">
                        
                        <thead style="background:#f1f1f1;">
                            <tr>
                                <th style="padding:12px;">#</th>
                                <th style="padding:12px;">Region</th>
                                <th style="padding:12px;">State</th>
                                <th style="padding:12px;">Amount</th>
                                <th style="padding:12px;">Description</th>
                                <th style="padding:12px;">Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($claims as $index => $claim): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:12px;"><?= $index + 1 ?></td>
                                    <td style="padding:12px;"><?= htmlspecialchars($claim['region']) ?></td>
                                    <td style="padding:12px;"><?= htmlspecialchars($claim['state']) ?></td>
                                    <td style="padding:12px;color:#2ecc71;font-weight:bold;">
                                        USD <?= number_format($claim['amount'], 2) ?>
                                    </td>
                                    <td style="padding:12px;">
                                        <?= htmlspecialchars($claim['description'] ?? 'State Claim') ?>
                                    </td>
                                    <td style="padding:12px;">
                                        <?= date('M d, Y H:i', strtotime($claim['created_at'] ?? $claim['id'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php else: ?>

                <div style="text-align:center;padding:30px;color:#777;">
                    No state allowance history found.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
