<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
// Get the admin's username for the navbar  
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Get the user ID from URL
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

// Get user details
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

// Get user's reservations by email
$reservations_query = "SELECT * FROM reservations WHERE email = ? ORDER BY reserv_date DESC";
$stmt = $conn->prepare($reservations_query);
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/view_user.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-start">
            <div class="sidebar-head">
                <a href="" class="logo-wrapper" title="Home">
                    <img src="../images/logo.png" alt="Maria Gracias Logo" 
                         class="icon logo" aria-hidden="true"
                         style="height: 6.0em; width: auto; max-height: 100px;">
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
                        <a class="show-cat-btn" href="ratings.php">
                            <span class="icon paper" aria-hidden="true"><i class="bi bi-star"></i></span>Ratings
                        </a> 
                    </li>
                    <li>
                        <a class="show-cat-btn" href="sales.php">
                            <span class="icon paper" aria-hidden="true"><i class="bi bi-graph-up"></i></span>Sales
                        </a> 
                    </li>
                </ul>
                <span class="system-menu__title">system</span>
                <ul class="sidebar-body-menu">
            
                    <li>
                         <a href="manage_users.php" class="active"><span class="icon edit" aria-hidden="true"><i class="bi bi-people"></i></span>Users</a>
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
                    <h1 class="dashboard-title">View User</h1>
                    <p class="dashboard-subtitle">User details and reservation history</p>
                </div>
                <a href="manage_users.php" class="action-btn">
                    <i class="bi bi-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-grid fade-in delay-1">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light">
                    <i class="bi bi-person"></i>
                </div>
                <div class="stat-title">User Status</div>
                <div class="stat-value">
                    <?php 
                    $statusClass = 'status-active';
                    if ($user['status'] == 'pending') {
                        $statusClass = 'status-pending';
                    } elseif ($user['status'] == 'inactive') {
                        $statusClass = 'status-inactive';
                    }
                    ?>
                    <span class="status-badge <?= $statusClass ?>">
                        <?php echo ucfirst($user['status']); ?>
                    </span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success-light">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-title">Total Reservations</div>
                <div class="stat-value"><?= $reservations->num_rows ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info-light">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="stat-title">Email</div>
                <div class="stat-value" style="font-size: 1rem;"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning-light">
                    <i class="bi bi-telephone"></i>
                </div>
                <div class="stat-title">Contact</div>
                <div class="stat-value" style="font-size: 1rem;"><?= htmlspecialchars($user['cnum']) ?></div>
            </div>
        </div>
        
        <!-- User Details -->
        <div class="row mb-4">
            <div class="col-md-12 fade-in delay-2">
                <div class="user-info-card">
                    <h2 class="user-info-title">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </h2>
                    
                    <div class="user-detail">
                        <div class="detail-label">User ID:</div>
                        <div class="detail-value"><?php echo substr($user['id'], 3); ?></div>
                    </div>
                    
                    <div class="user-detail">
                        <div class="detail-label">Email:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    
                    <div class="user-detail">
                        <div class="detail-label">Contact Number: </div>
                        <div class="detail-value"><?php echo htmlspecialchars($user['cnum']); ?></div>
                    </div>
                    
                    <div class="user-detail">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <?php 
                            $statusClass = 'status-active';
                            if ($user['status'] == 'pending') {
                                $statusClass = 'status-pending';
                            } elseif ($user['status'] == 'inactive') {
                                $statusClass = 'status-inactive';
                            }
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reservations Section -->
        <div class="user-card fade-in delay-3">
            <div class="user-card-header">
                <h2 class="user-card-title">Reservation History</h2>
            </div>
            
            <?php if ($reservations->num_rows > 0) : ?>
                <div class="table-responsive">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Reservation ID</th>
                                <th>Created At</th>
                                <th>Reservation Code</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Branch</th>
                                <th>Table Number</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reservation = $reservations->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $reservation['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($reservation['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['reservation_code']); ?></td>
                                    <td><?php echo (($reservation['reserv_date'])); ?></td>
                                    <td><?php echo (($reservation['reserv_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['branch']); ?></td>
                                    <td><?php echo $reservation['table_number']; ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = 'status-active';
                                        if ($reservation['status'] == 'pending') {
                                            $statusClass = 'status-pending';
                                        } elseif ($reservation['status'] == 'cancelled') {
                                            $statusClass = 'status-inactive';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?php echo ucfirst($reservation['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="no-reservations fade-in">
                    <i class="bi bi-calendar-x no-reservations-icon"></i>
                    <h3 class="no-reservations-text">No Reservations Found</h3>
                    <p>This user hasn't made any reservations yet</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="manage_users.php" class="back-btn">
                    <i class="bi bi-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
            
            // Animate elements on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.stat-card, .user-info-card, .user-card').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>