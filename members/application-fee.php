<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* -----------------------------
   USER DATA
------------------------------*/
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$email = $user['email'] ?? '';

/* -----------------------------
   JOB APPLICATION DATA
------------------------------*/
$application_id   = $_GET['application_id'] ?? null;
$full_name        = $_GET['full_name'] ?? '';
$sector           = $_GET['sector'] ?? '';
$expected_salary  = $_GET['expected_salary'] ?? '';

/* -----------------------------
   COUNTRIES LIST
------------------------------*/
include 'includes/all-countries.php';

/* -----------------------------
   GEOLOCATION (IP-BASED)
------------------------------*/
function getUserCountry() {
    $ip = $_SERVER['REMOTE_ADDR'];

    $json = @file_get_contents("https://ipapi.co/{$ip}/json/");
    if ($json) {
        $data = json_decode($json, true);
        return $data['country_name'] ?? null;
    }

    return null;
}

$detected_country = getUserCountry();

/* fallback if not detected */
$geo_country = in_array($detected_country, $countries)
    ? $detected_country
    : 'United States';

/* -----------------------------
   REGION SETTINGS (JOB FEE)
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM region_settings
    WHERE country = ?
    LIMIT 1
");
$stmt->execute([$geo_country]);
$region = $stmt->fetch();

if (!$region) {
    $_SESSION['error'] = "No region settings configured for {$geo_country}";
    header("Location: dashboard");
    exit();
}

/* -----------------------------
   IGNORE LOCATION LOGIC
------------------------------*/
if (($region['ignore_location'] ?? 'no') === 'yes') {
    $country = !empty($region['alternate_country'])
        ? $region['alternate_country']
        : $geo_country;
} else {
    $country = $geo_country;
}

/* -----------------------------
   JOB FEE CONFIG
------------------------------*/
$stmt = $pdo->prepare("
    SELECT *
    FROM region_settings
    WHERE country = ?
    LIMIT 1
");
$stmt->execute([$country]);
$region = $stmt->fetch();

if (!$region) {
    $_SESSION['error'] = "No region settings configured for {$country}";
    header("Location: dashboard");
    exit();
}

$fee               = (float) $region['job_fee'];
$currency          = $region['currency'] ?? 'USD';

$use_external      = $region['use_external'] ?? 'no';
$external_name     = $region['job_external_name'] ?? 'External Payment';
$external_link     = $region['job_external_link'] ?? '';

$method            = $region['job_method'] ?? '';
$method_value      = $region['job_method_value'] ?? '';

$method_name       = $region['job_method_name'] ?? '';
$method_name_value = $region['job_method_name_value'] ?? '';

$method_id         = $region['job_method_id'] ?? '';
$method_id_value   = $region['job_method_id_value'] ?? '';

/* -----------------------------
   SAVE DEPOSIT
------------------------------*/
function saveDeposit(
    $pdo,
    $user_id,
    $email,
    $country,
    $amount,
    $currency,
    $is_external,
    $external_name,
    $external_link,
    $description = 'job application fee',
    $status = 'pending'
) {
    $stmt = $pdo->prepare("
        INSERT INTO deposits
        (
            user_id,
            email,
            amount,
            currency,
            country,
            status,
            is_external,
            external_name,
            external_link,
            description,
            created_at
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $user_id,
        $email,
        $amount,
        $currency,
        $country,
        $status,
        $is_external,
        $external_name,
        $external_link,
        $description
    ]);

    return $pdo->lastInsertId();
}

/* -----------------------------
   EXTERNAL PAYMENT HANDLER
------------------------------*/
if ($use_external === 'yes' && isset($_POST['proceed_external'])) {

    saveDeposit(
        $pdo,
        $user_id,
        $email,
        $country,
        $fee,
        $currency,
        'yes',
        $external_name,
        $external_link
    );

    $_SESSION['success'] = "Redirecting to {$external_name}";
    header("Location: " . $external_link);
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

<div class="loan-form">

    <h2 style="text-align:center;margin-bottom:20px;">
        Job Application Fee
    </h2>

    <div style="padding:15px;background:#fff8e1;border-left:5px solid #ff9800;border-radius:10px;margin-bottom:20px;">
        <strong>A processing fee is required to complete your job application.</strong>
    </div>

    <!-- APPLICATION SUMMARY -->
    <div style="background:#f9f9f9;padding:15px;border-radius:10px;margin-bottom:20px;">
        <p><strong>Full Name:</strong> <?= htmlspecialchars($full_name) ?></p>
        <p><strong>Sector:</strong> <?= htmlspecialchars($sector) ?></p>
        <p><strong>Expected Salary:</strong> <?= htmlspecialchars($expected_salary) ?></p>
    </div>

    <!-- PAYMENT DETAILS -->
    <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #eee;margin-bottom:20px;">

        <?php if ($use_external === 'yes'): ?>

            <p><strong>Provider:</strong> <?= htmlspecialchars($external_name) ?></p>
            <p><strong>Fee:</strong> <?= $currency . ' ' . number_format($fee, 2) ?></p>

        <?php else: ?>

            <p><strong>Fee:</strong> <?= $currency . ' ' . number_format($fee, 2) ?></p>

            <hr>

            <p><strong><?= htmlspecialchars($method) ?>:</strong> <?= htmlspecialchars($method_value) ?></p>
            <p><strong><?= htmlspecialchars($method_name) ?>:</strong> <?= htmlspecialchars($method_name_value) ?></p>
            <p><strong><?= htmlspecialchars($method_id) ?>:</strong> <?= htmlspecialchars($method_id_value) ?></p>

        <?php endif; ?>

    </div>

    <!-- FORM -->
    <?php if ($use_external === 'yes'): ?>

        <form method="POST">
            <button type="submit" name="proceed_external" class="submit-btn">
                Pay with <?= htmlspecialchars($external_name) ?>
            </button>
        </form>

    <?php else: ?>

        <form method="POST" action="submit-application-fee" enctype="multipart/form-data">

            <input type="hidden" name="application_id" value="<?= $application_id ?>">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="amount" value="<?= $fee ?>">
            <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
            <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">
            <input type="hidden" name="description" value="job application fee">

            <div class="form-group">
                <label>Upload Payment Proof</label>
                <input type="file" name="receipt" accept="image/*" required>
            </div>

            <button type="submit" class="submit-btn">
                Submit Payment
            </button>

        </form>

    <?php endif; ?>

</div>

</div>

<?php include 'includes/footer.php'; ?>
