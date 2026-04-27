<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Get reservation code from URL
if (!isset($_GET['code'])) {
    $_SESSION['error'] = "No reservation code provided.";
    header("Location: profile.php");
    exit();
}

$reservation_code = $_GET['code'];

// Fetch reservation details
$stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_code = ? AND email = (SELECT email FROM users WHERE id = ?)");
$stmt->bind_param("si", $reservation_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Reservation not found or unauthorized access.";
    header("Location: profile.php");
    exit();
}

$reservation = $result->fetch_assoc();
$stmt->close();

// Fetch branches for dropdown
$branches = [];
$branch_result = $conn->query("SELECT DISTINCT name FROM branches");
if ($branch_result && $branch_result->num_rows > 0) {
    while ($row = $branch_result->fetch_assoc()) {
        $branches[] = $row['name'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch = trim($_POST['branch']);
    $table_number = trim($_POST['table_number']);
    $reserv_date = trim($_POST['reserv_date']);
    $reserv_time = trim($_POST['reserv_time']);

    if (empty($branch) || empty($table_number) || empty($reserv_date) || empty($reserv_time)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        $update_stmt = $conn->prepare("UPDATE reservations 
                                       SET branch = ?, table_number = ?, reserv_date = ?, reserv_time = ?
                                       WHERE reservation_code = ? AND email = (SELECT email FROM users WHERE id = ?)");
        $update_stmt->bind_param("sssssi", $branch, $table_number, $reserv_date, $reserv_time, $reservation_code, $user_id);
        if ($update_stmt->execute()) {
           echo "<script>
            alert('Reservation updated successfully.');
            window.location.href='profile.php';
              </script>";
        } else {
           echo "<script>
            alert('Failed to update reservation.');
            window.location.href='edit_reservation.php?code=" . htmlspecialchars($reservation_code) . "';
              </script>";
        }
        $update_stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Reservation - Maria Gracias</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="icon" href="images/logo.png" type="image/x-icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <style>
 /* Time slot container */
.time-slots-container,
#editTimeSlots {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 8px;
  align-items: flex-start;
  max-height: 200px;
  overflow-y: auto;
  padding: 6px;
  border-radius: 8px;
  background: #ffffff;
  border: 1px solid #e9ecef;
  box-shadow: 0 1px 4px rgba(0,0,0,0.03);
}

/* Time slot box */
.time-slot {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 10px;
  min-width: 92px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #d0d7de;
  font-size: 0.875rem;
  cursor: pointer;
  user-select: none;
  transition: all .15s ease-in-out;
}

/* Available */
.time-slot.available {
  background-color: #bafcc2; /* Light green */
  color: #0a3622;
  border-color: #89f59b;
}

/* Hover */
.time-slot.available:hover {
  background-color: #7ef08f;
  border-color: #47e467;
  transform: translateY(-1px);
}

/* Selected */
.time-slot.selected {
  background-color: #0d6efd;
  color: white;
  border-color: #0a58ca;
  box-shadow: 0 0 8px rgba(13,110,253,0.3);
}

/* Booked */
.time-slot.booked {
  background-color: #f8d7da;
  color: #842029;
  border-color: #f5c2c7;
  cursor: not-allowed;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 576px) {
  .time-slot {
    min-width: 48%;
    font-size: 0.85rem;
    padding: 8px 6px;
  }
}
    </style>
</head>
<body>

<div class="container mt-5">
  <div class="card mx-auto" style="max-width: 500px;">
      <h4 class="text-center mb-3">Edit Reservation</h4>
      <form method="POST" id="editForm">
          <div class="mb-3">
              <label class="form-label">Reservation Code</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($reservation['reservation_code']); ?>" disabled>
          </div>
          <div class="mb-3">
              <label class="form-label">Branch</label>
              <select id="branchSelect" name="branch" class="form-select" required>
                  <option value="" disabled>Select branch</option>
                  <?php foreach ($branches as $branch): ?>
                      <option value="<?= htmlspecialchars($branch); ?>" 
                        <?= $branch === $reservation['branch'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($branch); ?>
                      </option>
                  <?php endforeach; ?>
              </select>
          </div>
          <div class="mb-3">
              <label class="form-label">Table</label>
              <select id="tableSelect" name="table_number" class="form-select" required>
                  <option value="<?= htmlspecialchars($reservation['table_number']); ?>" selected>
                      Current: Table <?= htmlspecialchars($reservation['table_number']); ?>
                  </option>
              </select>
          </div>
          <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" id="reserv_date" name="reserv_date" class="form-control" 
                     value="<?= htmlspecialchars($reservation['reserv_date']); ?>" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Time</label>
              <input type="text" id="reserv_time" name="reserv_time" class="form-control" 
                     value="<?= htmlspecialchars($reservation['reserv_time']); ?>" readonly required>
          </div>
          <div class="d-flex justify-content-between mt-4">
              <a href="profile.php" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
      </form>
  </div>
</div>

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
flatpickr("#reserv_date", {
    dateFormat: "Y-m-d",
    minDate: "today"
});

// Include your full time-slot and branch-table logic
$(document).ready(function() {
    const timeSlotContainer = $(`
        <div class="time-slots-container mt-3">
            <div class="loading-message">Select branch, table, and date first</div>
        </div>
    `);
    $('#reserv_time').parent().append(timeSlotContainer);

    const today = new Date().toISOString().split('T')[0];
    $('#reserv_date').attr('min', today);

    function formatTime(hours) {
        const isHalfHour = hours % 1 !== 0;
        const hour = Math.floor(hours);
        const minutes = isHalfHour ? '30' : '00';
        const period = hour >= 12 ? 'pm' : 'am';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes}${period}`;
    }

    function generateTimeSlots() {
        const slots = [];
        const startHour = 9;
        const endHour = 20;
        for(let hour = startHour; hour < endHour; hour++) {
            const start = formatTime(hour);
            const end = formatTime(hour + 1);
            slots.push({
                display: `${start} - ${end}`,
                start: `${String(hour).padStart(2,'0')}:00`
            });
        }
        return slots;
    }

    function updateTimeSlots(bookedSlots) {
        timeSlotContainer.empty();
        if (!bookedSlots) {
            timeSlotContainer.html('<div class="loading-message">Select branch, table, and date first</div>');
            $('#reserv_time').val('').removeClass('is-valid is-invalid');
            return;
        }

        const allSlots = generateTimeSlots();
        allSlots.forEach(slot => {
            const isBooked = bookedSlots.includes(slot.start);
            const timeElement = $(`
                <div class="time-slot" data-time="${slot.start}">
                    ${slot.display}
                </div>
            `);
            if (isBooked) {
                timeElement.addClass('booked');
            } else {
                timeElement.addClass('available').click(function() {
                    $('.time-slot').removeClass('selected');
                    $(this).addClass('selected');
                    $('#reserv_time').val(slot.start).removeClass('is-invalid').addClass('is-valid');
                });
            }
            timeSlotContainer.append(timeElement);
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

        timeSlotContainer.html('<div class="loading-message">Checking availability...</div>');
        $.ajax({
            url: 'get_booked_times.php',
            method: 'POST',
            dataType: 'json',
            data: { branch, table_number: table, reserv_date: date },
            success: function(data) {
                if (data.success) {
                    updateTimeSlots(data.bookedSlots);
                } else {
                    timeSlotContainer.html(`<div class="error-message">${data.error || 'Error loading times'}</div>`);
                }
            },
            error: function() {
                timeSlotContainer.html(`<div class="error-message">Error loading availability</div>`);
            }
        });
    }

    $('#branchSelect').change(function() {
        const branchName = $(this).val();
        $.ajax({
            url: 'get_tables.php',
            method: 'POST',
            data: { branch: branchName },
            dataType: 'json',
            success: function(tables) {
                const $tableSelect = $('#tableSelect').empty();
                $tableSelect.append('<option value="" disabled selected>Select table</option>');
                tables.forEach(table => {
                    const optionText = table.is_active
                        ? `Table ${table.table_number} (max ${table.capacity} pax)`
                        : `Table ${table.table_number} (Unavailable)`;
                    const $option = $('<option>', {
                        value: table.table_number,
                        text: optionText,
                        disabled: !table.is_active
                    });
                    $tableSelect.append($option);
                });
                loadAvailableTimes();
            }
        });
    });

    $('#reserv_date, #tableSelect').change(loadAvailableTimes);

    $('form').submit(function(e) {
        if (!$('#reserv_time').val()) {
            e.preventDefault();
            $('#reserv_time').addClass('is-invalid');
            Swal.fire({
                icon: 'error',
                title: 'Missing Time Slot',
                text: 'Please select an available time slot for your reservation',
                confirmButtonColor: '#d33'
            });
        }
    });
});
</script>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= $_SESSION['error']; ?>'
});
</script>
<?php unset($_SESSION['error']); endif; ?>
</body>
</html>
