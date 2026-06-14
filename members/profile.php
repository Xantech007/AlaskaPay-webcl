<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

<div class="section active profile-card">

<div class="profile-avatar">
<?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 2)) ?>
</div>

<h2><?= htmlspecialchars($user['full_name']) ?></h2>

<p>Email: <?= $user['email'] ?></p>
<p>Phone: <?= $user['phone'] ?></p>
<p>Balance: GHS <?= number_format($user['balance'], 2) ?></p>

</div>

</div>

<?php include 'includes/footer.php'; ?>
