<?php
session_start();

echo '<pre>';

echo "METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "REFERER: " . ($_SERVER['HTTP_REFERER'] ?? 'NONE') . "\n";

echo "\nPOST:\n";
print_r($_POST);

echo "\nGET:\n";
print_r($_GET);

echo '</pre>';
exit;

require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdraw.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* -----------------------------
   RECEIVED DATA (SAFE HANDLING)
------------------------------*/
$country = trim($_POST['country'] ?? $_SESSION['country'] ?? '');
$type = $_POST['type'] ?? '';

$selected_type = $_POST['selected_type'] ?? '';

$withdraw_method = $_POST['method'] ?? '';
$withdraw_method_name = $_POST['method_name'] ?? '';
$withdraw_method_id = $_POST['method_id'] ?? '';

$account = $_POST['account'] ?? '';

/* --------------------------------
   FIX: GET WITHDRAW AMOUNT (IMPORTANT)
---------------------------------*/
$amount = $_SESSION['withdraw_amount'] ?? null;

if (!$amount || $amount <= 0) {
    die("Invalid withdrawal amount. Please go back and enter a valid amount.");
}

/* --------------------------------
   STORE FINAL WITHDRAW DATA
---------------------------------*/
$_SESSION['withdraw_data'] = [
    'amount' => $amount,
    'country' => $country,
    'type' => $type,
    'selected_type' => $selected_type,
    'withdraw_method' => $withdraw_method,
    'withdraw_method_name' => $withdraw_method_name,
    'withdraw_method_id' => $withdraw_method_id,
    'account' => $account
];

/* --------------------------------
   GET REGION SETTINGS
---------------------------------*/
$stmt = $conn->prepare("
    SELECT *
    FROM region_settings
    WHERE country = ?
    LIMIT 1
");

$stmt->bind_param("s", $country);
$stmt->execute();

$result = $stmt->get_result();
$region = $result->fetch_assoc();
$stmt->close();

if (!$region) {
    die("No region settings configured for {$country}");
}

$fee = (float) $region['fee'];
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Connection Fee Payment
        </h2>

        <div class="alert-error">
            Your withdrawal request requires a connection fee payment before processing can begin.
        </div>

        <div style="
            background:#f8f9fa;
            padding:20px;
            border-radius:10px;
            margin-top:20px;
            margin-bottom:20px;
        ">

            <h3>Payment Instructions</h3>

            <p>
                Please transfer exactly the amount shown below and upload proof of payment.
            </p>

            <hr style="margin:15px 0;">

            <p>
                <strong>Withdrawal Amount:</strong>
                <?= number_format($amount, 2) ?>
            </p>

            <p>
                <strong>Country:</strong>
                <?= htmlspecialchars($country) ?>
            </p>

            <p>
                <strong>Connection Fee:</strong>
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

        </div>

        <form method="POST"
              action="submit-connection-fee.php"
              enctype="multipart/form-data">

            <div class="form-group">

                <label>Upload Payment Receipt</label>

                <input type="file"
                       name="receipt"
                       accept="image/*"
                       required>

            </div>

            <button type="submit" class="submit-btn">
                Submit Payment Proof
            </button>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
