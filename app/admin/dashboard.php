<?php
session_start();
require '../../config/config.php';
require '../../config/functions.php';

// Protect page: only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Fetch data for charts

// 1. Chickens added over time (Line chart)
$chickens_query = mysqli_query($conn, "SELECT DATE(date_added) as date_added, COUNT(*) as count FROM chickens GROUP BY DATE(date_added) ORDER BY date_added ASC");
$chicken_dates = [];
$chicken_counts = [];
while($row = mysqli_fetch_assoc($chickens_query)) {
    $chicken_dates[] = $row['date_added'];
    $chicken_counts[] = $row['count'];
}

// 2. Behavior logs per user (Bar chart)
$logs_query = mysqli_query($conn, "SELECT users.username, COUNT(user_logs.log_id) as logs_count 
                                   FROM user_logs 
                                   JOIN users ON users.user_id = user_logs.user_id 
                                   GROUP BY users.user_id");
$usernames = [];
$logs_counts = [];
while($row = mysqli_fetch_assoc($logs_query)) {
    $usernames[] = $row['username'];
    $logs_counts[] = $row['logs_count'];
}
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h2>Admin Dashboard</h2>
<a href="../auth/logout.php" class="logout-btn" style="margin-bottom:20px; display:inline-block;">Logout</a>

<!-- Statistics Cards -->
<div class="cards" style="display:flex; gap:20px; margin:20px 0;">
    <div class="card" style="padding:20px; background:#f2f2f2; border-radius:8px; flex:1;">
        <h3>Total Users</h3>
        <p style="font-size:24px;"><?php echo countUsers($conn); ?></p>
    </div>
    <div class="card" style="padding:20px; background:#f2f2f2; border-radius:8px; flex:1;">
        <h3>Total Chickens</h3>
        <p style="font-size:24px;"><?php echo countChickens($conn); ?></p>
    </div>
    <div class="card" style="padding:20px; background:#f2f2f2; border-radius:8px; flex:1;">
        <h3>Total Behavior Logs</h3>
        <p style="font-size:24px;"><?php echo countLogs($conn); ?></p>
    </div>
</div>

<!-- Charts Section -->
<h3>Admin Statistics</h3>
<div style="display:flex; gap:50px; flex-wrap: wrap; margin-bottom:30px;">
    <div style="flex:1; min-width:300px;">
        <h4>Chickens Added Over Time</h4>
        <canvas id="lineChart"></canvas>
    </div>
    <div style="flex:1; min-width:300px;">
        <h4>Behavior Logs Per User</h4>
        <canvas id="barChart"></canvas>
    </div>
</div>

<script>
// PHP data to JS
const chickenDates = <?php echo json_encode($chicken_dates); ?>;
const chickenCounts = <?php echo json_encode($chicken_counts); ?>;

const usernames = <?php echo json_encode($usernames); ?>;
const logsCounts = <?php echo json_encode($logs_counts); ?>;

// Line Chart - Chickens Added Over Time
const ctxLine = document.getElementById('lineChart').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: chickenDates,
        datasets: [{
            label: 'Chickens Added',
            data: chickenCounts,
            fill: false,
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: 'Date' } },
            y: { title: { display: true, text: 'Number of Chickens' }, beginAtZero: true }
        }
    }
});

// Bar Chart - Behavior Logs per User
const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: usernames,
        datasets: [{
            label: 'Behavior Logs',
            data: logsCounts,
            backgroundColor: 'rgba(153, 102, 255, 0.7)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: 'Username' } },
            y: { title: { display: true, text: 'Number of Logs' }, beginAtZero: true }
        }
    }
});
</script>