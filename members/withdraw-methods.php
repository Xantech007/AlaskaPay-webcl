<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$type = $_GET['type'] ?? 'usa';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Link Withdrawal Method
        </h2>

        <?php if ($type === 'usa'): ?>

            <p>Select your US payment method:</p>

            <form method="POST" action="save-withdraw-method.php">

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="method" required>
                        <option value="paypal">PayPal</option>
                        <option value="cashapp">Cash App</option>
                        <option value="venmo">Venmo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Account Identifier</label>
                    <input type="text" name="account" required placeholder="Email / Username / Phone">
                </div>

                <button class="submit-btn">Save Method</button>

            </form>

        <?php else: ?>

            <p>You are outside the United States.</p>

            <p style="margin-bottom:15px;">
                You may still use PayPal as fallback or choose a local method.
            </p>

            <form method="POST" action="save-withdraw-method.php">

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="method" required>
                        <option value="paypal">PayPal (Recommended)</option>
                        <option value="local_bank">Local Bank Transfer</option>
                        <option value="crypto">Cryptocurrency</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Account Details</label>
                    <input type="text" name="account" required placeholder="Enter details">
                </div>

                <button class="submit-btn">Save Method</button>

            </form>

        <?php endif; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
