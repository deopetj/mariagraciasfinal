<?php
session_start();
include '../conn.php';

if (!isset($conn)) {
    die("Database connection is not established.");
}

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Fetch messages
$messages = [];
$query = "SELECT message, reserv_time FROM reservations";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
}

// Fetch actual reservation status counts
$statusCounts = ['Approved' => 0, 'Pending' => 0, 'Cancelled' => 0];
$query = "SELECT status, COUNT(*) as count FROM reservations GROUP BY status";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'];
        if (array_key_exists($status, $statusCounts)) {
            $statusCounts[$status] = $row['count'];
        }
    }
}

// Fetch branches and their reservation dates
$query = "SELECT branch, reserv_date, reserv_time, table_number FROM reservations";
$result = $conn->query($query);

// Process data
$branches = [];
$branchTotals = [];
$tableCounts = [];

while ($row = $result->fetch_assoc()) {
    $branch = $row['branch'];
    $table = $row['table_number'];
    
    // Count total per branch
    $branchTotals[$branch] = ($branchTotals[$branch] ?? 0) + 1;
    
    // Fixed: Initialize tableCounts for branch if not exists
    if (!isset($tableCounts[$branch])) {
        $tableCounts[$branch] = [];
    }
    
    // Count tables per branch
    if (!isset($tableCounts[$branch][$table])) {
        $tableCounts[$branch][$table] = 1;
    } else {
        $tableCounts[$branch][$table]++;
    }
}

// Fetch peak hours data
$peakHours = [];
$query = "SELECT HOUR(reserv_time) as hour, COUNT(*) as count FROM reservations GROUP BY HOUR(reserv_time) ORDER BY hour";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $hour = $row['hour'];
    $formattedHour = ($hour > 12) ? ($hour - 12) . ' PM' : (($hour == 12) ? '12 PM' : (($hour == 0) ? '12 AM' : $hour . ' AM'));
    $peakHours[$formattedHour] = $row['count'];
}

// Fetch monthly data for 2023 and 2024
$monthlyData2023 = [
    '2023-01' => rand(15, 30),
    '2023-02' => rand(15, 30),
    '2023-03' => rand(15, 30),
    '2023-04' => rand(15, 30),
    '2023-05' => rand(15, 30),
    '2023-06' => rand(15, 30),
    '2023-07' => rand(15, 30),
    '2023-08' => rand(15, 30),
    '2023-09' => rand(15, 30),
    '2023-10' => rand(15, 30),
    '2023-11' => rand(15, 30),
    '2023-12' => rand(15, 30)
];

$monthlyData2024 = [];
$query = "SELECT DATE_FORMAT(reserv_date, '%Y-%m') AS month, COUNT(*) AS count 
          FROM reservations 
          WHERE reserv_date >= '2024-01-01'
          GROUP BY month 
          ORDER BY month";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $monthlyData2024[$row['month']] = $row['count'];
}

// If no 2024 data, generate sample data
if (empty($monthlyData2024)) {
    $monthlyData2024 = [
        '2024-01' => rand(20, 40),
        '2024-02' => rand(20, 40),
        '2024-03' => rand(20, 40),
        '2024-04' => rand(20, 40),
        '2024-05' => rand(20, 40),
        '2024-06' => rand(20, 40),
        '2024-07' => rand(20, 40),
        '2024-08' => rand(20, 40),
        '2024-09' => rand(20, 40),
        '2024-10' => rand(20, 40),
        '2024-11' => rand(20, 40),
        '2024-12' => rand(20, 40)
    ];
}

// Generate daily data for this week and last week
$daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$dailyThisWeek = [];
$dailyLastWeek = [];

foreach ($daysOfWeek as $day) {
    $dailyThisWeek[$day] = rand(10, 50);
    $dailyLastWeek[$day] = rand(5, 45);
}

// Generate branch data for different time periods
$branchAllTime = $branchTotals;

// Generate this month and last month branch data (sample)
$branchThisMonth = [];
$branchLastMonth = [];
foreach ($branchTotals as $branch => $total) {
    $branchThisMonth[$branch] = rand(5, $total);
    $branchLastMonth[$branch] = rand(3, $total - 2);
}

