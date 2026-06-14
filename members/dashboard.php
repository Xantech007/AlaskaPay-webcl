<?php
// members/dashboard.php - Alaska Cash Wallet System

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

try {

    // USER DATA ONLY (CLEANED)
    $stmt = $pdo->prepare("
        SELECT id, username, email, full_name, phone, balance, is_verified, created_at
        FROM users
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: ../login.php');
        exit();
    }

    // STATUS
    $status = match ((int)$user['is_verified']) {
        0 => 'Not Verified',
        1 => 'Pending',
        2 => 'Verified',
        default => 'Unknown'
    };

    // UPDATE PROFILE
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $update = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
        if ($update->execute([$full_name, $phone, $user_id])) {
            $message = "<div class='alert-success'>Profile updated successfully!</div>";
            $user['full_name'] = $full_name;
            $user['phone'] = $phone;
        }
    }

    // CHANGE PASSWORD
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_new'];

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();

        if (password_verify($current, $row['password'])) {
            if ($new === $confirm && strlen($new) >= 8) {
                $hash = password_hash($new, PASSWORD_DEFAULT);

                $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->execute([$hash, $user_id]);

                $message = "<div class='alert-success'>Password changed successfully!</div>";
            } else {
                $message = "<div class='alert-error'>Passwords do not match or too short.</div>";
            }
        } else {
            $message = "<div class='alert-error'>Incorrect current password.</div>";
        }
    }

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
