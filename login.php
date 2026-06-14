<?php
// login.php - Alaska Energy Network Login

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require 'config/db.php';

$error = "";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: members/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {

        $error = "Email and password are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        try {

            $stmt = $pdo->prepare("
                SELECT
                    id,
                    username,
                    full_name,
                    email,
                    password,
                    state,
                    account_status
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {

                $error = "Invalid email or password.";

            } elseif ($user['account_status'] !== 'active') {

                $error = "Your account is currently pending verification.";

            } elseif (password_verify($password, $user['password'])) {

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['state']     = $user['state'];
                $_SESSION['logged_in'] = true;

                header("Location: members/dashboard.php");
                exit();

            } else {

                $error = "Invalid email or password.";

            }

        } catch (PDOException $e) {

            $error = "Something went wrong. Please try again later.";

            // DEBUG:
            // $error = $e->getMessage();

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sign In • Alaska Energy Network</title>

<style>

:root{
    --primary:#001f3f;
    --primary-light:#003366;
    --white:#ffffff;
    --light:#f8f9fa;
    --gray:#dddddd;
    --error:#e74c3c;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Segoe UI', Arial, sans-serif;
    background:linear-gradient(135deg,#001f3f,#003366);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.main-container{
    display:grid;
    grid-template-columns:1fr 1fr;
    width:90%;
    max-width:1000px;
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.col-left{
    background:
    linear-gradient(
        rgba(0,31,63,.92),
        rgba(0,51,102,.92)
    ),
    url('assets/register-bg.jpg');

    background-size:cover;
    background-position:center;

    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;

    padding:40px;
    color:white;
    text-align:center;
}

.col-left img{
    width:200px;
    margin-bottom:20px;
}

.col-left h1{
    font-size:2.3rem;
    margin-bottom:15px;
}

.col-left p{
    line-height:1.7;
}

.container{
    padding:50px 45px;
    background:#fff;
}

.container h2{
    text-align:center;
    color:var(--primary);
    margin:10px 0 35px;
    font-size:28px;
}

.form-group{
    position:relative;
    margin-bottom:28px;
}

.form-group input{
    width:100%;
    padding:18px 0 8px;
    border:none;
    border-bottom:2px solid var(--gray);
    outline:none;
    font-size:15px;
}

.form-group label{
    position:absolute;
    top:18px;
    left:0;
    color:#999;
    transition:.3s;
}

.form-group input:focus ~ label,
.form-group input:valid ~ label{
    top:-5px;
    font-size:13px;
    color:var(--primary);
    font-weight:600;
}

button{
    width:100%;
    padding:16px;
    background:var(--primary);
    color:white;
    border:none;
    border-radius:10px;
    font-size:17px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:var(--primary-light);
}

.alert-error{
    padding:15px;
    margin-bottom:20px;
    background:#fdf2f2;
    color:var(--error);
    border-radius:10px;
    text-align:center;
}

.register-link{
    text-align:center;
    margin-top:25px;
    color:#555;
    font-size:15px;
}

.register-link a{
    color:var(--primary);
    text-decoration:none;
    font-weight:600;
}

.register-link a:hover{
    text-decoration:underline;
}

@media(max-width:768px){

    .main-container{
        grid-template-columns:1fr;
    }

    .col-left{
        display:none;
    }
}

</style>

</head>

<body>

<div class="main-container">

    <div class="col-left">

        <img
            src="assets/logo-white.png"
            alt="Alaska Energy Network"
        >

        <h1>Welcome Back</h1>

        <p>
            Sign in to monitor your participation,
            view reward activity, manage withdrawals,
            and access your member dashboard.
        </p>

    </div>

    <div class="container">

        <h2>Member Login</h2>

        <?php if($error): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <input
                    type="email"
                    name="email"
                    required
                >
                <label>Email Address</label>
            </div>

            <div class="form-group">
                <input
                    type="password"
                    name="password"
                    required
                >
                <label>Password</label>
            </div>

            <button type="submit">
                Sign In
            </button>

        </form>

        <div class="register-link">
            Don't have an account?
            <a href="register.php">
                Join the Network
            </a>
        </div>
        
    </div>

</div>

</body>
</html>
