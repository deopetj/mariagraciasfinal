<?php
session_start();
include '../conn.php';

// Check admin logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// fetch admin info
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Handle reservation insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch = $_POST['branch'] ?? '';
    $table_number = intval($_POST['table_number'] ?? 0);
    $reserv_date = $_POST['reserv_date'] ?? '';
    $reserv_time = $_POST['reserv_time'] ?? '';
    $status = 'Approved'; // <-- automatically approve

    $sql = "INSERT INTO reservations (branch, table_number, reserv_date, reserv_time, status)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sisss", $branch, $table_number, $reserv_date, $reserv_time, $status);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Successfully added!";
        } else {
            $_SESSION['error'] = "Error inserting: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Prepare failed: " . $conn->error;
    }

    header("Location: admin_add_reservation.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin — Add Reservation | Maria Gracias</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"/>
    <link rel="icon" href="../images/logo.png" type="image/x-icon"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"/>
    <link rel="stylesheet" href="../css/manage_tables.css"/>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .card { border-radius: 10px; }
        .time-slot { display:inline-block; margin:3px; padding:8px 12px; border-radius:6px; cursor:pointer; border:1px solid #ccc; }
        .time-slot.available:hover { background-color:#198754; color:#fff; }
        .time-slot.booked { background-color:#dc3545; color:#fff; cursor:not-allowed; }
        .time-slot.selected { background-color:#0d6efd; color:#fff; }
        .main-content { padding: 1.5rem; margin-left: 260px; }
        @media (max-width: 991px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-start">
            <div class="sidebar-head">
                <a href="index.php" class="logo-wrapper" title="Home">
                    <img src="../images/logo.png" alt="Maria Gracias Logo" class="icon logo" style="height: 64px; width:auto;">
                    <div class="logo-text">
                        <span class="logo-title">Maria Gracias</span>
                        <span class="logo-subtitle">Dashboard</span>
                    </div>
                </a>
            </div>
            <div class="sidebar-body">
                <ul class="sidebar-body-menu">
                    <span class="system-menu__title">Dashboard</span>
                    <li><a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a href="manage_reservation.php"><i class="bi bi-calendar-check"></i> Reservations</a></li>
                    <li><a href="manage_tables.php"><i class="bi bi-grid"></i> Manage Tables</a></li>
                    <li><a href="sales.php"><i class="bi bi-graph-up"></i> Sales</a></li>
                    <li><a href="ratings.php"><i class="bi bi-star"></i> Ratings</a></li>
                </ul>
                <span class="system-menu__title">System</span>
                <ul class="sidebar-body-menu">
                    <li><a href="manage_users.php"><i class="bi bi-people"></i> Users</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-nav mb-3 d-flex justify-content-end align-items-center">
            <div class="nav-user-wrapper d-flex align-items-center gap-2">
                <a class="nav-link text-muted"><?php echo htmlspecialchars($email); ?></a>
                <button class="nav-user-btn border-0 bg-transparent">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($email); ?>&background=random" class="nav-user-img rounded-circle" width="40" height="40" alt="<?php echo htmlspecialchars($username); ?>">
                </button>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="mb-0">Admin — Add Occupied Table</h3>
                        <small class="text-muted">Add occupied table to prevent double bookings</small>
                    </div>
                    <div>
                        <a href="manage_tables.php" class="btn btn-outline-secondary" style="background-color:#5979D6; color:white;">Back to Tables</a>
                    </div>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form method="POST" id="adminReservationForm">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <select class="form-select" name="branch" id="branchSelect" required>
                                <option value="" disabled selected>Select Branch</option>
                                <?php
                                $brows = $conn->query("SELECT name FROM branches ORDER BY name");
                                if ($brows) {
                                    while ($b = $brows->fetch_assoc()) {
                                        $n = htmlspecialchars($b['name']);
                                        echo "<option value=\"{$n}\">{$n}</option>";
                                    }
                                } else {
                                    echo '<option value="Del Carmen">Del Carmen</option>
                                          <option value="Lapaz">Lapaz</option>
                                          <option value="Mandurriao">Mandurriao</option>
                                          <option value="Lapuz">Lapuz</option>
                                          <option value="CPU">CPU</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Table Number</label>
                            <select class="form-select" name="table_number" id="tableSelect" required>
                                <option value="" disabled selected>Select Branch First</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control" name="reserv_date" id="reserv_date" required readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Available Time Slots</label>
                            <div class="time-slots-container mt-2 p-2 border rounded bg-white">
                                <div class="loading-message text-muted">Select branch, table, and date first</div>
                            </div>
                            <!-- ✅ Hidden field for selected time -->
                            <input type="hidden" name="reserv_time" id="reserv_time">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="background-color:#5979D6;">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    flatpickr("#reserv_date", { dateFormat: "Y-m-d", minDate: "today", allowInput: true });

    const timeSlotContainer = $('.time-slots-container');

    function formatTime(hours) {
        const isHalf = hours % 1 !== 0;
        const hour = Math.floor(hours);
        const minutes = isHalf ? '30' : '00';
        const period = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes}${period}`;
    }

    function generateTimeSlots() {
        const slots = [];
        for (let hour = 9; hour < 21; hour += 1) {
            const start = `${String(Math.floor(hour)).padStart(2, '0')}:00`;
            slots.push({ start, display: `${formatTime(hour)} - ${formatTime(hour + 1)}` });
        }
        return slots;
    }

    function updateTimeSlots(bookedSlots) {
        timeSlotContainer.empty();
        if (!bookedSlots) {
            timeSlotContainer.html('<div class="loading-message text-muted">Select branch, table, and date first</div>');
            $('#reserv_time').val('');
            return;
        }

        const allSlots = generateTimeSlots();
        allSlots.forEach(slot => {
            const isBooked = bookedSlots.includes(slot.start);
            const div = $('<div class="time-slot"></div>').text(slot.display);
            if (isBooked) {
                div.addClass('booked');
            } else {
                div.addClass('available').on('click', function() {
                    $('.time-slot').removeClass('selected');
                    $(this).addClass('selected');
                    $('#reserv_time').val(slot.start);
                });
            }
            timeSlotContainer.append(div);
        });
    }

    function loadAvailableTimes() {
        const branch = $('#branchSelect').val();
        const table = $('#tableSelect').val();
        const date = $('#reserv_date').val();

        if (!branch || !table || !date) {
            updateTimeSlots(null);
            return;
        }

        timeSlotContainer.html('<div class="text-muted">Checking availability...</div>');

        $.ajax({
            url: '../get_booked_times.php',
            method: 'POST',
            dataType: 'json',
            data: { branch, table_number: table, reserv_date: date },
            success: function(data) {
                if (data.success) {
                    updateTimeSlots(data.bookedSlots || []);
                } else {
                    timeSlotContainer.html('<div class="text-danger">Error loading times</div>');
                }
            },
            error: function() {
                timeSlotContainer.html('<div class="text-danger">Error loading availability</div>');
            }
        });
    }

    $('#branchSelect').change(function() {
        const branchName = $(this).val();
        $.ajax({
            url: '../get_tables.php',
            method: 'POST',
            data: { branch: branchName },
            dataType: 'json',
            success: function(tables) {
                const $tableSelect = $('#tableSelect').empty();
                $tableSelect.append('<option value="" disabled selected>Select Table</option>');
                tables.forEach(table => {
                    if (table.is_active) {
                        $tableSelect.append(`<option value="${table.table_number}">Table ${table.table_number} (Max ${table.capacity} pax)</option>`);
                    }
                });
            },
            error: function(err) {
                console.error("Error loading tables:", err);
            }
        });
    });

    $('#tableSelect, #reserv_date').change(loadAvailableTimes);
</script>
</body>
</html>
