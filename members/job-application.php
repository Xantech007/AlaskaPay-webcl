<?php
session_start();
require '../config/db.php';
include 'includes/countries.php';

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

/*
|--------------------------------------------------------------------------
| HANDLE FORM SUBMISSION
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // collect fields

    // upload resume

    // insert into job_applications table

    $_SESSION['success_message'] =
        "Your job application has been submitted successfully.";

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

<style>
body {
    background: #f4f6fb;
}

/* ---------- SLIDESHOW ---------- */

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

.job-hero{
    height:450px;
    position:relative;
    overflow:hidden;
    border-radius:20px;
    margin-bottom:30px;
}

.slide{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
    opacity:0;
    transition:opacity 1s ease;
}

.slide.active{
    opacity:1;
}

.hero-overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.55);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    color:#fff;
    text-align:center;
    padding:30px;
}

.form-section{
    margin-top:25px;
    margin-bottom:25px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

.job-form input,
.job-form select,
.job-form textarea{
    width:100%;
    padding:14px;
    border:1px solid #ddd;
    border-radius:10px;
    background:#fafafa;
}

.job-form input:focus,
.job-form select:focus,
.job-form textarea:focus{
    border-color:#0d6efd;
    outline:none;
    background:#fff;
}

.submit-btn{
    width:100%;
    padding:16px;
    border:none;
    border-radius:12px;
    background:#0d6efd;
    color:white;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
}

@media(max-width:768px){

    .grid-2{
        grid-template-columns:1fr;
    }

}

.job-alert{
    background:#fff8e1;
    border-left:4px solid #ff9800;
    padding:12px;
    border-radius:10px;
    margin:10px 0;
    font-size:14px;
}

.grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

@media(max-width:768px){
    .grid-2{
        grid-template-columns:1fr;
    }
}

.submit-btn{
    width:100%;
    padding:16px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#4a90e2,#6c5ce7);
    color:white;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
}
    
</style>

<div class="job-container">

    <!-- SLIDESHOW -->
    <div class="job-hero">
    
        <?php for($i=1; $i<=15; $i++): ?>
    
            <img
                src="../assets/jobs-slideshow/<?= $i ?>.png"
                class="slide <?= $i === 1 ? 'active' : '' ?>"
                loading="lazy"
            >
    
        <?php endfor; ?>
    
        <div class="hero-overlay">
    
            <h1>Apply for Jobs in the United States</h1>
    
            <p>
                Explore opportunities across Engineering,
                Healthcare, Information Technology,
                Finance, Construction, Government,
                Hospitality and more.
            </p>
    
        </div>
    
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

                <div class="section-title">Location Information</div>
                
                <div class="form-grid-full">
                
                    <select name="country_status" id="country_status" required>
                        <option value="">Select Status</option>
                        <option value="in_us">Currently in United States</option>
                        <option value="outside_us">Outside United States</option>
                    </select>
                
                </div>
                
                <div class="form-grid">
                
                    <div class="form-group" id="us_state_wrapper" style="display:none;">
                        <label>US State</label>
                        <input type="text" name="us_state" placeholder="US State">
                    </div>
                
                    <div class="form-group" id="country_wrapper" style="display:none;">
                        <label>Country</label>
                
                        <select name="current_country" id="current_country">
                            <option value="">Select Country</option>
                
                            <?php foreach($countries as $country): ?>
                                <option value="<?= htmlspecialchars($country) ?>">
                                    <?= htmlspecialchars($country) ?>
                                </option>
                            <?php endforeach; ?>
                
                        </select>
                    </div>
                
                </div>

                <div class="section-title">Professional Details</div>

                <select name="sector" required>
                    <option value="">Select Sector</option>
                    <?php foreach ($sectors as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>">
                            <?= htmlspecialchars($s) ?>
                        </option>
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

                <div class="form-section">
                
                    <h3>
                        <i class="fas fa-id-card"></i>
                        Identity Verification
                    </h3>
                
                    <div class="job-alert">
                        Please upload a valid government-issued identification document.
                    </div>
                
                    <div class="grid-2">
                
                        <div class="form-group">
                            <label>ID Type *</label>
                
                            <select name="id_type" required>
                                <option value="">Select ID Type</option>
                                <option value="Passport">Passport</option>
                                <option value="National ID">National ID Card</option>
                                <option value="Driver License">Driver License</option>
                                <option value="Permanent Resident Card">Permanent Resident Card</option>
                                <option value="Voter Card">Voter Card</option>
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label>ID Number *</label>
                            <input
                                type="text"
                                name="id_number"
                                required
                            >
                        </div>
                
                    </div>
                
                    <div class="grid-2">
                
                        <div class="form-group">
                            <label>ID Front Image *</label>
                
                            <input
                                type="file"
                                name="id_front"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required
                            >
                        </div>
                
                        <div class="form-group">
                            <label>ID Back Image (Optional)</label>
                
                            <input
                                type="file"
                                name="id_back"
                                accept=".jpg,.jpeg,.png,.pdf"
                            >
                        </div>
                
                    </div>
                
                </div>

                <div class="section-title">Upload CV</div>
                
                <div class="form-grid-full">
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
                </div>

                <button type="submit" class="submit-btn">
                    Submit Application
                </button>

            </div>

        </form>

    </div>
</div>

<script>

const slides =
document.querySelectorAll('.slide');

let current = 0;

setInterval(() => {

    slides[current]
        .classList.remove('active');

    current++;

    if(current >= slides.length){
        current = 0;
    }

    slides[current]
        .classList.add('active');

}, 3500);



/* ---------- LOCATION TOGGLE ---------- */
const statusField = document.getElementById('country_status');
const stateWrapper = document.getElementById('us_state_wrapper');
const countryWrapper = document.getElementById('country_wrapper');

if (statusField) {
    statusField.addEventListener('change', function () {

        if (this.value === 'in_us') {
            stateWrapper.style.display = 'block';
            countryWrapper.style.display = 'none';

        } else if (this.value === 'outside_us') {
            stateWrapper.style.display = 'none';
            countryWrapper.style.display = 'block';

        } else {
            stateWrapper.style.display = 'none';
            countryWrapper.style.display = 'none';
        }

    });
}

function updateLocationFields(value) {

    if (value === 'in_us') {
        stateWrapper.style.display = 'block';
        countryWrapper.style.display = 'none';

    } else if (value === 'outside_us') {
        stateWrapper.style.display = 'none';
        countryWrapper.style.display = 'block';

    } else {
        stateWrapper.style.display = 'none';
        countryWrapper.style.display = 'none';
    }
}

statusField.addEventListener('change', function () {
    updateLocationFields(this.value);
});

// run on page load
updateLocationFields(statusField.value);
    
</script>

<?php include 'includes/footer.php'; ?>
