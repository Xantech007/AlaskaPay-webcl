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
        
            <!-- BALANCE -->
            <div class="card" style="background:#1f2a44;color:#fff;">
                <i class="fas fa-wallet"></i>
                <h3>Account Balance</h3>
                <p>USD <?= number_format($user['balance'] ?? 0, 2) ?></p>
            </div>
        
            <?php if ((int)$user['state_status'] === 1): ?>
        
                <a href="choose-state.php" style="text-decoration:none;">
                    <div class="card" style="background:#6f42c1;color:#fff;cursor:pointer;">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Choose Your State Of Origin</h3>
                        <p>Claim Allowance</p>
                    </div>
                </a>
        
            <?php else: ?>
        
                <div class="card" style="background:#6c757d;color:#fff;">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>State Of Origin</h3>
                    <p><?= htmlspecialchars($user['state'] ?? 'Not Set') ?></p>
                </div>
        
            <?php endif; ?>
        
            <!-- REDEEM -->
            <a href="redeem-code.php" style="text-decoration:none;">
                <div class="card" style="background:#f39c12;color:#fff;cursor:pointer;">
                    <i class="fas fa-gift"></i>
                    <h3>Redeem Monthly Allowance</h3>
                    <p>Redeem Code</p>
                </div>
            </a>
        
            <!-- WITHDRAW -->
            <a href="withdraw.php" style="text-decoration:none;">
                <div class="card" style="background:#28a745;color:#fff;cursor:pointer;">
                    <i class="fas fa-money-bill-transfer"></i>
                    <h3>Withdraw Funds</h3>
                    <p>Withdraw</p>
                </div>
            </a>
        
            <!-- HISTORY -->
            <a href="history.php" style="text-decoration:none;">
                <div class="card" style="background:#17a2b8;color:#fff;cursor:pointer;">
                    <i class="fas fa-history"></i>
                    <h3>History</h3>
                    <p>View</p>
                </div>
            </a>
        
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
