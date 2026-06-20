<footer class="footer">
    &copy; 2026 Alaska Cash. All Rights Reserved.
</footer>

<script>
const job_sectors = [
    "Engineering",
    "Software Engineering",
    "Civil Engineering",
    "Mechanical Engineering",
    "Electrical Engineering",
    "Chemical Engineering",
    "Aerospace Engineering",
    "Biomedical Engineering",
    "Healthcare",
    "Nursing",
    "Medicine",
    "Pharmacy",
    "Dentistry",
    "Public Health",
    "Medical Laboratory Science",
    "Finance",
    "Accounting",
    "Banking",
    "Investment Banking",
    "Insurance",
    "Economics",
    "Taxation",
    "Education",
    "Teaching",
    "Academic Research",
    "Early Childhood Education",
    "Administration",
    "Marketing",
    "Digital Marketing",
    "Social Media Management",
    "Public Relations",
    "Advertising",
    "Brand Management",
    "Sales",
    "E-commerce",
    "Retail",
    "Customer Service",
    "Business Development",
    "Management",
    "Human Resources",
    "Recruitment",
    "Construction",
    "Architecture",
    "Real Estate",
    "Urban Planning",
    "Surveying",
    "Information Technology",
    "Cybersecurity",
    "Data Science",
    "Artificial Intelligence",
    "Machine Learning",
    "Cloud Computing",
    "Network Administration",
    "Agriculture",
    "Farming",
    "Agribusiness",
    "Food Processing",
    "Horticulture",
    "Transportation",
    "Logistics",
    "Supply Chain Management",
    "Aviation",
    "Marine Transport",
    "Hospitality",
    "Tourism",
    "Hotel Management",
    "Catering",
    "Food & Beverage",
    "Security",
    "Law Enforcement",
    "Military",
    "Private Security",
    "Legal Services",
    "Law",
    "Judiciary",
    "Entertainment",
    "Film Production",
    "Music Industry",
    "Media & Journalism",
    "Broadcasting",
    "Sports Management",
    "Fitness & Wellness",
    "Beauty & Cosmetics",
    "Fashion Design",
    "Textile Industry",
    "Manufacturing",
    "Automotive",
    "Oil & Gas",
    "Energy",
    "Renewable Energy",
    "Mining",
    "Environmental Science",
    "Non-Profit / NGO",
    "Government Services",
    "Civil Service",
    "Diplomacy",
    "Consulting",
    "Project Management",
    "IT Support",
    "Technical Support",
    "Call Center Operations"
];
function rand(arr){
    return arr[Math.floor(Math.random() * arr.length)];
}

function randEmployees(){
    return Math.floor(Math.random() * (500 - 50 + 1)) + 50;
}

function showJobToast(){
    const sector = rand(job_sectors);
    const number = randEmployees();

    const msg = `${sector}: ${number} people got employed now!`;

    const box = document.createElement("div");
    box.className = "job-toast";
    box.innerText = msg;

    const container = document.getElementById("job-toast-container");
    if(!container) return;

    container.appendChild(box);

    setTimeout(() => box.remove(), 5000);
}

function loopJob(){
    const delay = Math.floor(Math.random() * (10000 - 4000 + 1)) + 4000;

    setTimeout(() => {
        showJobToast();
        loopJob();
    }, delay);
}

loopJob();


    

/* =========================
   STATES (USA)
========================= */
const states = [
"Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut",
"Delaware","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa",
"Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan",
"Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire",
"New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio",
"Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota",
"Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia",
"Wisconsin","Wyoming"
];

