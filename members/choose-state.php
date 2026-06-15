<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit();
}

if ((int)$user['state_status'] !== 1) {
    header('Location: dashboard.php');
    exit();
}

$message = '';

$regions = $pdo->query("
    SELECT DISTINCT region
    FROM state_allowances
    ORDER BY region
")->fetchAll(PDO::FETCH_COLUMN);

$states = $pdo->query("
    SELECT region, state, amount
    FROM state_allowances
    ORDER BY region, state
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selected_state = trim($_POST['state'] ?? '');

    if (empty($selected_state)) {
        $message = '<div class="alert-error">Please select a state.</div>';
    } else {

        $stmt = $pdo->prepare("
            SELECT region, state, amount
            FROM state_allowances
            WHERE state = ?
            LIMIT 1
        ");
        $stmt->execute([$selected_state]);
        $allowance = $stmt->fetch();

        if (!$allowance) {
            $message = '<div class="alert-error">Invalid state selected.</div>';
        } else {

            try {

                $pdo->beginTransaction();

                // 🔒 prevent double claim
                $check = $pdo->prepare("
                    SELECT id FROM state_claims 
                    WHERE user_id = ? AND state = ?
                    LIMIT 1
                ");
                $check->execute([$user_id, $allowance['state']]);

                if ($check->fetch()) {
                    throw new Exception("You have already claimed this allowance.");
                }

                // 1. Update user
                $update = $pdo->prepare("
                    UPDATE users
                    SET
                        state = ?,
                        region = ?,
                        balance = balance + ?,
                        state_status = 0
                    WHERE id = ?
                    AND state_status = 1
                ");

                $update->execute([
                    $allowance['state'],
                    $allowance['region'],
                    $allowance['amount'],
                    $user_id
                ]);

                if ($update->rowCount() === 0) {
                    throw new Exception("State already processed or invalid.");
                }

                // 2. Log claim
                $log = $pdo->prepare("
                    INSERT INTO state_claims (user_id, region, state, amount, description)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $log->execute([
                    $user_id,
                    $allowance['region'],
                    $allowance['state'],
                    $allowance['amount'],
                    'State Allowance Claim'
                ]);

                $pdo->commit();

                // ✅ SUCCESS MESSAGE FOR DASHBOARD
                $_SESSION['success_message'] = "State successfully claimed! Allowance of USD " . number_format($allowance['amount'], 2) . " has been added to your balance.";

                header('Location: dashboard.php');
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                $message = '<div class="alert-error">' . $e->getMessage() . '</div>';
            }
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <div class="section active">

        <?= $message ?>

        <div class="loan-form">
        
            <h2 style="text-align:center;margin-bottom:30px;color:var(--primary);">
                Choose Your State Of Origin
            </h2>
        
            <!-- ✅ INFO BLOCK -->
            <div style="
                background:#f8fbff;
                border-left:5px solid var(--accent);
                padding:25px;
                border-radius:12px;
                margin-bottom:30px;
            ">
        
                <h3 style="color:var(--primary);margin-bottom:15px;">
                    <i class="fas fa-info-circle"></i>
                    State Allowance Program
                </h3>
        
                <p style="margin-bottom:15px;line-height:1.7;">
                    Welcome to the State Allowance Program. Eligible members are entitled to
                    receive a one-time state allowance based on their selected State of Origin.
                    The allowance amount varies by state and is automatically credited to your
                    account balance upon successful confirmation.
                </p>
        
                <div style="margin-top:20px;">
                    <h4 style="color:var(--primary);margin-bottom:10px;">
                        How It Works
                    </h4>
        
                    <ol style="padding-left:20px;line-height:1.8;">
                        <li>Select your region from the list provided.</li>
                        <li>Select your State of Origin.</li>
                        <li>Review the allowance amount displayed beside the state.</li>
                        <li>Click <strong>Confirm State & Claim Allowance</strong>.</li>
                        <li>The allowance will be credited instantly to your account balance.</li>
                    </ol>
                </div>
        
                <div style="
                    margin-top:20px;
                    background:#fff3cd;
                    color:#856404;
                    padding:15px;
                    border-radius:10px;
                ">
                    <strong>Important Notice:</strong><br>
                    Your State of Origin can only be selected once.
                    After claiming your allowance, you will not be able
                    to change your state or claim another state allowance.
                </div>
        
            </div>
            <!-- END INFO BLOCK -->
        
            <form method="POST">

                <div class="form-group">
                    <label>Select Region</label>
                    <select id="region" required>
                        <option value="">Choose Region</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= htmlspecialchars($region) ?>">
                                <?= htmlspecialchars($region) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Select State</label>
                    <select name="state" id="state" required>
                        <option value="">Choose State</option>
                    </select>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-check-circle"></i>
                    Confirm State & Claim Allowance
                </button>

            </form>

        </div>
    </div>
</div>

<script>
const states = <?= json_encode($states) ?>;

document.getElementById('region').addEventListener('change', function () {

    let region = this.value;
    let html = '<option value="">Choose State</option>';

    states.forEach(item => {
        if (item.region === region) {
            html += `<option value="${item.state}">
                ${item.state} - USD ${Number(item.amount).toLocaleString()}
            </option>`;
        }
    });

    document.getElementById('state').innerHTML = html;
});
</script>

<?php include 'includes/footer.php'; ?>
