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

/* -----------------------------
   SUBMIT APPLICATION
------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['dob'] ?? null;

    $country_status = $_POST['country_status'] ?? '';
    $current_country = $_POST['current_country'] ?? null;
    $us_state = $_POST['us_state'] ?? null;

    $sector = $_POST['sector'] ?? '';
    $sub_sector = $_POST['sub_sector'] ?? null;

    $education = $_POST['education'] ?? null;
    $experience = $_POST['experience'] ?? 0;

    $employment_status = $_POST['employment_status'] ?? null;
    $salary = $_POST['salary'] ?? null;

    $skills = $_POST['skills'] ?? null;
    $cover_letter = $_POST['cover_letter'] ?? null;

    $visa_required = isset($_POST['visa_required']) ? 1 : 0;
    $relocation = isset($_POST['relocation']) ? 1 : 0;

    /* ------------------ RESUME UPLOAD ------------------ */
    $resume_path = null;

    if (!empty($_FILES['resume']['name'])) {
        $uploadDir = "../uploads/resumes/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["resume"]["name"]);
        $targetFile = $uploadDir . $fileName;

        move_uploaded_file($_FILES["resume"]["tmp_name"], $targetFile);

        $resume_path = $targetFile;
    }

    /* ------------------ INSERT ------------------ */
    $stmt = $pdo->prepare("
        INSERT INTO job_applications (
            user_id, full_name, email, phone, date_of_birth,
            country_status, current_country, us_state,
            sector, sub_sector,
            highest_education, years_of_experience,
            employment_status, expected_salary,
            skills, cover_letter, resume_path,
            visa_required, relocation_willing
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $user_id, $full_name, $email, $phone, $dob,
        $country_status, $current_country, $us_state,
        $sector, $sub_sector,
        $education, $experience,
        $employment_status, $salary,
        $skills, $cover_letter, $resume_path,
        $visa_required, $relocation
    ]);

    $_SESSION['success_message'] = "Application submitted successfully!";
    header("Location: job-application.php");
    exit();
}

/* -----------------------------
   SECTORS LIST
------------------------------*/
$sectors = [
    "Engineering","Software Engineering","Civil Engineering","Mechanical Engineering",
    "Electrical Engineering","Chemical Engineering","Aerospace Engineering",
    "Biomedical Engineering","Healthcare","Nursing","Medicine","Pharmacy","Dentistry",
    "Public Health","Medical Laboratory Science","Finance","Accounting","Banking",
    "Investment Banking","Insurance","Economics","Taxation","Education","Teaching",
    "Academic Research","Early Childhood Education","Administration","Marketing",
    "Digital Marketing","Social Media Management","Public Relations","Advertising",
    "Brand Management","Sales","E-commerce","Retail","Customer Service",
    "Business Development","Management","Human Resources","Recruitment",
    "Construction","Architecture","Real Estate","Urban Planning","Surveying",
    "Information Technology","Cybersecurity","Data Science","Artificial Intelligence",
    "Machine Learning","Cloud Computing","Network Administration","Agriculture",
    "Farming","Agribusiness","Food Processing","Horticulture","Transportation",
    "Logistics","Supply Chain Management","Aviation","Marine Transport",
    "Hospitality","Tourism","Hotel Management","Catering","Food & Beverage",
    "Security","Law Enforcement","Military","Private Security","Legal Services",
    "Law","Judiciary","Entertainment","Film Production","Music Industry",
    "Media & Journalism","Broadcasting","Sports Management","Fitness & Wellness",
    "Beauty & Cosmetics","Fashion Design","Textile Industry","Manufacturing",
    "Automotive","Oil & Gas","Energy","Renewable Energy","Mining",
    "Environmental Science","Non-Profit / NGO","Government Services","Civil Service",
    "Diplomacy","Consulting","Project Management","IT Support","Technical Support",
    "Call Center Operations"
];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">

    <h2 style="margin-bottom:20px;">Apply for a Job in the United States</h2>

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert-success">
            <?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="job-form">

        <h3>Personal Information</h3>

        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="date" name="dob">

        <h3>Location Status</h3>

        <select name="country_status" id="country_status" required>
            <option value="">-- Select Location Status --</option>
            <option value="in_us">Currently in United States</option>
            <option value="outside_us">Outside United States</option>
        </select>

        <div id="us_fields" style="display:none;">
            <input type="text" name="us_state" placeholder="US State (if in USA)">
        </div>

        <div id="outside_fields" style="display:none;">
            <input type="text" name="current_country" placeholder="Current Country">
        </div>

        <h3>Professional Details</h3>

        <select name="sector" required>
            <option value="">-- Select Sector --</option>
            <?php foreach ($sectors as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="sub_sector" placeholder="Specialization (optional)">
        <input type="text" name="education" placeholder="Highest Education">
        <input type="number" name="experience" placeholder="Years of Experience">

        <input type="text" name="employment_status" placeholder="Employment Status">
        <input type="text" name="salary" placeholder="Expected Salary (USD)">

        <textarea name="skills" placeholder="Skills"></textarea>
        <textarea name="cover_letter" placeholder="Cover Letter"></textarea>

        <h3>Additional Information</h3>

        <label>
            <input type="checkbox" name="visa_required">
            Will you require visa sponsorship?
        </label>

        <label>
            <input type="checkbox" name="relocation">
            Willing to relocate?
        </label>

        <h3>Upload Documents</h3>

        <input type="file" name="resume" required>

        <button type="submit">Submit Application</button>

    </form>
</div>

<script>
document.getElementById('country_status').addEventListener('change', function () {
    const us = document.getElementById('us_fields');
    const out = document.getElementById('outside_fields');

    if (this.value === 'in_us') {
        us.style.display = 'block';
        out.style.display = 'none';
    } else if (this.value === 'outside_us') {
        out.style.display = 'block';
        us.style.display = 'none';
    } else {
        us.style.display = 'none';
        out.style.display = 'none';
    }
});
</script>

<style>
.job-form {
    background:#fff;
    padding:25px;
    border-radius:10px;
    max-width:900px;
}

.job-form input,
.job-form select,
.job-form textarea {
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:6px;
}

.job-form button {
    background:#2c3e50;
    color:#fff;
    padding:12px;
    border:none;
    width:100%;
    border-radius:6px;
    cursor:pointer;
}

.job-form h3 {
    margin-top:20px;
}
</style>

<?php include 'includes/footer.php'; ?>
