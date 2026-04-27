<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../conn.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
// Get the admin's username for the navbar  $user_id = $_SESSION['id'];
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

$messages = [];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $cnum = trim($_POST['cnum'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $role = $_POST['role'] ?? 'user'; // Get role from form

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($cnum) < 9) {
        $errors[] = "Contact number must be at least 9 digits.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Fixed: Added role to SQL query and binding
        $stmt = $conn->prepare("INSERT INTO users (email, cnum, password, status, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $cnum, $hashedPassword, $status, $role);

        if ($stmt->execute()) {
            $messages[] = "User successfully added as " . ucfirst($role) . "!";
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Maria Gracias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/adduser.css">

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
                        <a class="active" href="manage_users.php"><span class="icon edit" aria-hidden="true"><i class="bi bi-people"></i></span>Users</a>
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
                    <h1 class="dashboard-title">Add New User</h1>
                    <p class="dashboard-subtitle">Create a new user account with appropriate permissions</p>
                </div>
                <a href="manage_users.php" class="action-btn">
                    <i class="bi bi-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>
        
        <!-- User Form Card -->
        <div class="user-card fade-in delay-1">
            <div class="user-card-header">
                <h2 class="user-card-title">User Information</h2>
                <span class="status-badge status-pending">New Account</span>
            </div>
            
            <?php if (!empty($messages)): ?>
                <div class="alert alert-success fade-in"><?= implode("<br>", $messages) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger fade-in"><?= implode("<br>", $errors) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <small class="text-muted">Enter a valid email address</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contact Number:</label>
                    <input type="text" name="cnum" class="form-control" required value="<?= htmlspecialchars($_POST['cnum'] ?? '') ?>">
                    <small class="text-muted">At least 7 digits</small>
                </div>
                
                <div class="mb-3 password-container">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="bi bi-eye"></i>
                    </span>
                    <small class="text-muted">Minimum 6 characters</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Account Type:</label>
                    <div class="role-selection">
                        <div class="role-option user" onclick="selectRole('user')">
                            <div class="role-icon user">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="role-title">Standard User</div>
                            <div class="role-description">Can make reservations only</div>
                        </div>
                        <div class="role-option admin glowing" onclick="selectRole('admin')">
                            <div class="role-icon admin">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <div class="role-title">Administrator</div>
                            <div class="role-description">Full access to admin dashboard</div>
                        </div>
                    </div>
                    <input type="hidden" name="role" id="roleInput" value="<?= $_POST['role'] ?? 'user' ?>">
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Status:</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= (($_POST['status'] ?? '') == 'active' ? 'selected' : '') ?>>Active</option>
                        <option value="pending" <?= (($_POST['status'] ?? '') == 'pending' || empty($_POST) ? 'selected' : '') ?>>Pending</option>
                        <option value="inactive" <?= (($_POST['status'] ?? '') == 'inactive' ? 'selected' : '') ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <a href="manage_users.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Add User
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Info Cards -->
        <div class="stats-grid fade-in delay-2">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-title">Total Users</div>
                <div class="stat-value">127</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-info-light">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div class="stat-title">Administrators</div>
                <div class="stat-value">5</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon bg-warning-light">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-title">Pending Users</div>
                <div class="stat-value">12</div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            
            // Initialize role selection
            const initialRole = document.getElementById('roleInput').value;
            selectRole(initialRole);
            
            // Animate elements on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.user-card, .stat-card').forEach(el => {
                observer.observe(el);
            });
        });
        
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('.toggle-password i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        
        function selectRole(role) {
            // Update hidden input
            document.getElementById('roleInput').value = role;
            
            // Update UI
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            document.querySelectorAll(`.role-option.${role}`).forEach(option => {
                option.classList.add('selected');
            });
            
            // Update badge in header
            const badge = document.querySelector('.status-badge');
            if (role === 'admin') {
                badge.innerHTML = '<i class="bi bi-shield-lock"></i> Administrator';
                badge.className = 'status-badge role-admin';
            } else {
                badge.innerHTML = '<i class="bi bi-person"></i> Standard User';
                badge.className = 'status-badge role-user';
            }
        }
    </script>
</body>
</html>