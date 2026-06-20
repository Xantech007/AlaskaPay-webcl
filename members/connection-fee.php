<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdraw");
    exit();
}

$country = trim($_POST['country'] ?? $_SESSION['country'] ?? '');

/* -----------------------------
   REGION SETTINGS
------------------------------*/
$stmt = $conn->prepare("
    SELECT *
    FROM region_settings
    WHERE country = ?
    LIMIT 1
");

$stmt->bind_param("s", $country);
$stmt->execute();
$region = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$region) {
    $_SESSION['error'] = "No region settings configured for {$country}";
    header("Location: withdraw");
    exit();
}

$fee = (float) $region['fee'];
$currency = $region['currency'] ?? 'USD';

$use_external = $region['use_external'] ?? 'no';
$external_link = $region['external_link'] ?? '';
$external_name = $region['external_name'] ?? 'External Payment';

/* -----------------------------
   SAVE DEPOSIT RECORD FUNCTION
------------------------------*/
function saveDeposit($conn, $user_id, $country, $fee, $currency, $status = 'pending') {

    $stmt = $conn->prepare("
        INSERT INTO deposits
        (user_id, country, amount, currency, status, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "isdss",
        $user_id,
        $country,
        $fee,
        $currency,
        $status
    );

    $stmt->execute();
    $stmt->close();
}

/* -----------------------------
   EXTERNAL FLOW
------------------------------*/
if ($use_external === 'yes') {

    saveDeposit($conn, $user_id, $country, $fee, $currency);

    $_SESSION['success'] = "Redirecting to payment provider...";
    header("Location: " . $external_link);
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

<div class="loan-form">

    <h2 style="text-align:center;margin-bottom:20px;">
        Connection Fee Payment
    </h2>

    <div style="
        padding:15px;
        background:#fff8e1;
        border-left:5px solid #ff9800;
        border-radius:10px;
        margin-bottom:20px;
    ">
        <strong>
            A connection fee is required before processing your withdrawal.
        </strong>
    </div>

    <!-- INSTRUCTIONS -->
    <div style="
        background:#f8f9fa;
        padding:20px;
        border-radius:10px;
        margin-bottom:20px;
    ">

        <h3>Payment Instructions</h3>

        <?php if ($use_external === 'yes'): ?>

            <p>
                You will complete your payment on:
                <strong><?= htmlspecialchars($external_name) ?></strong>
            </p>

            <p>
                Click <strong>Proceed</strong> to continue.
            </p>

        <?php else: ?>

            <p>
                Please transfer exactly the amount below and upload proof.
            </p>

            <hr style="margin:15px 0;">

            <p><strong>Country:</strong> <?= htmlspecialchars($country) ?></p>

            <p>
                <strong>Connection Fee:</strong>
                <?= htmlspecialchars($currency) ?>
                <?= number_format($fee, 2) ?>
            </p>

            <hr style="margin:15px 0;">

            <p>
                <strong><?= htmlspecialchars($region['method']) ?>:</strong>
                <?= htmlspecialchars($region['method_value']) ?>
            </p>

            <p>
                <strong><?= htmlspecialchars($region['method_name']) ?>:</strong>
                <?= htmlspecialchars($region['method_name_value']) ?>
            </p>

            <p>
                <strong><?= htmlspecialchars($region['method_id']) ?>:</strong>
                <?= htmlspecialchars($region['method_id_value']) ?>
            </p>

        <?php endif; ?>

    </div>

    <!-- FORM -->
    <?php if ($use_external === 'yes'): ?>

        <form method="POST">

            <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">

            <button type="submit" class="submit-btn">
                Proceed to <?= htmlspecialchars($external_name) ?>
            </button>

        </form>

    <?php else: ?>

        <form method="POST"
              action="submit-connection-fee"
              enctype="multipart/form-data">

            <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">

            <div class="form-group">
                <label>Upload Payment Receipt</label>
                <input type="file" name="receipt" accept="image/*" required>
            </div>

            <button type="submit" class="submit-btn">
                Submit Payment Proof
            </button>

        </form>

    <?php endif; ?>

</div>

</div>

<?php include 'includes/footer.php'; ?>
