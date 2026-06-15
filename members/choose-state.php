<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT state_status, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

if ((int)$user['state_status'] !== 1) {
    header('Location: dashboard.php');
    exit();
}

$message = '';

$regions = $pdo->query("
    SELECT DISTINCT region
    FROM state_allowances
    ORDER BY region
")->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $state = trim($_POST['state'] ?? '');

    if (empty($state)) {
        $message = "Please select a state.";
    } else {

        $stmt = $pdo->prepare("
            SELECT state, amount
            FROM state_allowances
            WHERE state = ?
            LIMIT 1
        ");
        $stmt->execute([$state]);
        $allowance = $stmt->fetch();

        if (!$allowance) {

            $message = "Invalid state selected.";

        } else {

            $amount = $allowance['amount'];

            $pdo->beginTransaction();

            try {

                $update = $pdo->prepare("
                    UPDATE users
                    SET
                        state = ?,
                        allowance_amount = ?,
                        balance = balance + ?,
                        state_status = 0
                    WHERE id = ?
                ");

                $update->execute([
                    $state,
                    $amount,
                    $amount,
                    $user_id
                ]);

                $pdo->commit();

                header("Location: dashboard.php");
                exit();

            } catch (Exception $e) {

                $pdo->rollBack();
                $message = "Something went wrong.";

            }
        }
    }
}

$states = $pdo->query("
    SELECT region, state, amount
    FROM state_allowances
    ORDER BY region, state
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Choose State</title>

<style>
body{
    font-family:Arial,sans-serif;
    background:#f5f5f5;
}

.container{
    max-width:600px;
    margin:50px auto;
    background:#fff;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

h2{
    text-align:center;
    margin-bottom:25px;
}

.form-group{
    margin-bottom:20px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:bold;
}

select{
    width:100%;
    padding:12px;
}

button{
    width:100%;
    padding:14px;
    background:#001f3f;
    color:#fff;
    border:none;
    cursor:pointer;
}

button:hover{
    background:#003366;
}

.alert{
    padding:10px;
    background:#ffe6e6;
    color:#c00;
    margin-bottom:15px;
}
</style>
</head>
<body>

<div class="container">

    <h2>Choose Your State Of Origin</h2>

    <?php if($message): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Region</label>

            <select id="region">
                <option value="">Select Region</option>

                <?php foreach($regions as $region): ?>
                    <option value="<?= htmlspecialchars($region) ?>">
                        <?= htmlspecialchars($region) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label>State</label>

            <select name="state" id="state" required>
                <option value="">Select State</option>
            </select>
        </div>

        <button type="submit">
            Confirm State & Claim Allowance
        </button>

    </form>

</div>

<script>

const states = <?= json_encode($states) ?>;

document.getElementById('region').addEventListener('change', function(){

    const region = this.value;

    let html = '<option value="">Select State</option>';

    states.forEach(function(item){

        if(item.region === region){

            html += `
                <option value="${item.state}">
                    ${item.state} - USD ${parseFloat(item.amount).toLocaleString()}
                </option>
            `;
        }

    });

    document.getElementById('state').innerHTML = html;

});
</script>

</body>
</html>