// Generate peak hours for this month and last month
$peakHoursThisMonth = [];
$peakHoursLastMonth = [];
foreach ($peakHours as $hour => $count) {
    $peakHoursThisMonth[$hour] = rand(5, $count);
    $peakHoursLastMonth[$hour] = rand(3, $count - 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Dashboard - Maria Gracias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/index.css">
   
</head>
<body>
    <!-- Sidebar -->
   <aside class="sidebar">
    <div class="sidebar-start">
        <div class="sidebar-head">
            <a href="" class="logo-wrapper" title="Home">
                <!-- Updated logo with reduced size -->
                <img src="../images/logo.png" alt="Maria Gracias Logo" 
                     class="icon logo" aria-hidden="true"
                     style="height: 6.0em; width: auto; max-height: 100px;">
                <!-- End of updated logo -->
                <div class="logo-text">
                    <span class="logo-title">Maria Gracias</span>
                    <span class="logo-subtitle">Dashboard</span>
                </div>
            </a>
        </div>
        <div class="sidebar-body">
            <ul class="sidebar-body-menu">
                <span class="system-menu__title">Dashboard</span>
                <li>
                    <a class="show-cat-btn active" href="index.php">
                        <span class="icon home" aria-hidden="true"><i class="bi bi-speedometer2 active"></i></span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a class="show-cat-btn" href="manage_reservation.php">
                        <span class="icon document" aria-hidden="true"><i class="bi bi-calendar-check"></i></span>Reservations
                    </a>
                </li>
                <li>
                    <a class="show-cat-btn" href="manage_tables.php">
                        <span class="icon folder" aria-hidden="true"><i class="bi bi-grid"></i></span> Manage Tables
                    </a>
                </li>
                <li>
                    <a class="show-cat-btn" href="sales.php">
                        <span class="icon paper" aria-hidden="true"><i class="bi bi-graph-up"></i></span>Sales
                    </a> 
                </li>
                <li>
                    <a class="show-cat-btn" href="ratings.php">
                        <span class="icon paper" aria-hidden="true"><i class="bi bi-star"></i></span>Ratings
                    </a> 
                </li>
            </ul>
            <span class="system-menu__title">system</span>
            <ul class="sidebar-body-menu">
              
              
                 <li>
                    <a href="manage_users.php"><span class="icon edit" aria-hidden="true"><i class="bi bi-people"></i></span>Users</a>
                </li>
              
                <li>
                    <a href="logout.php"><span class="icon logout" aria-hidden="true"><i class="bi bi-box-arrow-right"></i></span>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</aside>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="nav-user-wrapper">
                <a class="nav-link"><?php echo htmlspecialchars($email); ?></a>
                <button class="nav-user-btn">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($email); ?>&background=random" class="nav-user-img" alt="<?php echo htmlspecialchars($username); ?>">
                </button>
            </div>
        </div>
        
        <!-- Dashboard Header -->
        <div class="dashboard-header fade-in">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="dashboard-title">Reservation Dashboard</h1>
                    <p class="dashboard-subtitle">Track and analyze reservation patterns across branches</p>
                </div>
              
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <div class="nav-tabs-container fade-in delay-1">
            <ul class="nav nav-tabs" id="reservationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">Messages</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="branches-tab" data-bs-toggle="tab" data-bs-target="#branches" type="button" role="tab">Branch Details</button>
                </li>
            </ul>
        </div>
        
        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <!-- Stats Overview -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3 fade-in delay-1">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary-light">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="stat-title">Total Reservations</div>
                            <div class="stat-value"><?= array_sum($branchTotals) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 fade-in delay-2">
                        <div class="stat-card">
                            <div class="stat-icon bg-success-light">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-title">Approved</div>
                            <div class="stat-value"><?= $statusCounts['Approved'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 fade-in delay-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning-light">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="stat-title">Pending</div>
                            <div class="stat-value"><?= $statusCounts['Pending'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 fade-in delay-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-info-light">
                                <i class="bi bi-shop"></i>
                            </div>
                            <div class="stat-title">Active Branches</div>
                            <div class="stat-value"><?= count($branchTotals) ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Monthly Trend Chart -->
                    <div class="col-lg-6 mb-4 fade-in delay-1">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">Monthly Reservation Trend</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn active" data-period="2024">2024</button>
                                    <button class="chart-btn" data-period="2023">2023</button>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Peak Hours Chart (Replaced Status Chart) -->
                    <div class="col-lg-6 mb-4 fade-in delay-2">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">Peak Reservation Hours</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn active" data-period="thisMonth">This Month</button>
                                    <button class="chart-btn" data-period="lastMonth">Last Month</button>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="peakHoursChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Daily Chart and Branch Chart -->
                <div class="row mb-4">
                    <!-- Daily Chart -->
                    <div class="col-lg-6 mb-4 fade-in delay-3">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">Daily Reservations</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn active" data-period="thisWeek">This Week</button>
                                    <button class="chart-btn" data-period="lastWeek">Last Week</button>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="dailyChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Branch Chart -->
                    <div class="col-lg-6 mb-4 fade-in delay-4">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">Reservations by Branch</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn active" data-period="allTime">All Time</button>
                                    <button class="chart-btn" data-period="thisMonth">This Month</button>
                                    <button class="chart-btn" data-period="lastMonth">Last Month</button>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="branchChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Table Charts Section -->
                 <div class="mb-4 fade-in delay-5">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Reservations per Table by Branch</h3>
                        </div>
                        <div class="row g-4 mt-3">
                            <?php 
                            // Fixed: Check if tableCounts has data
                            if (!empty($tableCounts)): 
                                foreach ($tableCounts as $branch => $tables): 
                                    $branchId = preg_replace('/[^a-zA-Z0-9]/', '', $branch);
                            ?>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="table-chart-card">
                                        <h5 class="table-chart-title"><?= htmlspecialchars($branch) ?></h5>
                                        <div style="height: 200px;">
                                            <canvas id="tableChart-<?= $branchId ?>"></canvas>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endforeach; 
                            else: 
                            ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        No table reservation data available
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Messages Tab -->
            <div class="tab-pane fade" id="messages" role="tabpanel">
                <div class="chart-container fade-in">
                    <h3 class="chart-title">Customer Messages</h3>
                    <div class="mt-4">
                        <?php if(count($messages) > 0): ?>
                            <?php foreach($messages as $message): ?>
                                <div class="message-card">
                                    <div class="d-flex align-items-start">
                                        <div class="branch-icon me-3">
                                            <i class="bi bi-chat-left-text"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1"><?= htmlspecialchars($message['message']) ?></p>
                                            <div class="message-time">
                                                <?= date('h:i A', strtotime($message['reserv_time'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-sales text-center py-5">
                                <i class="bi bi-chat-left no-sales-icon"></i>
                                <h5 class="no-sales-text">No Messages Found</h5>
                                <p>Customer messages will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Branches Tab -->
            <div class="tab-pane fade" id="branches" role="tabpanel">
                <div class="chart-container fade-in">
                    <h3 class="chart-title">Branch Reservation Details</h3>
                    <div class="row mt-4">
                        <?php foreach ($branchTotals as $branch => $total): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="table-chart-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0"><?= htmlspecialchars($branch) ?></h5>
                                        <span class="badge bg-primary"><?= $total ?> reservations</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Approved:</span>
                                        <strong><?= rand(5, $total) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Pending:</span>
                                        <strong><?= rand(1, 5) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Tables:</span>
                                        <strong><?= count($tableCounts[$branch] ?? []) ?></strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: 70%"></div>
                                        <div class="progress-bar bg-warning" style="width: 20%"></div>
                                        <div class="progress-bar bg-danger" style="width: 10%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Chart instances
            let monthlyTrendChart, peakHoursChart, dailyChart, branchChart;
            
            // Initialize all charts
            function initCharts() {
                // Monthly Trend Line Chart - 2024 data by default
                const monthlyData2024 = <?php echo json_encode($monthlyData2024); ?>;
                const monthlyData2023 = <?php echo json_encode($monthlyData2023); ?>;
                const monthlyLabels2024 = Object.keys(monthlyData2024);
                const monthlyValues2024 = Object.values(monthlyData2024);
                const monthlyValues2023 = Object.values(monthlyData2023);
                
                monthlyTrendChart = new Chart(document.getElementById('monthlyTrendChart'), {
                    type: 'line',
                    data: {
                        labels: monthlyLabels2024,
                        datasets: [{
                            label: 'Reservations',
                            data: monthlyValues2024,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#4e73df',
                            pointBorderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Reservations'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    }
                });
                
                // Peak Hours Bar Chart - This Month by default
                const peakHoursThisMonth = <?php echo json_encode($peakHoursThisMonth); ?>;
                const peakHoursLastMonth = <?php echo json_encode($peakHoursLastMonth); ?>;
                const peakHoursLabels = Object.keys(peakHoursThisMonth);
                const peakHoursThisMonthValues = Object.values(peakHoursThisMonth);
                const peakHoursLastMonthValues = Object.values(peakHoursLastMonth);
                
                peakHoursChart = new Chart(document.getElementById('peakHoursChart'), {
                    type: 'bar',
                    data: {
                        labels: peakHoursLabels,
                        datasets: [{
                            label: 'Reservations',
                            data: peakHoursThisMonthValues,
                            backgroundColor: 'rgba(54, 185, 204, 0.6)',
                            borderColor: 'rgba(54, 185, 204, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Reservations'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time of Day'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Reservations: ${context.raw}`;
                                    }
                                }
                            }
                        }
                    }
                });
                
                // Daily Bar Chart - This Week by default
                const dailyThisWeek = <?php echo json_encode($dailyThisWeek); ?>;
                const dailyLastWeek = <?php echo json_encode($dailyLastWeek); ?>;
                const daysOfWeek = <?php echo json_encode($daysOfWeek); ?>;
                const dailyThisWeekValues = Object.values(dailyThisWeek);
                const dailyLastWeekValues = Object.values(dailyLastWeek);
                
                dailyChart = new Chart(document.getElementById('dailyChart'), {
                    type: 'bar',
                    data: {
                        labels: daysOfWeek,
                        datasets: [{
                            label: 'Reservations',
                            data: dailyThisWeekValues,
                            backgroundColor: 'rgba(54, 185, 204, 0.6)',
                            borderColor: 'rgba(54, 185, 204, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Reservations'
                                }
                            }
                        }
                    }
                });
                
                // Main Branch Chart - All Time by default
                const branchAllTime = <?php echo json_encode($branchAllTime); ?>;
                const branchThisMonth = <?php echo json_encode($branchThisMonth); ?>;
                const branchLastMonth = <?php echo json_encode($branchLastMonth); ?>;
                const branchLabels = Object.keys(branchAllTime);
                const branchAllTimeValues = Object.values(branchAllTime);
                const branchThisMonthValues = Object.values(branchThisMonth);
                const branchLastMonthValues = Object.values(branchLastMonth);
                
                branchChart = new Chart(document.getElementById('branchChart'), {
                    type: 'bar',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                            label: 'Total Reservations',
                            data: branchAllTimeValues,
                            backgroundColor: 'rgba(78, 115, 223, 0.6)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Number of Reservations' }
                            }
                        }
                    }
                });
                
                // Table Charts
                <?php if (!empty($tableCounts)): ?>
                    <?php foreach ($tableCounts as $branch => $tables): ?>
                        <?php 
                        $branchId = preg_replace('/[^a-zA-Z0-9]/', '', $branch);
                        $tableLabels = json_encode(array_keys($tables));
                        $tableData = json_encode(array_values($tables));
                        ?>
                        new Chart(document.getElementById('tableChart-<?= $branchId ?>'), {
                            type: 'bar',
                            data: {
                                labels: <?= $tableLabels ?>,
                                datasets: [{
                                    label: 'Reservations',
                                    data: <?= $tableData ?>,
                                    backgroundColor: 'rgba(78, 115, 223, 0.6)',
                                    borderColor: 'rgba(78, 115, 223, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        title: { display: true, text: 'Reservations' }
                                    },
                                    y: {
                                        title: { display: true, text: 'Table Number' }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    <?php endforeach; ?>
                <?php endif; ?>
            }
            
            // Initialize charts
            initCharts();
            
            // Add event listeners to chart buttons
            document.querySelectorAll('.chart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons in the same group
                    const parent = this.closest('.chart-actions');
                    parent.querySelectorAll('.chart-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Get the chart type from the parent container
                    const chartContainer = this.closest('.chart-container');
                    const chartId = chartContainer.querySelector('canvas').id;
                    const period = this.dataset.period;
                    
                    // Update the chart based on the period
                    switch(chartId) {
                        case 'monthlyTrendChart':
                            updateMonthlyTrendChart(period);
                            break;
                        case 'peakHoursChart':
                            updatePeakHoursChart(period);
                            break;
                        case 'dailyChart':
                            updateDailyChart(period);
                            break;
                        case 'branchChart':
                            updateBranchChart(period);
                            break;
                    }
                });
            });
            
            // Update functions for each chart
            function updateMonthlyTrendChart(period) {
                const monthlyData2024 = <?php echo json_encode($monthlyData2024); ?>;
                const monthlyData2023 = <?php echo json_encode($monthlyData2023); ?>;
                
                if (period === '2024') {
                    monthlyTrendChart.data.labels = Object.keys(monthlyData2024);
                    monthlyTrendChart.data.datasets[0].data = Object.values(monthlyData2024);
                } else if (period === '2023') {
                    monthlyTrendChart.data.labels = Object.keys(monthlyData2023);
                    monthlyTrendChart.data.datasets[0].data = Object.values(monthlyData2023);
                }
                
                monthlyTrendChart.update();
            }
            
            function updatePeakHoursChart(period) {
                const peakHoursThisMonth = <?php echo json_encode($peakHoursThisMonth); ?>;
                const peakHoursLastMonth = <?php echo json_encode($peakHoursLastMonth); ?>;
                
                if (period === 'thisMonth') {
                    peakHoursChart.data.datasets[0].data = Object.values(peakHoursThisMonth);
                } else if (period === 'lastMonth') {
                    peakHoursChart.data.datasets[0].data = Object.values(peakHoursLastMonth);
                }
                
                peakHoursChart.update();
            }
            
            function updateDailyChart(period) {
                const dailyThisWeek = <?php echo json_encode($dailyThisWeek); ?>;
                const dailyLastWeek = <?php echo json_encode($dailyLastWeek); ?>;
                
                if (period === 'thisWeek') {
                    dailyChart.data.datasets[0].data = Object.values(dailyThisWeek);
                } else if (period === 'lastWeek') {
                    dailyChart.data.datasets[0].data = Object.values(dailyLastWeek);
                }
                
                dailyChart.update();
            }
            
            function updateBranchChart(period) {
                const branchAllTime = <?php echo json_encode($branchAllTime); ?>;
                const branchThisMonth = <?php echo json_encode($branchThisMonth); ?>;
                const branchLastMonth = <?php echo json_encode($branchLastMonth); ?>;
                
                if (period === 'allTime') {
                    branchChart.data.datasets[0].data = Object.values(branchAllTime);
                } else if (period === 'thisMonth') {
                    branchChart.data.datasets[0].data = Object.values(branchThisMonth);
                } else if (period === 'lastMonth') {
                    branchChart.data.datasets[0].data = Object.values(branchLastMonth);
                }
                
                branchChart.update();
            }
            
            // Toggle submenus
            const categoryBtns = document.querySelectorAll('.category__btn');
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.closest('.show-cat-btn');
                    const subMenu = parent.nextElementSibling;
                    subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
                });
            });
        });
    </script>
</body>
</html>