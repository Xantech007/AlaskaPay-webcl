<footer class="footer">
    &copy; 2026 Alaska Cash. All Rights Reserved.
</footer>

<script>
const job_sectors = [
    "Engineering", "Healthcare", "Finance", "Education",
    "Marketing", "Construction", "IT", "Agriculture",
    "Retail", "Transportation", "Hospitality", "Security"
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

const names = ["John","Michael","David","James","Robert","William","Mary","Patricia","Jennifer","Linda","Daniel","Sarah","Emily","Joshua","Sophia"];

function rand(arr){ return arr[Math.floor(Math.random()*arr.length)]; }

function amount(){
    return Math.floor(Math.random()*(7000-3000+1))+3000;
}

function showWithdrawToast(){
    const name = rand(names);
    const state = rand(states);
    const country = "United States";
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

</body>
</html>
