<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET USER VERIFICATION STATUS
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        balance,
        is_verified,
        verified_method,
        verified_account_name,
        verified_account_id
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

$error = '';

/*
|--------------------------------------------------------------------------
| PROCESS WITHDRAWAL
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ((int)$user['is_verified'] !== 2) {
        $error = "Your payment method is not approved.";
    } else {

        $amount = (float)($_POST['amount'] ?? 0);

        if ($amount <= 0) {
            $error = "Enter a valid amount.";
        } elseif ($amount > $user['balance']) {
            $error = "Insufficient balance.";
        } else {

            $stmt = $conn->prepare("
                INSERT INTO withdrawals (
                    user_id,
                    amount,
                    payment_method,
                    account_name,
                    account_id,
                    status
                )
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->bind_param(
                "idsss",
                $user_id,
                $amount,
                $user['verified_method'],
                $user['verified_account_name'],
                $user['verified_account_id']
            );

            if ($stmt->execute()) {

                $_SESSION['success'] =
                    "Withdrawal request submitted successfully. Status: Pending Review.";

                header("Location: withdraw.php");
                exit();
            }

            $stmt->close();

            $error = "Unable to submit withdrawal request.";
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

        <?php if ($success): ?>
            <div class="alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- VERIFICATION STATUS -->

        <?php if ((int)$user['is_verified'] === 0): ?>

            <div style="
                padding:15px;
                background:#fff8e1;
                border-left:4px solid orange;
                border-radius:8px;
                margin-bottom:20px;
            ">
                <strong>Payment Method Not Connected</strong>

                <p style="margin-top:10px;">
                    Before withdrawals can be processed, you must make the
                    one-time connection payment and link your withdrawal
                    account.
                </p>

                <a href="connection-fee.php"
                   class="submit-btn"
                   style="display:inline-block;text-decoration:none;">
                    Connect Payment Method
                </a>
            </div>

        <?php elseif ((int)$user['is_verified'] === 1): ?>

            <div style="
                padding:15px;
                background:#e3f2fd;
                border-left:4px solid #2196f3;
                border-radius:8px;
                margin-bottom:20px;
            ">
                <strong>Verification Pending</strong>

                <p style="margin-top:10px;">
                    Your payment method is currently under review.
                </p>

                <p>
                    If you believe your previous submission was incorrect,
                    you may complete the connection process again.
                </p>

                <a href="connection-fee.php"
                   class="submit-btn"
                   style="display:inline-block;text-decoration:none;">
                    Submit Again
                </a>
            </div>

        <?php elseif ((int)$user['is_verified'] === 2): ?>

            <div style="
                padding:15px;
                background:#e8f5e9;
                border-left:4px solid green;
                border-radius:8px;
                margin-bottom:20px;
            ">
                <strong>Payment Method Approved</strong>

                <hr style="margin:10px 0;">

                <p>
                    <strong>Method:</strong>
                    <?= htmlspecialchars($user['verified_method']) ?>
                </p>

                <p>
                    <strong>Account Name:</strong>
                    <?= htmlspecialchars($user['verified_account_name']) ?>
                </p>

                <p>
                    <strong>Account ID:</strong>
                    <?= htmlspecialchars($user['verified_account_id']) ?>
                </p>
            </div>

            <!-- WITHDRAWAL FORM -->

            <form method="POST">

                <div class="form-group">
                    <label>Available Balance</label>
                    <input
                        type="text"
                        value="$<?= number_format($user['balance'], 2) ?>"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label>Amount to Withdraw</label>
                    <input
                        type="number"
                        name="amount"
                        min="1"
                        max="<?= $user['balance'] ?>"
                        step="0.01"
                        required
                    >
                </div>

                <button type="submit" class="submit-btn">
                    Submit Withdrawal Request
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
