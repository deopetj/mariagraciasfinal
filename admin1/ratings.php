<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$messages = [];
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

$username = ("SELECT email FROM users WHERE id = ?");


// Search functionality
$search = '';
$is_search_active = false;
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string(trim($_GET['search']));
    $is_search_active = true;
}

// Get ratings data with improved JOIN logic
$sql = "SELECT r.*, u.cnum
        FROM ratings r 
        LEFT JOIN users u ON r.id = u.id";


// Apply search filter if active

if ($is_search_active) {
    $sql .= " WHERE 
        r.email LIKE '%$search%' OR 
        u.cnum LIKE '%$search%' OR 
        r.comment LIKE '%$search%' OR 
        r.branch LIKE '%$search%'";
}
$result = $conn->query($sql);

// Debugging: Show SQL error if any
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get chart data for booking experience
$chartQuery = "SELECT 
    rating,
    COUNT(*) as rating_count 
    FROM ratings 
    GROUP BY rating
    ORDER BY rating ASC";
$chartResult = $conn->query($chartQuery);

// Initialize rating counts
$ratingCounts = array_fill(1, 5, 0);
while ($row = $chartResult->fetch_assoc()) {
    $ratingCounts[$row['rating']] = $row['rating_count'];
}

// Get total ratings count for stats
$totalRatings = $conn->query("SELECT COUNT(*) as total FROM ratings")->fetch_assoc()['total'];
// Get chart data for booking experience
$chartQuery = "SELECT 
    rating,
    COUNT(*) as rating_count 
    FROM ratings 
    GROUP BY rating
    ORDER BY rating ASC";
$chartResult = $conn->query($chartQuery);

// Initialize rating counts
$ratingCounts = array_fill(1, 5, 0);
while ($row = $chartResult->fetch_assoc()) {
    $ratingCounts[$row['rating']] = $row['rating_count'];
}

// NEW: Calculate real-time metrics
// Total ratings
$totalRatings = $conn->query("SELECT COUNT(*) as total FROM ratings")->fetch_assoc()['total'];

// Average rating
$avgResult = $conn->query("SELECT AVG(rating) as avg_rating FROM ratings");
$avgRow = $avgResult->fetch_assoc();
$avgRating = $avgRow['avg_rating'] ? number_format($avgRow['avg_rating'], 1) : '0.0';

// Positive feedback percentage (4-5 stars)
$positiveResult = $conn->query(
    "SELECT COUNT(*) as positive_count FROM ratings WHERE rating >= 4"
);
$positiveRow = $positiveResult->fetch_assoc();
$positivePercent = $totalRatings > 0 
    ? round(($positiveRow['positive_count'] / $totalRatings) * 100)
    : 0;

// Ratings with comments
$commentsResult = $conn->query(
    "SELECT COUNT(*) as with_comments FROM ratings WHERE comment <> ''"
);
$commentsRow = $commentsResult->fetch_assoc();
$withComments = $commentsRow['with_comments'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ratings - Maria Gracias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/ratings.css">
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
                            <span class="icon home" aria-hidden="true"><i class="bi bi-speedometer2"></i></span>
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
                    <h1 class="dashboard-title">Manage Ratings</h1>
                    <p class="dashboard-subtitle">View and analyze customer feedback and ratings</p>
                </div>
                
            </div>
        </div>  
        <!-- Stats Section -->
    <div class="stats-grid fade-in delay-1">
    <div class="stat-card">
        <div class="stat-icon bg-primary-light">
            <i class="bi bi-star"></i>
        </div>
        <div class="stat-title">Total Ratings</div>
        <div class="stat-value"><?= $totalRatings ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success-light">
            <i class="bi bi-star-fill"></i>
        </div>
        <div class="stat-title">Avg. Rating</div>
        <div class="stat-value"><?= $avgRating ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info-light">
            <i class="bi bi-emoji-smile"></i>
        </div>
        <div class="stat-title">Positive Feedback</div>
        <div class="stat-value"><?= $positivePercent ?>%</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-light">
            <i class="bi bi-chat"></i>
        </div>
        <div class="stat-title">With Comments</div>
        <div class="stat-value"><?= $withComments ?></div>
    </div>
</div>
        
        <!-- Chart Section -->
        <div class="chart-container fade-in delay-2">
            <div class="chart-header ">
                <h3 class="chart-title">Rating Distribution</h3>
                <div class="chart-actions">
                    <button class="chart-btn">This Month</button>
                    <button class="chart-btn">All Time</button>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="experienceChart"></canvas>
            </div>
        </div>
        
        
        <!-- Ratings Table -->
        <div class="ratings-table fade-in delay-5">
             <div class="search-form fade-in delay-3">
            <form method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by email, contact number, or comment..." 
                           value="<?php echo htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if ($is_search_active) : ?>
                        <a href="ratings.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <?php if ($is_search_active) : ?>
            <div class="alert alert-info mb-4 fade-in delay-4">
                Found <?php echo $result->num_rows ?> results for "<?php echo htmlspecialchars($search) ?>"
            </div>
        <?php endif; ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Branch</th>
                        <th>Created At</th>
                    </tr>
                </thead>
           <tbody>
    <?php if ($result->num_rows > 0) : ?>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td>
                    <div><?php echo htmlspecialchars($row['email']) ?></div>
                    <div><?php echo htmlspecialchars($row['contact_number']) ?></div>    
                </td>
                <td>
                    <?php 
                    $rating = $row['rating'] ?? 0;
                    $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                    echo '<span style="color: #ffd700;">' . $stars . '</span>';
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row['comment'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($row['branch'] ?? '') ?></td>
                <td>
                    <?php
                    $dateString = $row['created_at'] ?? null;
                    if ($dateString) {
                        echo date('M d, Y h:i A', strtotime($dateString));
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else : ?>
        <tr>
            <td colspan="5" class="text-center">No ratings found</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
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
            
            // Experience Rating Chart
            const ctx = document.getElementById('experienceChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
                    datasets: [{
                        label: 'Number of Ratings',
                        data: <?php echo json_encode(array_values($ratingCounts)) ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(255, 205, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(54, 162, 235, 0.7)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'x',
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Ratings',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Rating Stars',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 6
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
    <script>
    // Save scroll position before leaving or submitting form
    window.addEventListener("beforeunload", function () {
        localStorage.setItem("scrollPos", window.scrollY);
    });

    // Restore scroll position after page load
    window.addEventListener("load", function () {
        const scrollPos = localStorage.getItem("scrollPos");
        if (scrollPos) {
            window.scrollTo(0, parseInt(scrollPos));
            localStorage.removeItem("scrollPos"); // Clear after restoring
        }
    });
</script>
    </body>
</html>