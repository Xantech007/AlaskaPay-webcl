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
   USER VERIFIED WITHDRAWAL DATA
------------------------------*/
$stmt = $conn->prepare("
    SELECT
        verified_method,
        verified_account_name,
        verified_account_id
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$verified_method = $userData['verified_method'] ?? '';
$verified_account_name = $userData['verified_account_name'] ?? '';
$verified_account_id = $userData['verified_account_id'] ?? '';

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
function saveDeposit($conn, $user_id, $country, $fee, $currency, $status = 'pending')
{
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
                A connection fee is required before processing your withdrawal request.
            </strong>
        </div>

        <!-- PAYMENT DETAILS -->
        <div style="
            background:#f8f9fa;
            padding:20px;
            border-radius:10px;
            margin-bottom:20px;
            border:1px solid #eee;
        ">

            <h3 style="margin-top:0;">
                Payment Information
            </h3>

            <p>
                <strong>Country:</strong>
                <?= htmlspecialchars($country) ?>
            </p>

            <p>
                <strong>Connection Fee:</strong>
                <?= htmlspecialchars($currency) ?>
                <?= number_format($fee, 2) ?>
            </p>

            <hr style="margin:15px 0;">

            <?php if ($use_external === 'yes'): ?>

                <div style="
                    background:#eef6ff;
                    padding:15px;
                    border-left:4px solid #2196f3;
                    border-radius:8px;
                ">
                    <strong>External Payment Provider</strong>

                    <p style="margin-top:10px;">
                        To complete your connection fee payment, click the
                        <strong>Continue</strong> button below.
                    </p>

                    <p>
                        You will be redirected to
                        <strong><?= htmlspecialchars($external_name) ?></strong>,
                        where you can securely complete your payment.
                    </p>

                    <p>
                        Once payment has been completed on the provider's page,
                        follow any instructions displayed there and return to your account if required.
                    </p>
                </div>

            <?php else: ?>

                <p>
                    Please send the exact amount shown above using the payment details below.
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

                <hr style="margin:15px 0;">

                <!-- VERIFIED WITHDRAWAL METHOD -->
                <div style="
                    background:#e8f5e9;
                    padding:15px;
                    border-left:4px solid #4caf50;
                    border-radius:8px;
                    margin-bottom:15px;
                ">

                    <strong style="display:block;margin-bottom:10px;">
                        Verified Withdrawal Method
                    </strong>

                    <p style="margin:6px 0;">
                        <strong>Method:</strong>
                        <?= htmlspecialchars($verified_method) ?>
                    </p>

                    <p style="margin:6px 0;">
                        <strong>Account Name:</strong>
                        <?= htmlspecialchars($verified_account_name) ?>
                    </p>

                    <p style="margin:6px 0;">
                        <strong>Account ID:</strong>
                        <?= htmlspecialchars($verified_account_id) ?>
                    </p>

                </div>

            <?php endif; ?>

        </div>

        <!-- EXTERNAL PAYMENT -->
        <?php if ($use_external === 'yes'): ?>

            <form method="POST">

                <input
                    type="hidden"
                    name="country"
                    value="<?= htmlspecialchars($country) ?>"
                >

                <button
                    type="submit"
                    name="proceed_external"
                    class="submit-btn"
                >
                    Continue to <?= htmlspecialchars($external_name) ?>
                </button>

            </form>

        <!-- INTERNAL PAYMENT -->
        <?php else: ?>

            <form method="POST"
                  action="submit-connection-fee"
                  enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="country"
                    value="<?= htmlspecialchars($country) ?>"
                >

                <div class="form-group">

                    <label>Upload Payment Receipt</label>

                    <input
                        type="file"
                        name="receipt"
                        accept="image/*"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="submit-btn"
                >
                    Submit Payment Proof
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
