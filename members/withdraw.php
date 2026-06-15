<?php
session_start();
require '../config/db.php';
require 'includes/countries.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* -----------------------------
   GEO DETECTION (IP API)
------------------------------*/
$ip = $_SERVER['REMOTE_ADDR'];
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));

$country = $geo->country ?? 'Unknown';
$isUSA = ($country === "United States");

/* Store detected country for next page */
$_SESSION['country'] = $country;

/* -----------------------------
   HANDLE AMOUNT SUBMISSION (STORE IN SESSION)
------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);

    if ($amount <= 0) {
        $error = "Please enter a valid withdrawal amount.";
    } else {
        $_SESSION['withdraw_amount'] = $amount;

        if ($isUSA) {
            header("Location: withdraw-methods.php?type=usa");
            exit();
        } else {
            header("Location: withdraw-methods.php?type=fallback");
            exit();
        }
    }
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

        <!-- WITHDRAW AMOUNT FIELD -->
        <form method="POST">

            <div class="form-group">
                <label>Amount to Withdraw</label>
                <input
                    type="number"
                    name="amount"
                    min="1"
                    step="0.01"
                    required
                    placeholder="Enter amount to withdraw">
            </div>

            <p style="margin-bottom:20px;">
                Are you currently located outside the United States?
            </p>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">

                <button type="submit"
                        class="submit-btn"
                        style="flex:1;">
                    Yes
                </button>

                <button type="submit"
                        name="usa"
                        value="1"
                        class="submit-btn"
                        style="flex:1;background:#555;">
                    No
                </button>

            </div>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
