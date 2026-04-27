<?php
session_start();
include '../conn.php';

if (!isset($conn)) {
    die("Database connection is not established.");
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
$username = ("SELECT email FROM users WHERE id = ?");

    $reservationSales = 0;
    $resQuery = $conn->query("SELECT SUM(amount) AS total FROM reservations");
    if ($resQuery) {
        $row = $resQuery->fetch_assoc();
        $reservationSales = $row['total'];
    }
    $formattedReservationSales = number_format($reservationSales, 2);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $branch = $_POST['branch'];
    $date = $_POST['date'];
    $total_sales = $_POST['total_sales'];
    
    $stmt = $conn->prepare("INSERT INTO sales (branch, date, total_sales) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $branch, $date, $total_sales);
    
    if ($stmt->execute()) {
        $success_message = "Sales data added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Fetch branches for dropdown
$branches = [];
$branch_query = $conn->query("SELECT DISTINCT branch FROM reservations");
while ($row = $branch_query->fetch_assoc()) {
    $branches[] = $row['branch'];
}

// Fetch recent sales entries (last 6 entries)
$recent_sales = [];
$sales_query = $conn->query("SELECT * FROM sales ORDER BY date DESC, id DESC LIMIT 6");
if ($sales_query) {
    while ($row = $sales_query->fetch_assoc()) {
        $recent_sales[] = $row;
    }
}

// Fetch real sales data for the chart (last 7 days)
$chartData = ['labels' => [], 'datasets' => []];
$colors = [
    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
    '#858796', '#5a5c69', '#3a3b45', '#2e59d9', '#17a673'
];

// Get start and end dates (last 7 days including today)
$endDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-6 days'));

// Generate date labels for the last 7 days
$dateLabels = [];
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($startDate . " +$i days"));
    $dateLabels[] = date('M j', strtotime($date));
}

// Fetch all branches that have sales in the last 7 days
$branchStmt = $conn->prepare("SELECT DISTINCT branch FROM sales WHERE date BETWEEN ? AND ?");
$branchStmt->bind_param("ss", $startDate, $endDate);
$branchStmt->execute();
$branchResult = $branchStmt->get_result();

$allBranches = [];
while ($row = $branchResult->fetch_assoc()) {
    $allBranches[] = $row['branch'];
}

// Prepare datasets for each branch
$datasets = [];
foreach ($allBranches as $index => $branch) {
    // Initialize with zeros for all dates
    $branchData = array_fill(0, 7, 0);
    
    // Fetch sales data for this branch
    $salesStmt = $conn->prepare("SELECT DATE(date) as sales_date, SUM(total_sales) as total 
                                FROM sales 
                                WHERE branch = ? AND date BETWEEN ? AND ?
                                GROUP BY DATE(date)");
    $salesStmt->bind_param("sss", $branch, $startDate, $endDate);
    $salesStmt->execute();
    $salesResult = $salesStmt->get_result();
    
    // Fill in actual sales data
    while ($row = $salesResult->fetch_assoc()) {
        $date = $row['sales_date'];
        $dateIndex = array_search(date('M j', strtotime($date)), $dateLabels);
        if ($dateIndex !== false) {
            $branchData[$dateIndex] = (float)$row['total'];
        }
    }
    
    $colorIndex = $index % count($colors);
    $datasets[] = [
        'label' => $branch,
        'data' => $branchData,
        'borderColor' => $colors[$colorIndex],
        'backgroundColor' => $colors[$colorIndex] . '33', // Add opacity
        'tension' => 0.3,
        'fill' => true
    ];
}

$chartData = [
    'labels' => $dateLabels,
    'datasets' => $datasets
];

$chartDataJson = json_encode($chartData);

// Fetch data for pie chart (current month sales by branch)
$pieChartData = [];
$pieLabels = [];
$pieValues = [];
$pieColors = [];

$currentMonth = date('Y-m');
$pieQuery = $conn->query("SELECT branch, SUM(total_sales) as total 
                         FROM sales 
                         WHERE DATE_FORMAT(date, '%Y-%m') = '$currentMonth' 
                         GROUP BY branch");

if ($pieQuery) {
    while ($row = $pieQuery->fetch_assoc()) {
        $pieLabels[] = $row['branch'];
        $pieValues[] = (float)$row['total'];
        $pieColors[] = $colors[count($pieLabels) % count($colors)];
    }
}

$pieDataJson = json_encode([
    'labels' => $pieLabels,
    'values' => $pieValues,
    'colors' => $pieColors
]);

// Calculate total sales for the month
$totalMonthlySales = array_sum($pieValues);
$formattedTotalSales = number_format($totalMonthlySales, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard - Maria Gracias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/sales.css">
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
                        <a class="show-cat-btn" href="index.php">
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
                        <a class="show-cat-btn active" href="sales.php">
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
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="dashboard-title">Sales Dashboard</h1>
                    <p class="dashboard-subtitle">Track and analyze sales performance across branches</p>
                </div>
                <button class="action-btn" data-bs-toggle="modal" data-bs-target="#salesModal">
                    <i class="bi bi-plus-circle"></i> Add Sales Data
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
      <div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light">
                <i class="bi bi-shop"></i>
            </div>
            <div class="stat-title">Active Branches</div>
            <div class="stat-value"><?php echo  count($branches); ?></div>
        </div>
    </div>
    <!-- Existing stat cards... -->
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div class="stat-title">Reservation Sales</div>
            <div class="stat-value">₱<?php echo $formattedReservationSales; ?></div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light">
                <i class="bi bi-currency-exchange"></i>
            </div>
            <div class="stat-title">Monthly Sales</div>
            <div class="stat-value">₱<?php echo $formattedTotalSales; ?></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="stat-title">Avg. Daily Sales</div>
            <div class="stat-value">₱<?php 
                $avgDaily = $totalMonthlySales > 0 ? number_format($totalMonthlySales / date('t'), 2) : '0.00';
                echo $avgDaily;
            ?></div>
        </div>
    </div>
</div>
        
        
        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Pie Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">Sales Distribution by Branch</h3>
                        <div class="chart-actions">
                            <button class="chart-btn">This Month</button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="salesPieChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Recent Sales -->
            <div class="col-lg-6 mb-4">
                <div class="sales-history">
                    <div class="sales-history-header">
                        <h3>Recent Sales Entries</h3>
                    </div>
                    <div>
                        <?php if(count($recent_sales) > 0): ?>
                            <?php foreach($recent_sales as $sale): ?>
                                <div class="sales-history-item">
                                    <div class="branch-info">
                                        <div class="branch-icon">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div>
                                            <div class="branch-name"><?php echo htmlspecialchars($sale['branch']); ?></div>
                                            <div class="sales-date">
                                                <?php 
                                                    $date = new DateTime($sale['date']);
                                                    echo $date->format('F j, Y'); 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sales-amount">₱<?php echo number_format($sale['total_sales'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-sales">
                                <i class="bi bi-cash-coin no-sales-icon"></i>
                                <h5 class="no-sales-text">No Sales Data Found</h5>
                                <p>Start adding sales data to see records here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Line Chart -->
        <div class="chart-container mb-4">
            <div class="chart-header">
                <h3 class="chart-title">Sales Trends (Last 7 Days)</h3>
                <div class="chart-actions">
                    <button class="chart-btn">Last Week</button>
                    <button class="chart-btn">Last Month</button>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Sales Modal -->
    <div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesModalLabel">Record Daily Sales</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if(isset($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if(isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="salesForm">
                        <div class="mb-4">
                            <label for="branch" class="form-label">Branch</label>
                            <select class="form-select" id="branch" name="branch" required>
                                <option value="" selected disabled>Select Branch</option>
                                <?php foreach($branches as $branch): ?>
                                    <option value="<?php echo htmlspecialchars($branch); ?>"><?php echo htmlspecialchars($branch); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required 
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="total_sales" class="form-label">Total Sales (₱)</label>
                            <input type="number" class="form-control" id="total_sales" name="total_sales" 
                                   placeholder="Enter total sales amount" min="0" step="0.01" required>
                            <div class="form-text mt-2">Enter the total sales amount for the day</div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Sales Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Set default date to today
            document.getElementById('date').valueAsDate = new Date();
            
            // Pie Chart
            const pieCtx = document.getElementById('salesPieChart').getContext('2d');
            const pieData = {
                labels: <?php echo json_encode($pieLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($pieValues); ?>,
                    backgroundColor: <?php echo json_encode($pieColors); ?>,
                    borderWidth: 1
                }]
            };
            
            new Chart(pieCtx, {
                type: 'doughnut',
                data: pieData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Sales Trend Chart
            const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
            const trendData = <?php echo $chartDataJson; ?>;
            
            new Chart(trendCtx, {
                type: 'line',
                data: trendData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Form validation
            const form = document.getElementById('salesForm');
            form.addEventListener('submit', function(e) {
                const salesInput = document.getElementById('total_sales');
                if (salesInput.value <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid sales amount greater than zero.');
                    salesInput.focus();
                }
            });
            
            // Auto-close modal on success
            <?php if(isset($success_message)): ?>
                const modal = bootstrap.Modal.getInstance(document.getElementById('salesModal'));
                if (modal) modal.hide();
            <?php endif; ?>
            
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