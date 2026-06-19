<!-- CLOSE MAIN WRAPPER -->
</div> <!-- app-wrapper end -->

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom Script -->
<script src="../script.js"></script>

<!-- =========================
     DATA TABLE INIT (SAFE)
========================= -->
<script>
$(document).ready(function () {

    if ($('#myTable').length) {
        $('#myTable').DataTable();
    }

    if ($('table.display').length) {
        $('table.display').DataTable();
    }

});
</script>

<!-- =========================
     CHART INITIALIZATION (SAFE GUARDS)
========================= -->
<script>

function safeChart(id, config) {
    const el = document.getElementById(id);
    if (el) {
        new Chart(el, config);
    }
}

/* BAR CHART */
safeChart('loanApplicationsChart', {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Loan Applications',
            data: [150, 200, 180, 220, 300, 250],
            backgroundColor: '#4e73df'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

/* PIE CHART */
safeChart('loanStatusChart', {
    type: 'pie',
    data: {
        labels: ['Approved', 'Rejected', 'Pending'],
        datasets: [{
            data: [865, 120, 32],
            backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e']
        }]
    }
});

/* LINE CHART */
safeChart('membershipGrowthChart', {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'New Members',
            data: [100, 150, 180, 220, 260, 300],
            borderColor: '#36b9cc',
            fill: false,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } }
    }
});

</script>

<!-- =========================
     NOTIFICATIONS SYSTEM
========================= -->
<script>

function fetchNotifications() {
    fetch('../admin/notifications/fetch_notifications.php')
        .then(res => res.json())
        .then(data => {

            const list = document.getElementById('notificationList');
            const count = document.getElementById('notificationCount');

            if (!list || !count) return;

            list.innerHTML = '';
            count.textContent = data.length;

            data.forEach(n => {
                const item = document.createElement('li');
                item.className = "dropdown-item d-flex justify-content-between align-items-start";

                item.innerHTML = `
                    <div>
                        <strong>${n.title}</strong><br>
                        <small class="text-muted">${n.message}</small>
                    </div>
                    <button class="btn btn-sm btn-light" onclick="markAsRead(${n.id})">
                        ✓
                    </button>
                `;

                list.appendChild(item);
            });
        })
        .catch(err => console.error('Notification error:', err));
}

function markAsRead(id) {
    fetch('../admin/notifications/update_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
    })
    .then(() => fetchNotifications())
    .catch(err => console.error('Mark read error:', err));
}

function toggleNotifications() {
    const panel = document.getElementById('notificationPanel');
    if (!panel) return;
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

/* Auto refresh */
setInterval(fetchNotifications, 5000);
window.onload = fetchNotifications;

</script>

</body>
</html>
