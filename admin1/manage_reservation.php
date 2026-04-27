<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require '../conn.php';
require '../vendor/autoload.php';

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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch admin username
$messages = [];

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get counts for each status
$statusQuery = "SELECT status, COUNT(*) as count FROM reservations GROUP BY status";
$statusResult = $conn->query($statusQuery);
$statusCounts = [
    'Approved' => 0,
    'Pending' => 0,
    'Declined' => 0,
    'Cancelled' => 0
];

while ($row = $statusResult->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['count'];
}

// Handle approval action
if (isset($_POST['approve'])) {
    $id = intval($_POST['id']);

    $query = "UPDATE reservations SET status = 'Approved' WHERE user_id = $id";
    if ($conn->query($query)) {
        $emailQuery = "SELECT email FROM reservations WHERE user_id = $id";
        $result = $conn->query($emailQuery);
        $email = $result->fetch_assoc()['email'];

        sendEmail($email, 'Reservation Approved', 'Your reservation has been approved, thanks for dining with us! -Maria Gracias Samgyupsal');

        header('Location: manage_reservation.php');
        exit;
    }
}

// Handle decline action
if (isset($_POST['decline'])) {
    $id = intval($_POST['id']);

    $query = "UPDATE reservations SET status = 'Declined' WHERE user_id = $id";
    if ($conn->query($query)) {
        $emailQuery = "SELECT email FROM reservations WHERE user_id = $id";
        $result = $conn->query($emailQuery);
        $email = $result->fetch_assoc()['email'];

        sendEmail($email, 'Reservation Declined', 'Your reservation has been declined.');

        header('Location: manage_reservation.php');
        exit;
    }
}

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'deopetj@gmail.com';
        $mail->Password = 'yasd dqmg fvrq lpct';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('deopetj@gmail.com', 'MARIA GRACIAS SAMGYUPSAL');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

$search = '';
$is_search_active = false;
$date_condition = '';
$time_condition = '';
$search_terms = [];
$date_search = '';
$time_search = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $raw_search = trim($_GET['search']);
    $is_search_active = true;
    
    // Improved date parsing
    $date_patterns = [
        // Common date formats
        '/(\d{4}-\d{2}-\d{2})/', // YYYY-MM-DD
        '/(\d{1,2}\/\d{1,2}\/\d{4})/', // MM/DD/YYYY
        '/(\d{1,2}-\d{1,2}-\d{4})/', // MM-DD-YYYY
        // Month names with days
        '/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*\s+\d{1,4}/i',
        // Full month names
        '/(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{1,4}/i'
    ];
    
    $date_found = false;
    foreach ($date_patterns as $pattern) {
        if (preg_match($pattern, $raw_search, $date_matches)) {
            $date_str = $date_matches[0];
            $timestamp = strtotime($date_str);
            if ($timestamp !== false) {
                $date_search = date('Y-m-d', $timestamp);
                $raw_search = str_replace($date_str, '', $raw_search);
                $date_found = true;
                break;
            }
        }
    }
    
    // If no specific date found, check if it's just a month name
    if (!$date_found) {
        $month_pattern = '/(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)/i';
        if (preg_match($month_pattern, $raw_search, $month_matches)) {
            $month_str = $month_matches[0];
            $month_num = date('m', strtotime($month_str));
            if ($month_num) {
                $date_search = $month_num; // Store as month number for later use
                $raw_search = str_replace($month_str, '', $raw_search);
                $date_found = true;
            }
        }
    }
    
    // Time parsing (improved)
    $time_patterns = [
        '/(\d{1,2}:\d{2}\s*(am|pm)?)/i', // 2:30 PM or 14:30
        '/(\d{1,2}\s*(am|pm))/i' // 2 PM or 2pm
    ];
    
    foreach ($time_patterns as $pattern) {
        if (preg_match($pattern, $raw_search, $time_matches)) {
            $time_str = $time_matches[0];
            $timestamp = strtotime($time_str);
            if ($timestamp !== false) {
                $time_search = date('H:i', $timestamp);
                $raw_search = str_replace($time_str, '', $raw_search);
                break;
            }
        }
    }
    
    // Process remaining search terms
    $search_terms = array_filter(preg_split('/\s+/', trim($raw_search)), function($term) {
        return !empty(trim($term)) && strlen(trim($term)) > 1;
    });
    
    $search_terms = array_map(function($term) use ($conn) {
        return $conn->real_escape_string(trim($term));
    }, $search_terms);
}

