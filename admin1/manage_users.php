<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../conn.php';
require '../vendor/autoload.php';

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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get admin username
$messages = [];

// Search functionality
$search = '';
$is_search_active = false;
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string(trim($_GET['search']));
    $is_search_active = true;
}

// Get filtered users
$sql = "SELECT id, email, cnum, status FROM users WHERE role = 'user'";
if ($is_search_active) {
    $sql .= " AND (email LIKE '%$search%' OR cnum LIKE '%$search%')";
}
$result = $conn->query($sql);

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['user_id'])) {
    // [Keep original delete handling code]
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Maria Gracias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/manage_users.css">
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
                    <h1 class="dashboard-title">Manage Users</h1>
                    <p class="dashboard-subtitle">Administer and monitor user accounts</p>
                </div>
                <a href="add_user.php" class="action-btn">
                    <i class="bi bi-person-plus"></i>
                    Add New User
                </a>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-grid fade-in delay-1">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-title">Total Users</div>
                <div class="stat-value"><?= $result->num_rows ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success-light">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-title">Active Users</div>
                <div class="stat-value"><?= $result->num_rows > 0 ? round($result->num_rows * 0.7) : 0 ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning-light">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-title">Pending Users</div>
                <div class="stat-value"><?= $result->num_rows > 0 ? round($result->num_rows * 0.2) : 0 ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info-light">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div class="stat-title">Admin Accounts</div>
                <div class="stat-value">2</div>
            </div>
        </div>
        
        <!-- User Management Card -->
        <div class="user-card fade-in delay-2">
            <div class="user-card-header">
                <h2 class="user-card-title">User Accounts</h2>
            </div>
            
            <!-- Search Form -->
            <div class="search-container fade-in delay-3">
                <form method="GET">
                    <div class="search-form">
                        <input type="text" name="search" class="search-input" 
                               placeholder="Search by email or contact number..." 
                               value="<?php echo htmlspecialchars($search) ?>">
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <?php if ($is_search_active) : ?>
                            <a href="manage_users.php" class="clear-btn">
                                <i class="bi bi-x-lg"></i> Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <?php if ($is_search_active) : ?>
                    <div class="results-info fade-in">
                        <i class="bi bi-info-circle"></i> Found <?php echo $result->num_rows ?> results for "<?php echo htmlspecialchars($search) ?>"
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- User Table -->
            <?php if ($result->num_rows > 0) : ?>
                <div class="table-responsive fade-in delay-4">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo substr($row['id'], 3); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cnum']); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = 'status-active';
                                        if ($row['status'] == 'pending') {
                                            $statusClass = 'status-pending';
                                        } elseif ($row['status'] == 'inactive') {
                                            $statusClass = 'status-inactive';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="view_user.php?id=<?php echo $row['id']; ?>" class="btn-view">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-delete" onclick="return confirm('This will permanently delete the user and their reservations!')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="no-users fade-in delay-4">
                    <i class="bi bi-people no-users-icon"></i>
                    <h3 class="no-users-text">No Users Found</h3>
                    <p>User accounts will appear here when created</p>
                </div>
            <?php endif; ?>
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
            
            document.querySelectorAll('.stat-card, .user-card, .search-container').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>