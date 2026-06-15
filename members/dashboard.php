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

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div id="dashboard" class="section active">

        <div class="cards-grid">

            <div class="card balance">
                <i class="fas fa-wallet"></i>
                <h3>Account Balance</h3>
                <p>USD <?= number_format($user['balance'] ?? 0, 2) ?></p>
            </div>

            <?php if ((int)$user['state_status'] === 1): ?>
            
            <a href="choose-state.php" style="text-decoration:none;color:inherit;">
                <div class="card loans" style="cursor:pointer;">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Choose Your State Of Origin</h3>
                    <p>Claim Allowance</p>
                </div>
            </a>
            
            <?php else: ?>
            
            <div class="card loans">
                <i class="fas fa-map-marker-alt"></i>
                <h3>State Of Origin</h3>
                <p><?= htmlspecialchars($user['state'] ?? 'Not Set') ?></p>
            </div>
            
            <?php endif; ?>

            <div class="card pending">
                <i class="fas fa-clock"></i>
                <h3>Pending Applications</h3>
                <p><?= $pending ?></p>
            </div>

            <div class="card approved">
                <i class="fas fa-check-circle"></i>
                <h3>Approved Loans</h3>
                <p><?= $approved ?></p>
            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
