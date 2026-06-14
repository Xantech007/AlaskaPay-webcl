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

$stmt_loans = $pdo->prepare("SELECT * FROM loans WHERE user_id = ?");
$stmt_loans->execute([$user_id]);
$loans = $stmt_loans->fetchAll();

$total_loans = count($loans);
$pending = count(array_filter($loans, fn($l) => $l['status'] === 'pending'));
$approved = count(array_filter($loans, fn($l) => $l['status'] === 'approved'));

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div id="dashboard" class="section active">

        <div class="cards-grid">

            <div class="card balance">
                <i class="fas fa-wallet"></i>
                <h3>Account Balance</h3>
                <p>GHS <?= number_format($user['balance'] ?? 0, 2) ?></p>
            </div>

            <div class="card loans">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Total Loans Applied</h3>
                <p><?= $total_loans ?></p>
            </div>

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
