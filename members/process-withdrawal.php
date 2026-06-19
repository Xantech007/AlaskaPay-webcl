<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: withdraw.php");
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
    header("Location: withdraw.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Verification Check
|--------------------------------------------------------------------------
*/
if ((int)$user['is_verified'] !== 2) {
    $_SESSION['error'] = "Your payment method is not approved yet.";
    header("Location: withdraw.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validate Amount
|--------------------------------------------------------------------------
*/
$amount = isset($_POST['amount'])
    ? (float)$_POST['amount']
    : 0;

if ($amount <= 0) {
    $_SESSION['error'] = "Invalid withdrawal amount.";
    header("Location: withdraw.php");
    exit();
}

$currentBalance = (float)$user['balance'];

if ($amount > $currentBalance) {
    $_SESSION['error'] = "Insufficient balance.";
    header("Location: withdraw.php");
    exit();
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
            method,
            account_name,
            account_id,
            status,
            created_at
        )
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "idssss",
        $user_id,
        $amount,
        $user['verified_method'],
        $user['verified_account_name'],
        $user['verified_account_id'],
        $status
    );

    $stmt->execute();
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | Deduct Balance
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

    /*
    |--------------------------------------------------------------------------
    | SUCCESS → DASHBOARD
    |--------------------------------------------------------------------------
    */
    $_SESSION['success_message'] =
        "Withdrawal request submitted successfully and is now pending review.";

    header("Location: dashboard.php");
    exit();

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION['error'] =
        "Unable to process withdrawal. Please try again.";

    header("Location: withdraw.php");
    exit();
}
