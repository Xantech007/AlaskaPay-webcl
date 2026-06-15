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

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <!-- ✅ FLASH MESSAGE DISPLAY -->
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

            <a href="history.php" style="text-decoration:none;color:inherit;">
                <div class="card approved" style="cursor:pointer;">
                    <i class="fas fa-history" style="color:#34495e;"></i>
                    <h3>History</h3>
                    <p>View</p>
                </div>
            </a>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
