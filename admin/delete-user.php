<?php
$deleteUsers = $pdo->query("SELECT id, email FROM users")->fetchAll();

foreach ($deleteUsers as $u):
?>

<?php
if (isset($_POST['delete_user_' . $u['id']])) {

    try {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$u['id']]);
        $_SESSION['success'] = "User deleted successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = "Delete failed";
    }

    header("Location: users.php");
    exit();
}
?>

<div class="modal fade" id="deleteUserModal<?= $u['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
    <h5>Delete User</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <p>Are you sure you want to delete:</p>
    <strong><?= htmlspecialchars($u['email']) ?></strong>
</div>

<div class="modal-footer">
    <button name="delete_user_<?= $u['id'] ?>" class="btn btn-danger">Yes, Delete</button>
</div>

</form>

</div>
</div>
</div>

<?php endforeach; ?>
