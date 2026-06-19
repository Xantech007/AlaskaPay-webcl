<?php
if (isset($_POST['add_user'])) {
    require '../config/db.php';

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, full_name, phone, is_verified, status)
            VALUES (?, ?, ?, ?, 0, 'active')
        ");
        $stmt->execute([$email, $password, $full_name, $phone]);

        $_SESSION['success'] = "User created successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to create user";
    }

    header("Location: users.php");
    exit();
}
?>

<div class="modal fade" id="addUserModal">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">
    <div class="modal-header">
        <h5>Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

        <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

        <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>

        <input type="text" name="phone" class="form-control mb-2" placeholder="Phone">

    </div>

    <div class="modal-footer">
        <button name="add_user" class="btn btn-primary">Create</button>
    </div>
</form>

</div>
</div>
</div>
