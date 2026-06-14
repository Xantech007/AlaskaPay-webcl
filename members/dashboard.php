<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

try {

    $stmt = $pdo->prepare("
        SELECT username, email, full_name, phone, balance, is_verified,
               created_at
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

    // Verification status (same logic but rebranded)
    $verification_status = match ((int)$user['is_verified']) {
        0 => ['label' => 'Not Verified', 'color' => '#e74c3c'],
        1 => ['label' => 'Pending Review', 'color' => '#f39c12'],
        2 => ['label' => 'Verified Member', 'color' => '#27ae60'],
        default => ['label' => 'Unknown', 'color' => '#7f8c8d']
    };

    $can_join_network = ($user['is_verified'] == 2);

    // Fetch participation records (formerly loans)
    $stmt_participation = $pdo->prepare("
        SELECT * FROM loans
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt_participation->execute([$user_id]);
    $records = $stmt_participation->fetchAll();

    $total = count($records);
    $pending = count(array_filter($records, fn($r) => $r['status'] === 'pending'));
    $active = count(array_filter($records, fn($r) => $r['status'] === 'approved'));

} catch (Exception $e) {
    die("ERROR: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alaska Energy Network • Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    margin:0;
    font-family: Arial, sans-serif;
    background:#f4f7fb;
}

header {
    background:#003366;
    color:white;
    padding:25px;
    text-align:center;
}

.nav {
    display:flex;
    justify-content:center;
    gap:10px;
    background:white;
    padding:15px;
    position:sticky;
    top:0;
}

.nav button {
    padding:10px 18px;
    border:none;
    border-radius:20px;
    cursor:pointer;
}

.nav button.active {
    background:#003366;
    color:white;
}

.section { display:none; padding:30px; }
.section.active { display:block; }

.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}

.card {
    background:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.card h2 {
    margin:10px 0;
}

.badge {
    padding:8px 15px;
    border-radius:20px;
    color:white;
    display:inline-block;
}
</style>
</head>

<body>

<header>
    <h1>Alaska Energy Network</h1>
    <p>Welcome <?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></p>
</header>

<div class="nav">
    <button class="active" onclick="show('dashboard')">Overview</button>
    <button onclick="show('join')">Network Participation</button>
    <button onclick="show('history')">Activity History</button>
    <button onclick="show('profile')">Profile</button>
</div>

<div class="section active" id="dashboard">

    <div class="cards">
        <div class="card">
            <i class="fas fa-bolt"></i>
            <h3>Rewards Balance</h3>
            <h2>₦<?= number_format($user['balance'] ?? 0, 2) ?></h2>
        </div>

        <div class="card">
            <i class="fas fa-network-wired"></i>
            <h3>Total Contributions</h3>
            <h2><?= $total ?></h2>
        </div>

        <div class="card">
            <i class="fas fa-clock"></i>
            <h3>Pending Nodes</h3>
            <h2><?= $pending ?></h2>
        </div>

        <div class="card">
            <i class="fas fa-check-circle"></i>
            <h3>Active Nodes</h3>
            <h2><?= $active ?></h2>
        </div>
    </div>

</div>

<div class="section" id="join">

    <h2>Network Participation</h2>

    <?php if (!$can_join_network): ?>

        <div style="background:#fff3cd;padding:20px;border-radius:10px;">
            <h3>Verification Required</h3>
            <p>You must verify your account before joining the network.</p>
            <a href="verify-account.php">Start Verification</a>
        </div>

    <?php else: ?>

        <div style="background:white;padding:20px;border-radius:10px;">
            <h3>Start Contribution</h3>

            <form action="process_loan.php" method="POST">

                <label>Contribution Level</label>
                <input type="number" name="amount" required placeholder="Enter amount">

                <label>Duration (Months)</label>
                <input type="number" name="term" required>

                <button type="submit">Join Network Node</button>

            </form>
        </div>

    <?php endif; ?>

</div>

<div class="section" id="history">

    <h2>Activity History</h2>

    <table border="1" width="100%" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>

        <?php foreach ($records as $r): ?>
        <tr>
            <td>#<?= $r['id'] ?></td>
            <td>₦<?= number_format($r['amount'], 2) ?></td>
            <td><?= ucfirst($r['status']) ?></td>
            <td><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

<div class="section" id="profile">

    <h2>Profile</h2>

    <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
    <p><b>Status:</b> 
        <span class="badge" style="background:<?= $verification_status['color'] ?>">
            <?= $verification_status['label'] ?>
        </span>
    </p>
    <p><b>Member Since:</b> <?= $user['created_at'] ?></p>

</div>

<script>
function show(id){
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav button').forEach(b => b.classList.remove('active'));

    document.getElementById(id).classList.add('active');
    event.target.classList.add('active');
}
</script>

</body>
</html>
