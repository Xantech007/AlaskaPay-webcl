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
                <i class="fas fa-wallet"></i>
                <h3>Account Balance</h3>
                <p>USD <?= number_format($user['balance'] ?? 0, 2) ?></p>
            </div>

            <?php if ((int)$user['state_status'] === 1): ?>

                <a href="choose-state.php" style="text-decoration:none;color:inherit;">
                    <div class="card state" style="cursor:pointer;">
                        <i class="fas fa-map-marker-alt" style="color:#3498db;"></i>
                        <h3>Choose Your State Of Origin</h3>
                        <p>Claim Allowance</p>
                    </div>
                </a>

            <?php else: ?>

                <div class="card state">
                    <i class="fas fa-map-marker-alt" style="color:#3498db;"></i>
                    <h3>State Of Origin</h3>
                    <p><?= htmlspecialchars($user['state'] ?? 'Not Set') ?></p>
                </div>

            <?php endif; ?>

            <a href="withdraw.php" style="text-decoration:none;color:inherit;">
                <div class="card withdraw" style="cursor:pointer; position:relative;">
                    
                    <!-- POPUP WILL GO HERE -->
                    <div id="withdraw-toast-container"></div>
            
                    <i class="fas fa-money-bill-transfer" style="color:#e67e22;"></i>
                    <h3>Withdraw Funds to Local Wallet or Accounts</h3>
                    <p>Withdraw</p>
                </div>
            </a>

            <a href="history.php" style="text-decoration:none;color:inherit;">
                <div class="card history" style="cursor:pointer;">
                    <i class="fas fa-history" style="color:#34495e;"></i>
                    <h3>Transaction & Withdrawal History</h3>
                    <p>View</p>
                </div>
            </a>

            <a href="job-application.php" style="text-decoration:none;color:inherit;">
                <div class="card job" style="cursor:pointer; position:relative;">
            
                    <!-- POPUP CONTAINER (inside card only) -->
                    <div id="job-toast-container"></div>
            
                    <i class="fas fa-briefcase" style="color:#ef2c2c;"></i>
                    <h3>Apply for a Job in the United States</h3>
                    <p>Submit application</p>
                </div>
            </a>

            <a href="https://alaskafastcash.com" target="_blank" style="text-decoration:none;color:inherit;">
                <div class="card get-loan" style="cursor:pointer;">
                    <i class="fas fa-hand-holding-dollar" style="color:#9a22f8;"></i>
                    <h3>Get Loan</h3>
                    <p>Apply for a loan</p>
                </div>
            </a>

            <a href="https://chatgptfree.ai" target="_blank" style="text-decoration:none;color:inherit;">
                <div class="card access-ai" style="cursor:pointer;">
                    <i class="fas fa-robot" style="color:#ffd700;"></i>
                    <h3>All Al Apps in One Place-100% FR££</h3>
                    <p>Explore AI Tools</p>
                </div>
            </a>

        </div>

    </div>

</div>

<div id="toast-container"></div>

<?php include 'includes/footer.php'; ?>
