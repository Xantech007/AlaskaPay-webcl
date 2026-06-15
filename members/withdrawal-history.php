<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM loans WHERE user_id = ?");
$stmt->execute([$user_id]);
$loans = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

<div class="section active">

<h2 style="text-align:center;">Loan History</h2>

<div class="table-container">

<table>
<tr>
<th>ID</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>

<?php foreach ($loans as $loan): ?>
<tr>
<td>#<?= $loan['id'] ?></td>
<td>GHS <?= number_format($loan['amount'], 2) ?></td>
<td><?= $loan['status'] ?></td>
<td><?= $loan['created_at'] ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

</div>

</div>

<?php include 'includes/footer.php'; ?>
