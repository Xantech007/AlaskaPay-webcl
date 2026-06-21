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
    header('Location: logout.php');
    exit();
}

include 'includes/header.php';
include 'includes/navbar.php';

/*
|--------------------------------------------------------------------------
| AI Models
|--------------------------------------------------------------------------
*/
$aiModels = [
    [
        'name' => 'ChatGPT',
        'logo' => '../assets/ai-logo/chatgpt.png',
        'url'  => 'https://chat.openai.com/'
    ],
    [
        'name' => 'Gemini',
        'logo' => '../assets/ai-logo/gemini.png',
        'url'  => 'https://gemini.google.com/'
    ],
    [
        'name' => 'Claude',
        'logo' => '../assets/ai-logo/claude.png',
        'url'  => 'https://claude.ai/'
    ],
    [
        'name' => 'Grok',
        'logo' => '../assets/ai-logo/grok.png',
        'url'  => 'https://grok.com/'
    ],
    [
        'name' => 'DeepSeek',
        'logo' => '../assets/ai-logo/deepseek.png',
        'url'  => 'https://chat.deepseek.com/'
    ]
];
?>

<div class="container">

    <div class="section active">

        <div class="loan-form">

            <h2 style="text-align:center;margin-bottom:20px;color:var(--primary);">
                Select AI Model
            </h2>

            <div style="
                background:#f8fbff;
                border-left:5px solid var(--accent);
                padding:20px;
                border-radius:12px;
                margin-bottom:30px;
            ">
                <h3 style="margin-bottom:10px;color:var(--primary);">
                    <i class="fas fa-robot"></i>
                    AI Assistant Center
                </h3>

                <p style="line-height:1.8;">
                    Choose your preferred AI assistant below.
                    Click any AI model to launch it instantly.
                </p>
            </div>

            <div class="ai-grid">

                <?php foreach ($aiModels as $ai): ?>

                    <a href="<?= htmlspecialchars($ai['url']) ?>"
                       target="_blank"
                       class="ai-card">

                        <img src="<?= htmlspecialchars($ai['logo']) ?>"
                             alt="<?= htmlspecialchars($ai['name']) ?>">

                        <h3><?= htmlspecialchars($ai['name']) ?></h3>

                        <span>
                            Launch AI
                            <i class="fas fa-arrow-right"></i>
                        </span>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

</div>

<style>
.ai-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.ai-card{
    text-decoration:none;
    background:#fff;
    border-radius:15px;
    padding:25px;
    text-align:center;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    transition:.3s ease;
    border:1px solid #eee;
}

.ai-card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.12);
}

.ai-card img{
    width:80px;
    height:80px;
    object-fit:contain;
    margin-bottom:15px;
}

.ai-card h3{
    color:var(--primary);
    margin-bottom:10px;
}

.ai-card span{
    color:var(--accent);
    font-weight:600;
}
</style>

<?php include 'includes/footer.php'; ?>
