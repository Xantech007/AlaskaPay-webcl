<?php
session_start();
require '../config/db.php';

try {

    // =========================
    // VALIDATE INPUT
    // =========================
    $id = (int) $_POST['id'];

    if ($id <= 0) {
        throw new Exception("Invalid user ID");
    }

    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $balance = (float) $_POST['balance'];
    $is_verified = (int) $_POST['is_verified'];
    $status = trim($_POST['status']);
    $verified_method = trim($_POST['verified_method']);
    $verified_account_name = trim($_POST['verified_account_name']);
    $verified_account_id = trim($_POST['verified_account_id']);

    if (!$email || !$full_name) {
        throw new Exception("Email and Full Name are required");
    }

// =========================
// CHECK IF USER EXISTS
// =========================
$checkUser = $pdo->prepare("
    SELECT id, is_verified
    FROM users
    WHERE id = ?
");
$checkUser->execute([$id]);

$currentUser = $checkUser->fetch(PDO::FETCH_ASSOC);

if (!$currentUser) {
    throw new Exception("User not found");
}

$currentVerifiedStatus = (int)$currentUser['is_verified'];

    // =========================
    // CHECK DUPLICATE EMAIL
    // =========================
    $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $checkEmail->execute([$email, $id]);

    if ($checkEmail->fetch()) {
        throw new Exception("Email already used by another user");
    }

    // =========================
    // UPDATE LOGIC
    // =========================
    $updateVerifiedAt =
        ($currentVerifiedStatus !== 2 && $is_verified === 2);
    
    if (!empty($_POST['password'])) {
    
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
        if ($updateVerifiedAt) {
    
            $stmt = $pdo->prepare("
                UPDATE users SET
                    email=?,
                    password=?,
                    full_name=?,
                    balance=?,
                    is_verified=?,
                    verified_at=DATE_ADD(NOW(), INTERVAL 3 HOUR),
                    status=?,
                    verified_method=?,
                    verified_account_name=?,
                    verified_account_id=?
                WHERE id=?
            ");
    
        } else {
    
            $stmt = $pdo->prepare("
                UPDATE users SET
                    email=?,
                    password=?,
                    full_name=?,
                    balance=?,
                    is_verified=?,
                    status=?,
                    verified_method=?,
                    verified_account_name=?,
                    verified_account_id=?
                WHERE id=?
            ");
    
        }
    
        $stmt->execute([
            $email,
            $password,
            $full_name,
            $balance,
            $is_verified,
            $status,
            $verified_method,
            $verified_account_name,
            $verified_account_id,
            $id
        ]);
    
    } else {
    
        if ($updateVerifiedAt) {
    
            $stmt = $pdo->prepare("
                UPDATE users SET
                    email=?,
                    full_name=?,
                    balance=?,
                    is_verified=?,
                    verified_at=DATE_ADD(NOW(), INTERVAL 3 HOUR),
                    status=?,
                    verified_method=?,
                    verified_account_name=?,
                    verified_account_id=?
                WHERE id=?
            ");
    
        } else {
    
            $stmt = $pdo->prepare("
                UPDATE users SET
                    email=?,
                    full_name=?,
                    balance=?,
                    is_verified=?,
                    status=?,
                    verified_method=?,
                    verified_account_name=?,
                    verified_account_id=?
                WHERE id=?
            ");
    
        }
    
        $stmt->execute([
            $email,
            $full_name,
            $balance,
            $is_verified,
            $status,
            $verified_method,
            $verified_account_name,
            $verified_account_id,
            $id
        ]);
    }

    $_SESSION['success'] = "User updated successfully";

} catch (PDOException $e) {

    // database-specific safe error handling
    if ($e->getCode() == 23000) {
        $_SESSION['error'] = "Duplicate entry detected (email already exists)";
    } else {
        $_SESSION['error'] = "Database error occurred. Please try again.";
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: users");
exit();