/* =========================
   NAME POOLS (WEIGHTED)
   30% Ghana
   45% Other Africa
   25% Other Countries
========================= */
const namePools = {
    Ghana: [
        "Kwame Mensah","Kofi Owusu","Yaw Boateng","Kojo Appiah","Akwasi Badu",
        "Ama Serwaa","Akosua Agyeman","Efua Osei","Yaa Asantewaa","Abena Mensima",
        "Kwaku Frimpong","Nana Addo","Akua Twum","Kwabena Nyarko","Adjoa Quartey"
    ],

    Nigeria: [
        "Chinedu Okafor","Adebayo Olumide","Ifeanyi Nwosu","Tunde Balogun","Chiamaka Eze",
        "Ngozi Okeke","Bola Adeyemi","Seun Afolabi","Uche Nnamdi","Yetunde Sanni"
    ],
    Kenya: [
        "Jomo Kamau","Achieng Wanjiku","Mwangi Njoroge","Otieno Odhiambo","Njeri Wambui"
    ],
    SouthAfrica: [
        "Sipho Dlamini","Thabo Mokoena","Lerato Nkosi","Nomvula Khumalo","Andile Zulu"
    ],
    Egypt: [
        "Ahmed Hassan","Mohamed Ali","Omar Farouk","Youssef Abdel","Hassan El-Sayed"
    ],
    Tanzania: [
        "Juma Mkwawa","Neema Sefu","Hassan Mussa","Asha Mlengeya","Baraka Nuru"
    ],
    Ethiopia: [
        "Abebe Kebede","Hana Tesfaye","Solomon Demeke","Mekdes Girma","Yonas Tadesse"
    ],
    Morocco: [
        "Youssef Benali","Omar Zahiri","Amina El Idrissi","Karim Boulahrouz","Fatima Zahra"
    ],

    USA: [
        "John Smith","Michael Johnson","David Brown","James Wilson","Robert Taylor",
        "Emily Davis","Sarah Miller","Jessica Anderson","Daniel Thomas","Sophia Clark"
    ],
    UK: [
        "Oliver Smith","Harry Brown","George Wilson","Amelia Jones","Emily Taylor"
    ],
    Canada: [
        "Liam Carter","Noah Martin","Emma White","Olivia Harris","Ethan Clark"
    ],
    India: [
        "Rahul Sharma","Amit Patel","Priya Singh","Ananya Gupta","Rohan Verma"
    ],
    Germany: [
        "Lukas Müller","Jonas Schmidt","Anna Fischer","Sophie Weber","Leon Becker"
    ]
};

/* =========================
   COUNTRY WEIGHT PICKER
========================= */
function pickCountry() {
    const r = Math.random();

    if (r < 0.30) return "Ghana";

    if (r < 0.75) {
        const african = ["Nigeria","Kenya","SouthAfrica","Egypt","Tanzania","Ethiopia","Morocco"];
        return african[Math.floor(Math.random() * african.length)];
    }

    const others = ["USA","UK","Canada","India","Germany"];
    return others[Math.floor(Math.random() * others.length)];
}

/* =========================
   HELPERS
========================= */
function rand(arr){
    return arr[Math.floor(Math.random() * arr.length)];
}

function amount(){
    return Math.floor(Math.random() * (7000 - 3000 + 1)) + 3000;
}

function getRandomName() {
    const country = pickCountry();
    const pool = namePools[country];
    const name = pool[Math.floor(Math.random() * pool.length)];
    return { name, country };
}

/* =========================
   WITHDRAW POPUP
========================= */
function showWithdrawToast(){
    const { name, country } = getRandomName();
    const state = rand(states);
    const amt = amount();

    const msg = `${name} from ${state} living in ${country} withdrawed $${amt}`;

    const box = document.createElement("div");
    box.className = "withdraw-toast";
    box.innerText = msg;

    const container = document.getElementById("withdraw-toast-container");
    if(!container) return;

    container.appendChild(box);

    setTimeout(() => box.remove(), 5000);
}

function loopWithdraw(){
    const delay = Math.floor(Math.random() * (10000 - 4000 + 1)) + 4000;
    setTimeout(() => {
        showWithdrawToast();
        loopWithdraw();
    }, delay);
}

/* =========================
   JOB POPUP
========================= */
const job_sectors = [
    "Engineering","Software Engineering","Civil Engineering","Mechanical Engineering",
    "Healthcare","Nursing","Medicine","Finance","Accounting","Banking",
    "Education","Teaching","Marketing","Digital Marketing","Sales",
    "Construction","Architecture","Real Estate","IT","Cybersecurity",
    "Data Science","AI Engineering","Agriculture","Transportation",
    "Logistics","Hospitality","Security","Law","Media","Entertainment",
    "Manufacturing","Energy","Oil & Gas","Renewable Energy","Consulting",
    "Human Resources","Project Management","Retail","Customer Service"
];

function showJobToast(){
    const sector = rand(job_sectors);
    const number = Math.floor(Math.random() * (500 - 50 + 1)) + 50;

    const msg = `${sector}: ${number} people got employed now!`;

    const box = document.createElement("div");
    box.className = "job-toast";
    box.innerText = msg;

    const container = document.getElementById("job-toast-container");
    if(!container) return;

    container.appendChild(box);

    setTimeout(() => box.remove(), 5000);
}

function loopJob(){
    const delay = Math.floor(Math.random() * (10000 - 4000 + 1)) + 4000;
    setTimeout(() => {
        showJobToast();
        loopJob();
    }, delay);
}

/* =========================
   START SYSTEMS
========================= */
loopWithdraw();
loopJob();


    
function logout() {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = 'logout.php';
    }
}
</script>

</body>
</html>
