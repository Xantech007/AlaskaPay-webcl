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

$message = '';
$codeData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = trim($_POST['code'] ?? '');
    $confirm = isset($_POST['confirm']);

    if (empty($code)) {
        $message = '<div class="alert-error">Please enter a code.</div>';
    } else {

        $stmt = $pdo->prepare("
            SELECT * FROM allowance_codes
            WHERE code = ?
            LIMIT 1
        ");
        $stmt->execute([$code]);
        $codeData = $stmt->fetch();

        if (!$codeData) {
            $message = '<div class="alert-error">Invalid code.</div>';
        } elseif ((int)$codeData['is_used'] === 1) {
            $message = '<div class="alert-error">This code has already been used.</div>';
        } else {

            // Step 1: show preview first
            if (!$confirm) {
                $message = "<div style='background:#e7f3ff;padding:15px;border-radius:10px;margin-bottom:20px;'>
                    <strong>Valid Code Found!</strong><br>
                    Amount: USD " . number_format($codeData['amount'], 2) . "<br><br>
                    Confirm to add to your balance.
                </div>";
            } else {

                try {

                    $pdo->beginTransaction();

                    // 1. Update balance
                    $update = $pdo->prepare("
                        UPDATE users
                        SET balance = balance + ?
                        WHERE id = ?
                    ");
                    $update->execute([$codeData['amount'], $user_id]);

                    // 2. Mark code as used
                    $mark = $pdo->prepare("
                        UPDATE allowance_codes
                        SET is_used = 1,
                            used_by = ?,
                            used_at = NOW()
                        WHERE id = ?
                    ");
                    $mark->execute([$user_id, $codeData['id']]);

                    // 3. Log in state_claims
                    $log = $pdo->prepare("
                        INSERT INTO state_claims
                        (user_id, region, state, amount, code, description)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");

                    $log->execute([
                        $user_id,
                        'CODE',
                        'CODE REDEEM',
                        $codeData['amount'],
                        $codeData['code'],
                        'Redeemed Code'
                    ]);

                    $pdo->commit();

                    header("Location: dashboard.php");
                    exit();

                } catch (Exception $e) {
                    $pdo->rollBack();
                    $message = '<div class="alert-error">Unable to redeem code.</div>';
                }
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container">

    <div class="section active">

        <div class="loan-form">

            <h2 style="text-align:center;margin-bottom:30px;color:var(--primary);">
                Redeem Allowance Code
            </h2>

            <?= $message ?>

            <form method="POST">

                <div class="form-group">
                    <label>Enter Code</label>
                    <input type="text" name="code" required placeholder="Enter your code">
                </div>

                <?php if ($codeData && (int)$codeData['is_used'] === 0): ?>
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-gift"></i> Confirm Redeem USD <?= number_format($codeData['amount'], 2) ?>
                    </button>
                <?php else: ?>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-check-circle"></i> Validate Code
                    </button>
                <?php endif; ?>

            </form>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
