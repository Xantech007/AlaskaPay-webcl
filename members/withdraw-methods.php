<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* GEO DETECTION */
$ip = $_SERVER['REMOTE_ADDR'];
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));

$country = $geo->country ?? 'Unknown';

$_SESSION['country'] = $country;
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:20px;">
            Withdraw Funds
        </h2>

        <!-- STEP 1 -->
        <div id="step1">

            <div style="padding:15px;background:#f8fbff;border-left:5px solid var(--accent);border-radius:10px;margin-bottom:20px;">
                <strong>Detected Country:</strong>
                <?= htmlspecialchars($country) ?>
            </div>

            <p style="margin-bottom:20px;">
                Are you currently located outside the United States?
            </p>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">

                <button class="submit-btn" onclick="loadWithdrawMethods('fallback')">
                    Yes
                </button>

                <button class="submit-btn" style="background:#555;" onclick="loadWithdrawMethods('usa')">
                    No
                </button>

            </div>

        </div>

        <!-- STEP 2 (DYNAMIC CONTENT) -->
        <div id="step2" style="display:none;"></div>

    </div>

</div>

<script>
function loadWithdrawMethods(type) {

    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';

    fetch('withdraw-methods.php?type=' + type)
        .then(res => res.text())
        .then(html => {
            document.getElementById('step2').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('step2').innerHTML =
                '<div class="alert-error">Failed to load form. Try again.</div>';
        });
}
</script>

<?php include 'includes/footer.php'; ?>
