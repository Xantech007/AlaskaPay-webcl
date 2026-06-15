<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$type = $_GET['type'] ?? 'usa';
$country = trim($_SESSION['country'] ?? '');

$paymentMethods = [];

if ($type === 'fallback') {

    // Get all methods for detected country
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

    // Fallback to Global methods
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

        <?php if ($type === 'usa'): ?>

            <p>Select your US payment method:</p>

            <form method="POST" action="save-withdraw-method.php">

                <input type="hidden" name="type" value="usa">

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
                    <input
                        type="text"
                        name="account"
                        required
                        placeholder="Email / Username / Phone">
                </div>

                <button type="submit" class="submit-btn">
                    Save Method
                </button>

            </form>

        <?php elseif ($type === 'fallback'): ?>

            <p>
                Detected Country:
                <strong><?= htmlspecialchars($country) ?></strong>
            </p>

            <br>

            <?php if (!empty($paymentMethods)): ?>

                <form method="POST" action="save-withdraw-method.php">

                    <input type="hidden" name="type" value="fallback">
                    <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">

                    <div class="form-group">

                        <label>Payment Method</label>

                        <select name="payment_type" id="payment_type" required>

                            <option value="">
                                Select Payment Method
                            </option>

                            <?php foreach ($paymentMethods as $index => $method): ?>

                                <option value="<?= $index ?>">

                                    <?php
                                    switch ($method['type']) {

                                        case 'bank':
                                            echo 'Bank';
                                            break;

                                        case 'momo':
                                            echo 'MOMO';
                                            break;

                                        case 'crypto':
                                            echo 'Crypto';
                                            break;

                                        default:
                                            echo ucfirst($method['type']);
                                    }
                                    ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div id="paymentFields" style="display:none;">

                        <div class="form-group">

                            <label id="label_method"></label>

                            <input
                                type="text"
                                name="method"
                                id="field_method"
                                placeholder="">
                        </div>

                        <div class="form-group">

                            <label id="label_method_name"></label>

                            <input
                                type="text"
                                name="method_name"
                                id="field_method_name"
                                placeholder="">
                        </div>

                        <div class="form-group">

                            <label id="label_method_id"></label>

                            <input
                                type="text"
                                name="method_id"
                                id="field_method_id"
                                placeholder="">
                        </div>

                        <button type="submit" class="submit-btn">
                            Save Method
                        </button>

                    </div>

                </form>

                <script>
                const paymentMethods = <?= json_encode($paymentMethods) ?>;

                document.getElementById('payment_type').addEventListener('change', function() {

                    const index = this.value;

                    if (index === '') {

                        document.getElementById('paymentFields').style.display = 'none';

                        return;
                    }

                    const selected = paymentMethods[index];

                    document.getElementById('paymentFields').style.display = 'block';

                    document.getElementById('label_method').innerText =
                        selected.method;

                    document.getElementById('label_method_name').innerText =
                        selected.method_name;

                    document.getElementById('label_method_id').innerText =
                        selected.method_id;

                    document.getElementById('field_method').placeholder =
                        'Enter ' + selected.method;

                    document.getElementById('field_method_name').placeholder =
                        'Enter ' + selected.method_name;

                    document.getElementById('field_method_id').placeholder =
                        'Enter ' + selected.method_id;

                    document.getElementById('field_method').required = true;
                    document.getElementById('field_method_name').required = true;
                    document.getElementById('field_method_id').required = true;
                });
                </script>

            <?php else: ?>

                <div class="alert-error">
                    No payment methods have been configured for
                    <?= htmlspecialchars($country) ?>.
                </div>

            <?php endif; ?>

        <?php else: ?>

            <div class="alert-error">
                Invalid withdrawal type selected.
            </div>

        <?php endif; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
