<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* -----------------------------
   SLIDESHOW IMAGES
------------------------------*/
$slides = [
    "../assets/jobs-slideshow/1.png",
    "../assets/jobs-slideshow/2.png",
    "../assets/jobs-slideshow/3.png",
    "../assets/jobs-slideshow/4.png",
    "../assets/jobs-slideshow/5.png"
];

/* -----------------------------
   SECTORS (trimmed for readability in UI)
------------------------------*/
$sectors = [
    "Software Engineering","Civil Engineering","Mechanical Engineering","Electrical Engineering",
    "Healthcare","Nursing","Finance","Accounting","Banking","Education",
    "Marketing","Data Science","Cybersecurity","Artificial Intelligence",
    "Construction","Architecture","Human Resources","Logistics",
    "Hospitality","Law","Media & Journalism","Oil & Gas","Renewable Energy",
    "Government Services","Consulting","Call Center Operations"
];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
body {
    background: #f4f6fb;
}

/* ---------- SLIDESHOW ---------- */
.slideshow {
    width: 100%;
    height: 320px;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.slideshow img {
    width: 100%;
    height: 320px;
    object-fit: cover;
    position: absolute;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.slideshow img.active {
    opacity: 1;
}

/* ---------- CONTAINER ---------- */
.job-container {
    max-width: 1100px;
    margin: auto;
}

/* ---------- FORM CARD ---------- */
.job-card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

/* ---------- HEADINGS ---------- */
.job-card h2 {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 700;
}

.job-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 25px;
}

/* ---------- FORM GRID ---------- */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.form-grid-full {
    grid-column: 1 / -1;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ddd;
    outline: none;
    transition: 0.3s;
    background: #fff;
}

input:focus, select:focus, textarea:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74,144,226,0.1);
}

textarea {
    min-height: 100px;
    resize: vertical;
}

/* ---------- SECTION TITLES ---------- */
.section-title {
    grid-column: 1 / -1;
    margin-top: 15px;
    font-weight: 600;
    color: #333;
}

/* ---------- BUTTON ---------- */
button {
    grid-column: 1 / -1;
    padding: 14px;
    background: linear-gradient(135deg,#4a90e2,#6c5ce7);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

/* ---------- CHECKBOX ---------- */
.check-row {
    display: flex;
    gap: 20px;
    grid-column: 1 / -1;
}

/* ---------- RESPONSIVE ---------- */
@media(max-width: 768px){
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="job-container">

    <!-- SLIDESHOW -->
    <div class="slideshow">
        <?php foreach ($slides as $i => $img): ?>
            <img src="<?= $img ?>" class="<?= $i === 0 ? 'active' : '' ?>">
        <?php endforeach; ?>
    </div>

    <div class="job-card">

        <h2>Apply for a Job in the United States</h2>
        <p class="job-subtitle">Complete your professional application to get matched with opportunities</p>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <div class="section-title">Personal Information</div>

                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="date" name="dob">

                <div class="section-title">Location Status</div>

                <select name="country_status" id="country_status" required>
                    <option value="">Select Status</option>
                    <option value="in_us">Currently in United States</option>
                    <option value="outside_us">Outside United States</option>
                </select>

                <input type="text" name="us_state" id="us_state" placeholder="US State (if in USA)" style="display:none;">
                <input type="text" name="current_country" id="current_country" placeholder="Current Country" style="display:none;">

                <div class="section-title">Professional Details</div>

                <select name="sector" required>
                    <option value="">Select Sector</option>
                    <?php foreach ($sectors as $s): ?>
                        <option value="<?= $s ?>"><?= $s ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="education" placeholder="Highest Education">
                <input type="number" name="experience" placeholder="Years of Experience">
                <input type="text" name="salary" placeholder="Expected Salary (USD)">

                <div class="form-grid-full">
                    <textarea name="skills" placeholder="Skills"></textarea>
                </div>

                <div class="form-grid-full">
                    <textarea name="cover_letter" placeholder="Cover Letter"></textarea>
                </div>

                <div class="section-title">Additional Info</div>

                <div class="check-row">
                    <label><input type="checkbox" name="visa_required"> Visa Required</label>
                    <label><input type="checkbox" name="relocation"> Willing to Relocate</label>
                </div>

                <div class="section-title">Upload CV</div>

                <input type="file" name="resume" required>

                <button type="submit">Submit Application</button>

            </div>

        </form>

    </div>
</div>

<script>
/* ---------- SLIDESHOW ---------- */
let slides = document.querySelectorAll(".slideshow img");
let index = 0;

setInterval(() => {
    slides[index].classList.remove("active");
    index = (index + 1) % slides.length;
    slides[index].classList.add("active");
}, 3000);

/* ---------- LOCATION TOGGLE ---------- */
document.getElementById('country_status').addEventListener('change', function () {
    document.getElementById('us_state').style.display =
        this.value === 'in_us' ? 'block' : 'none';

    document.getElementById('current_country').style.display =
        this.value === 'outside_us' ? 'block' : 'none';
});
</script>

<?php include 'includes/footer.php'; ?>