// Build query with improved date search
// Only fetch complete reservations (no NULLs in critical fields)
$query = "SELECT * FROM reservations WHERE 
    branch IS NOT NULL AND branch != '' 
    AND table_number IS NOT NULL AND table_number != '' 
    AND reserv_date IS NOT NULL AND reserv_date != '' 
    AND reserv_time IS NOT NULL AND reserv_time != '' 
    AND email IS NOT NULL AND email != '' 
    AND contact_number IS NOT NULL AND contact_number!= '' 
    AND status IS NOT NULL AND status != ''";
$conditions = [];



if ($is_search_active) {
    // Date condition - handle both specific dates and month searches
    if (!empty($date_search)) {
        if (is_numeric($date_search)) {
            // Month search (date_search contains month number)
            $conditions[] = "MONTH(reserv_date) = '$date_search'";
        } else {
            // Specific date search
            $conditions[] = "reserv_date = '$date_search'";
        }
    }
    
    // Time condition
    if (!empty($time_search)) {
        $conditions[] = "reserv_time LIKE '$time_search%'";
    }
    
    // Other search terms
    if (!empty($search_terms)) {
        $term_conditions = [];
        foreach ($search_terms as $term) {
            $term_conditions[] = "(
                email LIKE '%$term%' 
                OR contact_number LIKE '%$term%' 
                OR branch LIKE '%$term%'
                OR table_number LIKE '%$term%'
                OR reservation_code LIKE '%$term%'
                OR message LIKE '%$term%'
            )";
        }
        $conditions[] = "(" . implode(' OR ', $term_conditions) . ")";
    }
    
if (!empty($conditions)) {
    // Append filters after our non-null check
    $query .= " AND " . implode(' AND ', $conditions);
}
}

$query .= " ORDER BY user_id DESC";

$reservation_result = $conn->query($query);

