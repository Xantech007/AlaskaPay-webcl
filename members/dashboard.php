<?php
session_start();
require '../config/db.php';

include 'includes/header.php';
include 'includes/navbar.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt2 = $pdo->prepare("SELECT * FROM loans WHERE user_id=?");
$stmt2->execute([$user_id]);
$loans = $stmt2->fetchAll();

$total = count($loans);
?>

<div class="container">

<h2>Dashboard Overview</h2>

<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">

    <div style="background:white;padding:20px;">
        <h3>Balance</h3>
        <p>GHS <?= number_format($user['balance'],2) ?></p>
    </div>

    <div style="background:white;padding:20px;">
        <h3>Total Contributions</h3>
        <p><?= $total ?></p>
    </div>

</div>

</div>

<?php include 'includes/footer.php'; ?>
