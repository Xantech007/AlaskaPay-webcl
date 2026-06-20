<footer class="footer">
    &copy; 2026 Alaska Cash. All Rights Reserved.
</footer>

<script>

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
    "John", "Michael", "David", "James", "Robert", "William", "Mary", "Patricia",
    "Jennifer", "Linda", "Elizabeth", "Daniel", "Christopher", "Matthew", "Anthony",
    "Sarah", "Andrew", "Joshua", "Emily", "Sophia"
];

function randomItem(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
}

function randomAmount() {
    return Math.floor(Math.random() * (7000 - 3000 + 1)) + 3000;
}

function showToast() {
    const name = randomItem(names);
    const state = randomItem(states);
    const country = "United States";
    const amount = randomAmount();

    const message = `${name} from ${state} living in ${country} withdrawed $${amount}`;

    const toast = document.createElement("div");
    toast.className = "toast";
    toast.innerText = message;

    document.getElementById("toast-container").appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function scheduleToast() {
    const delay = Math.floor(Math.random() * (10000 - 4000 + 1)) + 4000;

    setTimeout(() => {
        showToast();
        scheduleToast();
    }, delay);
}

scheduleToast();
    
function logout() {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = 'logout.php';
    }
}
</script>

</body>
</html>
