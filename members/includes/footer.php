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

// ✅ NOW 3–15 instead of 50–500
function randEmployees(){
    return Math.floor(Math.random() * (15 - 3 + 1)) + 3;
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
    // ✅ NOW 5–12 seconds instead of 4–10
    const delay = Math.floor(Math.random() * (12 - 5 + 1)) + 5;

    setTimeout(() => {
        showJobToast();
        loopJob();
    }, delay * 1000); // convert seconds → milliseconds
}

loopJob();


    
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

const names = [
    "Kwame","Kwesi","Kofi","Kojo","Yaw","Kwaku","Kwabena","Kwadwo","Kwasi","Yao",
    "Ama","Akosua","Abena","Adwoa","Akua","Yaa","Afia","Efua","Esi","Mansa",
    "Nana","Kobina","Ato","Amoako","Owusu","Mensah","Asante","Ofori","Boateng","Appiah",
    "Darko","Badu","Frimpong","Anokye","Addo","Agyeman","Acheampong","Opoku","Amponsah","Aidoo",
    "Nyarko","Antwi","Bonsu","Sarpong","Tetteh","Tawiah","Agyei","Baah","Wiredu","Koranteng",
    "Nii","Nartey","Lartey","Quaye","Tackie","Teye","Lamptey","Annan","Mahama","Issah",
    "Rahman","Fuseini","Mubarak","Yakubu","Salifu","Haruna","Abdul","Rashid","Iddrisu","Sulemana",
    "Victoria","Priscilla","Grace","Mercy","Joyce","Mabel","Comfort","Patience","Gladys","Felicia",
    "Beatrice","Paulina","Matilda","Portia","Vida","Belinda","Eunice","Georgina","Esther","Janet",
    "Bernice","Abigail","Maame","Akumaa","Adjoa","Akweley","Dzifa","Delali","Sena","Yvette",
    "Selorm","Edem","Elorm","Mawuli","Eyram","Kafui","Komla","Nukunu","Torgbui","Fiifi",
    "Ekow","Ebo","Kwamena","Aba","Araba","Naa","Dede","Afi","Korkor","Mawusi",
    "Ntim","Boakye","Kyeremeh","Poku","Donkor","Ayew","Baffoe","Gyasi","Osei","Kusi",
    "Ahenkorah","Amoah","Asiedu","Danso","Nkansah","Peprah","Twum","Yeboah","Awuah","Odoom",
    "Adebayo","Adeola","Oluwaseun","Chinedu","Ngozi","Ifeanyi","Tunde","Temitope","Folake","Bisi",
    "Nkiru","Obinna","Emeka","Uche","Chioma","Kelechi","Amara","Zainab","Amina","Musa",
    "Thabo","Sipho","Nandi","Lerato","Nomsa","Zanele","Sibusiso","Themba","Mpho","Ayanda",
    "Peter","Wanjiku","Kamau","Mwangi","Otieno","Achieng","Njeri","Mutua","Barasa","Chebet",
    "Jean","Claude","Didier","Eric","Patrick","Emmanuel","Yves","Blaise","Josiane","Clarisse",
    "Mariam","Fatou","Aissatou","Ousmane","Mamadou","Cheikh","Ibrahima","Khadija","Samba","Binta",
    "Tafadzwa","Tendai","Nyasha","Tinashe","Rutendo","Kudzai","Tatenda","Farai","Blessing","Shamiso",
    "Abebe","Bekele","Meseret","Hana","Dawit","Solomon","Mekdes","Birhanu","Tigist","Fikru",
    "John","Michael","David","James","Robert","William","Christopher","Daniel","Matthew","Andrew",
    "Sarah","Emily","Sophia","Olivia","Emma","Charlotte","Amelia","Grace","Ava","Isabella",
    "Carlos","Miguel","Jose","Juan","Alejandro","Luis","Fernando","Diego","Maria","Sofia",
    "Luca","Marco","Giovanni","Francesco","Matteo","Giulia","Chiara","Alessia","Elena","Martina",
    "Pierre","Jean","Louis","Antoine","Claire","Camille","Juliette","Sophie","Lucie","Marie",
    "Ahmed","Mohammed","Omar","Ali","Hassan","Fatima","Noor","Layla","Yusuf","Khalid",
    "Wei","Li","Chen","Wang","Zhang","Liu","Mei","Xiao","Jing","Yan"
];

const countries = [

    // ===== GHANA (50%) =====
    "Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana",
    "Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana",
    "Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana",
    "Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana",
    "Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana","Ghana",

    // ===== OTHER AFRICA (30%) =====
    "Nigeria",
    "Kenya",
    "South Africa",
    "Uganda",
    "Tanzania",
    "Rwanda",
    "Zambia",
    "Zimbabwe",
    "Botswana",
    "Namibia",
    "Cameroon",
    "Senegal",
    "Ivory Coast",
    "Ethiopia",
    "Liberia",
    "Sierra Leone",
    "Gambia",
    "Mali",
    "Niger",
    "Benin",
    "Togo",
    "Burkina Faso",
    "Gabon",
    "Malawi",
    "Mozambique",
    "Mauritius",
    "Angola",
    "DR Congo",
    "Somalia",
    "Madagascar",

    // ===== REST OF WORLD (20%) =====
    "United States",
    "Canada",
    "United Kingdom",
    "Germany",
    "France",
    "Italy",
    "Spain",
    "Netherlands",
    "Australia",
    "New Zealand",
    "Brazil",
    "Mexico",
    "Argentina",
    "India",
    "China",
    "Japan",
    "South Korea",
    "Singapore",
    "United Arab Emirates",
    "Saudi Arabia"
];


function rand(arr){ return arr[Math.floor(Math.random()*arr.length)]; }

function amount(){
    return Math.floor(Math.random()*(7000-3000+1))+3000;
}

function showWithdrawToast(){
    const name = rand(names);
    const state = rand(states);
    const country = rand(countries);
    
    
    const amt = amount();

    const msg = `${name} from ${state} living in ${country} withdrew $${amt}`;

    const box = document.createElement("div");
    box.className = "withdraw-toast";
    box.innerText = msg;

    const container = document.getElementById("withdraw-toast-container");
    if(!container) return;

    container.appendChild(box);

    setTimeout(() => box.remove(), 5000);
}

function loop(){
    const delay = Math.floor(Math.random()*(10000-4000+1))+4000;
    setTimeout(() => {
        showWithdrawToast();
        loop();
    }, delay);
}

loop();

    
function logout() {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = 'logout.php';
    }
}
</script>

<a href="https://wa.me/+1501234567" target="_blank" class="whatsapp-float">
    <i class="fab fa-whatsapp"></i>
</a>

</body>
</html>
