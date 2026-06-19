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
    header('Location: logout');
    exit();
}

$message = '';

if (!empty($_SESSION['success_message'])) {
    $message = '
        <div class="alert-success" style="margin-bottom:20px;">
            ' . htmlspecialchars($_SESSION['success_message']) . '
        </div>
    ';
    unset($_SESSION['success_message']);
}

if (!empty($_SESSION['error_message'])) {
    $message .= '
        <div class="alert-error" style="margin-bottom:20px;">
            ' . htmlspecialchars($_SESSION['error_message']) . '
        </div>
    ';
    unset($_SESSION['error_message']);
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <?= $message ?>

    <div class="loan-form">

        <h2 style="text-align:center;margin-bottom:25px;">
            <i class="fas fa-user-circle"></i>
            My Profile
        </h2>

        <form method="POST" action="update-profile">

            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($user['username']) ?>"
                    readonly
                    style="background:#f5f5f5;">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($user['email']) ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input
                    type="text"
                    name="full_name"
                    value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input
                    type="text"
                    name="phone"
                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>

            <hr style="margin:25px 0;">

            <h3 style="margin-bottom:15px;">
                Change Password
            </h3>

            <div class="form-group">
                <label>New Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Leave blank to keep current password">
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm new password">
            </div>

            <button type="submit" class="submit-btn">
                Update Profile
            </button>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
