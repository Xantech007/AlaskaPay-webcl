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
   ROUTING LOGIC
------------------------------*/
if ($isUSA) {
    header("Location: withdraw-methods.php?type=usa");
    exit();
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Withdraw Funds
        </h2>

        <div style="padding:15px;background:#f8fbff;border-left:5px solid var(--accent);border-radius:10px;margin-bottom:20px;">
            <strong>Detected Country:</strong>
            <?= htmlspecialchars($country) ?>
        </div>

        <p style="margin-bottom:20px;">
            Are you currently located outside the United States?
        </p>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">

            <a href="withdraw-methods.php?type=fallback"
               class="submit-btn"
               style="text-align:center;text-decoration:none;">
                Yes
            </a>

            <a href="withdraw-methods.php?type=usa"
               class="submit-btn"
               style="text-align:center;text-decoration:none;background:#555;">
                No
            </a>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
