<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

<div class="section active">

<div class="loan-form">

<h2 style="text-align:center; margin-bottom:30px; color:var(--primary);">
Apply for a New Loan
</h2>

<form action="process_loan.php" method="POST">

<div class="form-group">
<label>Loan Amount</label>
<input type="number" name="amount" required>
</div>

<div class="form-group">
<label>Loan Term</label>
<input type="number" name="term" required>
</div>

<div class="form-group">
<label>Purpose</label>
<select name="purpose" required>
<option value="">Select</option>
<option>Personal</option>
<option>Business</option>
</select>
</div>

<button class="submit-btn">Submit Application</button>

</form>

</div>

</div>

</div>

<?php include 'includes/footer.php'; ?>
