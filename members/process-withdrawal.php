<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdraw");
    exit();
}

/*
|--------------------------------------------------------------------------
| Get User
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
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: withdraw");
    exit();
}

/*
|--------------------------------------------------------------------------
| Verification Check
|--------------------------------------------------------------------------
*/
if ((int)$user['is_verified'] !== 2) {
    $_SESSION['error'] = "Your payment method is not approved yet.";
    header("Location: withdraw");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validate Amount
|--------------------------------------------------------------------------
*/
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

if ($amount <= 0) {
    $_SESSION['error'] = "Invalid withdrawal amount.";
    header("Location: withdraw");
    exit();
}

$currentBalance = (float)$user['balance'];

if ($amount > $currentBalance) {
    $_SESSION['error'] = "Insufficient balance.";
    header("Location: withdraw");
    exit();
}

/*
|--------------------------------------------------------------------------
| NEW: Payment Method Data (from form)
|--------------------------------------------------------------------------
*/
$method = !empty($_POST['method']) ? trim($_POST['method']) : $user['verified_method'];
$method_name = !empty($_POST['method_name']) ? trim($_POST['method_name']) : $user['verified_account_name'];
$method_id = !empty($_POST['method_id']) ? trim($_POST['method_id']) : $user['verified_account_id'];

/*
|--------------------------------------------------------------------------
| Currency Conversion Fields
|--------------------------------------------------------------------------
*/
$receive_currency = !empty($_POST['receive_currency'])
    ? trim($_POST['receive_currency'])
    : null;

$exchange_rate = isset($_POST['exchange_rate'])
    ? (float)$_POST['exchange_rate']
    : null;

$receive_amount = isset($_POST['receive_amount'])
    ? (float)$_POST['receive_amount']
    : null;

/*
|--------------------------------------------------------------------------
| Safety Fallback
|--------------------------------------------------------------------------
*/
if (
    !empty($receive_currency) &&
    $exchange_rate > 0 &&
    $receive_amount <= 0
) {
    $receive_amount = $amount * $exchange_rate;
}

/*
|--------------------------------------------------------------------------
| Transaction
|--------------------------------------------------------------------------
*/
$conn->begin_transaction();

try {

    $status = 'pending';

    /*
    |--------------------------------------------------------------------------
    | Insert Withdrawal Request
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO withdrawals (
            user_id,
            amount,
            receive_currency,
            exchange_rate,
            receive_amount,
            method,
            account_name,
            account_id,
            status,
            created_at
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "idsddssss",
        $user_id,
        $amount,
        $receive_currency,
        $exchange_rate,
        $receive_amount,
        $method,
        $method_name,
        $method_id,
        $status
    );

    $stmt->execute();
    $withdrawal_id = $conn->insert_id;
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Deduct User Balance
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        UPDATE users
        SET balance = balance - ?
        WHERE id = ?
    ");

    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    $_SESSION['success_message'] =
        "Withdrawal request submitted successfully and is now pending review.";

    header("Location: withdraw-receipt?id=" . $withdrawal_id);
    exit();

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION['error'] =
        "Unable to process withdrawal. Please try again.";

    header("Location: withdraw");
    exit();
}
