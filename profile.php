<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit();
}

// Flash messages
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success text-center">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger text-center">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Get user info
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

$users = [];
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_result->num_rows > 0) {
    $users = $user_result->fetch_assoc();
} else {
    echo "User not found.";
}

// Get reservations
$reservations = [];
$res_stmt = $conn->prepare("SELECT * FROM reservations WHERE email = ?");
$res_stmt->bind_param("s", $email);
$res_stmt->execute();
$res_result = $res_stmt->get_result();

while ($row = $res_result->fetch_assoc()) {
    $reservations[] = $row;
}

$res_stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - Maria Gracias</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="icon" href="images/logo.png" type="image/x-icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
  <style>
      body {
          font-family: 'Poppins', sans-serif;
          background: #f9f9f9;
      }
      .navbar {
          background: #ffffff;
          border-bottom: 1px solid #ddd;
      }
      .profile-card {
          background: #fff;
          border-radius: 15px;
          padding: 20px;
          margin-bottom: 20px;
          box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      }
      .profile-icon {
          font-size: 3rem;
          color: #6c63ff;
      }
      .reservation-card {
          background: #fff;
          border-radius: 12px;
          padding: 15px;
          margin-bottom: 15px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      }
      .reservation-status {
          font-size: .8rem;
          padding: 3px 8px;
          border-radius: 5px;
          text-transform: capitalize;
      }
      .status-pending { background: #fff3cd; color: #856404; }
      .status-approved { background: #d4edda; color: #155724; }
      .status-cancelled { background: #f8d7da; color: #721c24; }
      .bottom-nav {
          position: fixed;
          bottom: 0;
          left: 0;
          right: 0;
          background: #fff;
          border-top: 1px solid #ddd;
          display: flex;
          justify-content: space-around;
          padding: 8px 0;
      }
      .bottom-nav a {
          text-decoration: none;
          color: #555;
          text-align: center;
          font-size: 0.85rem;
      }
      .bottom-nav i {
          display: block;
          font-size: 1.2rem;
      }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-light px-3">
  <a class="navbar-brand d-flex align-items-center" href="home.php">
    <img src="images/logo.png" alt="Logo" width="35" class="me-2">
    <span class="fw-bold">Maria Gracias</span>
  </a>
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
      <img src="https://ui-avatars.com/api/?name=<?= urlencode($email); ?>&background=random" class="rounded-circle me-2" width="35" height="35" alt="avatar">
      <span class="d-none d-sm-inline"><?= htmlspecialchars($username); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4 mb-5">
  <!-- Profile -->
  <div class="profile-card text-center">
    <div class="profile-icon mb-2"><i class="bi bi-person-circle"></i></div>
    <h5><?= htmlspecialchars($username); ?></h5>
    <p class="text-muted mb-1"><?= htmlspecialchars($email); ?></p>
    <p class="mb-0"><?= htmlspecialchars($users['cnum'] ?? 'Not provided'); ?></p>
  </div>

  <!-- Reservations -->
  <h5 class="mb-3">My Reservations</h5>
  
  <?php if (count($reservations) > 0): ?>
      <?php foreach ($reservations as $reservation): 
          $status = strtolower($reservation['status']);
          $statusClass = ($status === 'pending') ? 'status-pending' : (($status === 'approved') ? 'status-approved' : 'status-cancelled');
          $isActionable = in_array($status, ['pending','approved']);
      ?>
      <div class="reservation-card">
          <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">#<?= htmlspecialchars($reservation['reservation_code']); ?></h6>
              <span class="reservation-status <?= $statusClass ?>"><?= htmlspecialchars($reservation['status']); ?></span>
          </div>
          <p class="mb-1"><strong>Table:</strong> <?= htmlspecialchars($reservation['table_number']); ?></p>
          <p class="mb-1"><strong>Branch:</strong> <?= htmlspecialchars($reservation['branch']); ?></p>
          <p class="mb-1"><strong>Date:</strong> <?= date('M j, Y', strtotime($reservation['reserv_date'])); ?> <br><strong>Time:</strong> <?= date('g:i A', strtotime($reservation['reserv_time'])); ?></p>
          <p class="mb-2"><strong>Amount:</strong> ₱<?= number_format($reservation['amount'],2); ?></p>
         <?php if ($isActionable): ?>
  <div class="d-flex gap-2">
      <a href="edit_reservation.php?code=<?= urlencode($reservation['reservation_code']); ?>" class="btn btn-outline-primary btn-sm w-100">
          <i class="bi bi-pencil"></i> Edit
      </a>
      <form action="cancel_reservation.php" method="POST" class="w-100">
          <input type="hidden" name="reservation_id" value="<?= $reservation['user_id']; ?>">
          <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirmCancel()">
              <i class="bi bi-x-circle"></i> Cancel
          </button>
      </form>
  </div>
<?php endif; ?>
      </div>
      <?php endforeach; ?>
  <?php else: ?>
      <div class="text-center text-muted mt-4">
          <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
          <p>No reservations found</p>
          <a href="home.php#RV" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Make a Reservation</a>
      </div>
  <?php endif; ?>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
  <a href="home.php"><i class="bi bi-house-door"></i>Home</a>
  <a href="home.php#RV"><i class="bi bi-calendar-plus"></i>Reserve</a>
  <a href="home.php#menu"><i class="bi bi-calendar-check"></i>Menu</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmCancel() {
    return confirm("Are you sure you want to cancel this reservation?");
}
</script>
</body>
</html>
