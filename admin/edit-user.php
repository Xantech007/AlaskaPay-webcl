<?php
$editUsers = $pdo->query("SELECT * FROM users")->fetchAll();

foreach ($editUsers as $u):
?>

<?php
if (isset($_POST['update_user_' . $u['id']])) {

    try {
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $phone = $_POST['phone'];
        $balance = $_POST['balance'];
        $is_verified = $_POST['is_verified'];
        $status = $_POST['status'];
        $verified_method = $_POST['verified_method'];
        $verified_account_name = $_POST['verified_account_name'];
        $verified_account_id = $_POST['verified_account_id'];

        // password optional update
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$password, $u['id']]);
        }

        $stmt = $pdo->prepare("
            UPDATE users SET
                email=?,
                full_name=?,
                phone=?,
                balance=?,
                is_verified=?,
                status=?,
                verified_method=?,
                verified_account_name=?,
                verified_account_id=?
            WHERE id=?
        ");

        $stmt->execute([
            $email,
            $full_name,
            $phone,
            $balance,
            $is_verified,
            $status,
            $verified_method,
            $verified_account_name,
            $verified_account_id,
            $u['id']
        ]);

        $_SESSION['success'] = "User updated successfully";
        header("Location: users.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = "Update failed";
        header("Location: users.php");
        exit();
    }
}
?>

<div class="modal fade" id="editUserModal<?= $u['id'] ?>">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
    <h5>Edit User</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body row">

    <div class="col-md-6">
        <input name="email" value="<?= $u['email'] ?>" class="form-control mb-2">
        <input name="password" class="form-control mb-2" placeholder="New Password (optional)">
        <input name="full_name" value="<?= $u['full_name'] ?>" class="form-control mb-2">
        <input name="phone" value="<?= $u['phone'] ?>" class="form-control mb-2">
        <input name="balance" value="<?= $u['balance'] ?>" class="form-control mb-2">
    </div>

    <div class="col-md-6">
        <select name="is_verified" class="form-control mb-2">
            <option value="0" <?= $u['is_verified']==0?'selected':'' ?>>Not Verified</option>
            <option value="1" <?= $u['is_verified']==1?'selected':'' ?>>Pending</option>
            <option value="2" <?= $u['is_verified']==2?'selected':'' ?>>Verified</option>
        </select>

        <select name="status" class="form-control mb-2">
            <option value="active" <?= $u['status']=='active'?'selected':'' ?>>Active</option>
            <option value="suspended" <?= $u['status']=='suspended'?'selected':'' ?>>Suspended</option>
        </select>

        <input name="verified_method" value="<?= $u['verified_method'] ?>" class="form-control mb-2">
        <input name="verified_account_name" value="<?= $u['verified_account_name'] ?>" class="form-control mb-2">
        <input name="verified_account_id" value="<?= $u['verified_account_id'] ?>" class="form-control mb-2">
    </div>

</div>

<div class="modal-footer">
    <button name="update_user_<?= $u['id'] ?>" class="btn btn-success">Update</button>
</div>

</form>

</div>
</div>
</div>

<?php endforeach; ?>
