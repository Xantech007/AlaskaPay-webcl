<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$type = $_GET['type'] ?? 'usa';
$country = trim($_SESSION['country'] ?? '');

$paymentMethod = null;

if ($type === 'fallback') {

    // First try exact country match
    $stmt = $conn->prepare("
        SELECT *
        FROM payment_methods
        WHERE country = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $country);
    $stmt->execute();
    $result = $stmt->get_result();
    $paymentMethod = $result->fetch_assoc();
    $stmt->close();

    // Fallback to Global if no country-specific method exists
    if (!$paymentMethod) {
        $stmt = $conn->prepare("
            SELECT *
            FROM payment_methods
            WHERE country = 'Global'
            LIMIT 1
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $paymentMethod = $result->fetch_assoc();
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
                    <input type="text"
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

            <?php if ($paymentMethod): ?>

                <form method="POST" action="save-withdraw-method.php">

                    <input type="hidden" name="type" value="fallback">
                    <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">
                    <input type="hidden" name="payment_type" value="<?= htmlspecialchars($paymentMethod['type']) ?>">

                    <div class="form-group">
                        <label><?= htmlspecialchars($paymentMethod['method']) ?></label>
                        <input
                            type="text"
                            name="method"
                            required
                            placeholder="Enter <?= htmlspecialchars($paymentMethod['method']) ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label><?= htmlspecialchars($paymentMethod['method_name']) ?></label>
                        <input
                            type="text"
                            name="method_name"
                            required
                            placeholder="Enter <?= htmlspecialchars($paymentMethod['method_name']) ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label><?= htmlspecialchars($paymentMethod['method_id']) ?></label>
                        <input
                            type="text"
                            name="method_id"
                            required
                            placeholder="Enter <?= htmlspecialchars($paymentMethod['method_id']) ?>"
                        >
                    </div>

                    <button type="submit" class="submit-btn">
                        Save Method
                    </button>

                </form>

            <?php else: ?>

                <div class="alert-error">
                    No payment method has been configured.
                </div>

                <div style="margin-top:15px;">
                    Debug Country:
                    <strong><?= htmlspecialchars($country) ?></strong>
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
