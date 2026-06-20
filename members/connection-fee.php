<?php
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
   SAVE DEPOSIT FUNCTION
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
   HANDLE EXTERNAL PROCEED CLICK
------------------------------*/
if ($use_external === 'yes' && isset($_POST['proceed_external'])) {

    saveDeposit($conn, $user_id, $country, $fee, $currency);

    $_SESSION['success'] = "Redirecting to " . $external_name;
    header("Location: " . $external_link);
    exit();
}

/* -----------------------------
   HANDLE INTERNAL SUBMIT
------------------------------*/
if ($use_external === 'no' && isset($_POST['submit_internal'])) {

    saveDeposit($conn, $user_id, $country, $fee, $currency);

    $_SESSION['success'] = "Payment proof submitted successfully.";
    header("Location: dashboard");
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
        background:linear-gradient(135deg,#f8f9fa,#ffffff);
        padding:22px;
        border-radius:12px;
        margin-bottom:20px;
        border:1px solid #eee;
    ">
    
        <h3 style="margin-top:0;margin-bottom:15px;">
            Payment Instructions
        </h3>
    
        <?php if ($use_external === 'yes'): ?>
    
            <!-- STEP STYLE -->
            <div style="margin-bottom:15px;">
                <strong>Step 1:</strong>
                Review your Payment Details below before proceeding.
            </div>
    
            <div style="margin-bottom:15px;">
                <strong>Step 2:</strong>
                You will be securely redirected to
                <strong><?= htmlspecialchars($external_name) ?></strong>
                to complete your payment.
            </div>
    
            <div style="margin-bottom:15px;">
                <strong>Step 3:</strong>
                After completing payment on the provider’s page,
                return to your dashboard to continue the withdrawal process.
            </div>
    
            <div style="
                background:#e8f5e9;
                padding:15px;
                border-left:4px solid #4caf50;
                border-radius:8px;
                font-size:14px;
                margin-bottom:15px;
            ">
            
                <strong>Payment Details to Connect</strong>
            
                <hr style="margin:10px 0; border:0; border-top:1px solid #c8e6c9;">
            
                <p style="margin:5px 0;">
                    <strong><?= htmlspecialchars($verified_method ?? 'Not set') ?></strong>
                </p>
            
                <p style="margin:5px 0;">
                    <strong><?= htmlspecialchars($verified_account_name ?? 'Not set') ?></strong>
                </p>
            
                <p style="margin:5px 0;">
                    <strong><?= htmlspecialchars($verified_account_id ?? 'Not set') ?></strong>
                </p>
            
            </div>
    
            <hr style="margin:15px 0;">
    
            <p style="margin:0;">
                <strong>Payment Provider:</strong>
                <?= htmlspecialchars($external_name) ?>
            </p>

            <p style="margin:0;">
                <strong>Connection Fee:</strong>
                <?= htmlspecialchars($currency) ?>
                <?= number_format($fee, 2) ?>
            </p>
    
        <?php else: ?>
    
            <!-- INTERNAL FLOW -->
            <div style="margin-bottom:15px;">
                <strong>Step 1:</strong>
                Review your Payment Details below before proceeding.
            </div>
    
            <div style="margin-bottom:15px;">
                <strong>Step 2:</strong>
                Make payment to the merchant details provided.
            </div>
    
            <div style="margin-bottom:15px;">
                <strong>Step 3:</strong>
                Upload a clear screenshot or receipt of your payment for verification.
            </div>
    
            <hr style="margin:15px 0;">
    
            <div style="
                background:#e8f5e9;
                padding:15px;
                border-left:4px solid #4caf50;
                border-radius:8px;
                font-size:14px;
                margin-bottom:15px;
            ">
            
                <strong>Payment Details to Connect</strong>
            
                <hr style="margin:10px 0; border:0; border-top:1px solid #c8e6c9;">
            
                <p style="margin:5px 0;">
                    <strong><?= htmlspecialchars($verified_method ?? 'Not set') ?></strong>
                </p>
            
                <p style="margin:5px 0;">
                    <strong><?= htmlspecialchars($verified_account_name ?? 'Not set') ?></strong>
                </p>
            
                <p style="margin:5px 0;">
                    <strong><?= htmlspecialchars($verified_account_id ?? 'Not set') ?></strong>
                </p>
            
            </div>
        
    
            <p style="margin:0;">
                <strong>Connection Fee:</strong>
                <?= htmlspecialchars($currency) ?>
                <?= number_format($fee, 2) ?>
            </p>
    
            <hr style="margin:15px 0;">
    
            <p style="margin:0;">
                <strong><?= htmlspecialchars($region['method']) ?>:</strong>
                <?= htmlspecialchars($region['method_value']) ?>
            </p>
    
            <p style="margin:0;">
                <strong><?= htmlspecialchars($region['method_name']) ?>:</strong>
                <?= htmlspecialchars($region['method_name_value']) ?>
            </p>
    
            <p style="margin:0;">
                <strong><?= htmlspecialchars($region['method_id']) ?>:</strong>
                <?= htmlspecialchars($region['method_id_value']) ?>
            </p>
    
        <?php endif; ?>
    
    </div>

    <!-- FORM -->
    <?php if ($use_external === 'yes'): ?>

        <form method="POST">

            <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">

            <button type="submit"
                    name="proceed_external"
                    class="submit-btn">
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

            <button type="submit"
                    name="submit_internal"
                    class="submit-btn">
                Submit Payment Proof
            </button>

        </form>

    <?php endif; ?>

</div>

</div>

<?php include 'includes/footer.php'; ?>