// Calculate base URL for images
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations - Maria Gracias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="../images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../css/reservation.css">
</head>
<body>
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
                        <a class="show-cat-btn active" href="manage_reservation.php">
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
      <div class="dashboard-header fade-in" style="background-color:#3057C9; padding: 40px; min-height: 110x;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="dashboard-title" style="color: white;">Manage Reservations</h1>
                    <p class="dashboard-subtitle" style="color: white;">View and manage customer reservations</p>
                </div>
            </div>
        </div>
        
        <!-- Stats Overview --> 
        <div class="stats-grid fade-in delay-1">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Total Reservations</div>
                    <div class="stat-value"><?php echo $reservation_result->num_rows; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success-light">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Approved</div>
                    <div class="stat-value"><?= $statusCounts['Approved'] ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning-light">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Pending</div>
                    <div class="stat-value"><?= $statusCounts['Pending'] ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-danger-light">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Declined</div>
                    <div class="stat-value"><?= $statusCounts['Declined'] ?></div>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="search-section fade-in delay-2">
            <h3 class="mb-3">Search Reservations</h3>
            <form method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by email, contact number, branch, date, or time..."
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <?php if ($is_search_active) : ?>
                        <a href="manage_reservation.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_search_active) : ?>
    <div class="search-hints mt-3">
        <?php if (!empty($date_search)) : ?>
            <?php if (is_numeric($date_search)) : ?>
                <span class="badge bg-info">
                    <i class="bi bi-calendar me-1"></i> 
                    Month: <?= date('F', mktime(0, 0, 0, $date_search, 1)) ?>
                </span>
            <?php else : ?>
                <span class="badge bg-info">
                    <i class="bi bi-calendar me-1"></i> 
                    Date: <?= date('M j, Y', strtotime($date_search)) ?>
                </span>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($time_search)) : ?>
            <span class="badge bg-info">
                <i class="bi bi-clock me-1"></i> 
                Time: <?= date('g:i A', strtotime($time_search)) ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($search_terms)) : ?>
            <span class="badge bg-secondary">
                <i class="bi bi-tags me-1"></i> 
                Terms: <?= htmlspecialchars(implode(', ', $search_terms)) ?>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>
            </form>
        </div>
        
        <!-- Reservations List -->
        <div class="fade-in delay-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Reservation List</h3>
                <div>
                    <span class="me-2">Filter:</span>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button class="btn btn-outline-primary" data-filter="approved">Approved</button>
                        <button class="btn btn-outline-primary" data-filter="pending">Pending</button>
                        <button class="btn btn-outline-primary" data-filter="declined">Declined</button>
                    </div>
                </div>
            </div>
            
            <?php if ($reservation_result->num_rows > 0) : ?>
                <div class="reservations-grid">
                    <?php while ($row = $reservation_result->fetch_assoc()) { 
                        $statusClass = strtolower($row['status']);
                        $isPending = $row['status'] === 'Pending';
                    ?>
                    <div class="reservation-card <?= $statusClass ?>">
                        <div class="reservation-header">
                            <h4 class="reservation-title">Reservation #<?= $row['reservation_code'] ?></h4>
                            <span class="reservation-status status-<?= $statusClass ?>">
                                <?= $row['status'] ?>
                            </span>
                        </div>
                        
                        <div class="reservation-info">
                            <div class="info-item">
                                <span class="info-label">Customer</span>
                                <span class="info-value"><?= htmlspecialchars($row['email']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?= htmlspecialchars($row['contact_number']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Branch</span>
                                <span class="info-value"><?= htmlspecialchars($row['branch']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Table</span>
                                <span class="info-value">#<?= $row['table_number'] ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date</span>
                                <span class="info-value"><?= date('M j, Y', strtotime($row['reserv_date'])) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Time</span>
                                <span class="info-value"><?= date('g:i A', strtotime($row['reserv_time'])) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Created At</span>
                                <span class="info-value"><?= date('M j, Y H:i', strtotime($row['created_at'])) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Message</span>
                                <span class="info-value"><?= $row['message'] ?></span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if ($row['proof_of_payment']) : ?>
                                    <button class="btn btn-sm btn-outline-info view-proof-btn" 
                                            data-image="<?= $base_url . htmlspecialchars($row['proof_of_payment']) ?>"
                                            data-code="<?= $row['reservation_code'] ?>">
                                        <i class="bi bi-receipt"></i> View Proof of Payment
                                    </button>
                                <?php else : ?>
                                    <span class="text-muted">No proof of payment</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($isPending) : ?>
                            <div class="reservation-actions">
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" name="approve" class="action-btn approve">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" name="decline" class="action-btn decline">
                                        <i class="bi bi-x-lg"></i> Decline
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                
                <!-- Single Proof of Payment Modal -->
                <div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Proof of Payment - Reservation #<span id="modalReservationCode"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="proofImage" src="" class="img-fluid proof-image" alt="Proof of Payment">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #e2e6ea;"></i>
                    <h4 class="mt-3">No reservations found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <a href="manage_reservation.php" class="btn btn-primary mt-2">
                        <i class="bi bi-arrow-repeat"></i> Show All Reservations
                    </a>
                </div>
            <?php endif; ?>
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
            
            // Filter buttons
            $('.btn-group .btn').click(function() {
                $('.btn-group .btn').removeClass('active');
                $(this).addClass('active');
                
                const filter = $(this).data('filter');
                if (filter === 'all') {
                    $('.reservation-card').show();
                } else {
                    $('.reservation-card').each(function() {
                        if ($(this).hasClass(filter)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
            
            // Handle proof of payment view button clicks
            $('.view-proof-btn').click(function() {
                const imageUrl = $(this).data('image');
                const reservationCode = $(this).data('code');
                
                // Set modal content
                $('#modalReservationCode').text(reservationCode);
                $('#proofImage').attr('src', imageUrl);
                
                // Show the modal
                $('#proofModal').modal('show');
            });
            
            // Clean up when modal is closed
            $('#proofModal').on('hidden.bs.modal', function () {
                $('#proofImage').attr('src', '');
                $('#modalReservationCode').text('');
            });
        });
    </script>
</body>
</html>