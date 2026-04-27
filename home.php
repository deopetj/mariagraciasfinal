<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['id'])) {
    // Fetch user details from the database
    $user_id = $_SESSION['id'];
    $sql = "SELECT name, email, cnum FROM users WHERE id = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Store user details in the session
    $_SESSION['email'] = $user['email'];
    $_SESSION['cnum'] = $user['cnum'];
}

// Retrieve user details from the session
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$cnum = isset($_SESSION['cnum']) ? $_SESSION['cnum'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maria Gracias - Korean BBQ with Filipino Twist</title>
    <meta name="keywords" content="Maria Gracias, Samgyupsal, Korean BBQ, Iloilo, Unlimited Samgyupsal">
    <meta name="description" content="Affordable Korean BBQ restaurant in Iloilo with unlimited samgyupsal and Filipino twist.">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/logo.png" alt="Maria Gracias Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#branches">Branches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <?php if (isset($_SESSION['id'])): ?>
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link">
                                <i class="fa fa-user me-1" aria-hidden="true"></i> My Reservation
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content mx-auto">
                <h1 class="hero-title">"Korean by heart,<br>Filipino in style."</h1>
                <p class="hero-subtitle">Affordable Korean BBQ restaurant in Iloilo with unlimited samgyupsal and Filipino twist.</p>
                <a href="#reservation" class="btn btn-primary btn-lg pulse">Reserve Now</a>
            </div>
        </div>
        
        <!-- Decorative food elements -->
       
    </section>

    <!-- Menu Section -->
    <section id="menu" class="menu-section">
        <div class="container">
            <h2 class="section-title">Our Menu</h2>
            <div class="row">
                <div class="col-12">
                    <div class="menu-image">
                        <img src="images/blog-1.jpg" alt="Maria Gracias Menu" class="img-fluid w-100">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Branches Section -->
    <section id="branches" class="branches-section">
        <div class="container">
            <h2 class="section-title">Our Branches</h2>
            <div class="row">
                <!-- Branch 1 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/delcarmen.jpg" alt="Del Carmen Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Del Carmen</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> 14th Del Carmen St., Jaro, Iloilo City, Philippines beside Jo's Chiken Inato
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Branch 2 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/lapaz.jpg" alt="Lapaz Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Lapaz</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> Romnelcar Bldg., Corner Gustilo Burgos St., Lapaz, Iloilo City, Philippines
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Branch 3 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/mandu.jpg" alt="Mandurriao Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Mandurriao</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> 2/F Redsquare Building, Smallville Complex, Mandurriao, Iloilo City, Philippines
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Branch 4 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/lapuz.jpg" alt="Lapuz Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Lapuz</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> Ground Floor, Sea Eagle Boulevard, Brgy. Libertad Lapuz, Iloilo City, Philippines
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h3 class="mb-4">Maria Gracias Samgyupsal</h3>
                        <p class="mb-4">Maria Gracias Samgyupsal is more than just a restaurant—it's a celebration of flavors, community, and shared moments. Nestled in the vibrant city of Iloilo, Philippines, we bring the joy of Korean BBQ to your table with a unique Filipino twist.</p>
                        <p class="mb-4">Our story began with a simple idea: to create a space where everyone could enjoy unlimited samgyupsal without worrying about the cost. Over time, we've grown into a beloved destination, known for our affordable prices, warm hospitality, and delicious food.</p>
                        <p>At Maria Gracias Samgyupsal, we specialize in unlimited samgyupsal, offering premium cuts of pork belly that you can grill to perfection. Our menu is a fusion of Korean and Filipino flavors, featuring a variety of side dishes and sauces that reflect our local culture.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image">
                        <img src="images/about-img.jpg" alt="About Maria Gracias" class="img-fluid w-100">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation Section -->
    <section id="reservation" class="reservation-section">
        <div class="container">
            <h2 class="section-title">Book A Reservation</h2>
            <form action="process_reservation.php" method="POST" class="p-4 shadow rounded bg-light">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="contactnum" value="<?php echo htmlspecialchars($cnum); ?>">
                
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Branch</label>
                        <select class="form-select" name="branch" id="branchSelect" required>
                            <option value="" disabled selected>Select a branch</option>
                            <option value="Del Carmen">Del Carmen</option>
                            <option value="Lapaz">Lapaz</option>
                            <option value="Mandurriao">Mandurriao</option>
                            <option value="Lapuz">Lapuz</option>
                            <option value="CPU">CPU</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Select a table</label>
                        <select class="form-select" name="table_number" id="tableSelect" required>
                            <option value="" disabled selected>Select branch first</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" placeholder="Any special request? (Optional)" rows="2" name="message"></textarea>
                    </div>
                </div>

                <!-- Date and Time Selection -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Date</label>
                        <input type="date" class="form-control" name="reserv_date" id="reserv_date" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Time</label>
                        <input type="text" class="form-control" name="reserv_time" id="reserv_time" required readonly>
                        <div class="invalid-feedback">Please select a time slot</div>
                        <!-- Time slots will be inserted here by JavaScript -->
                    </div>
                </div>

                <button type="submit" class="btn-submit">Reserve</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h4>Maria Gracias Samgyupsal</h4>
                    <p>Korean by heart, Filipino in style.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> <a href="tel:09103857426">09103857426</a></p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:mgsamgyupsal@gmail.com">mgsamgyupsal@gmail.com</a></p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Maria Gracias Samgyupsal. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize date picker
        flatpickr("#reserv_date", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        // Handle reservation form functionality
        $(document).ready(function() {

            // Create container for time slots
            const timeSlotContainer = $(`
                <div class="time-slots-container mt-3">
                    <div class="loading-message">Select branch, table, and date first</div>
                </div>
            `);
            $('#reserv_time').parent().append(timeSlotContainer);
            
            // Configure time input
            $('#reserv_time').css('background-color', '#f8f9fa');

            // Prevent past dates
            const today = new Date().toISOString().split('T')[0];
            $('#reserv_date').attr('min', today);

            // Format time display (supports 30-minute increments)
            function formatTime(hours) {
                const isHalfHour = hours % 1 !== 0;
                const hour = Math.floor(hours);
                const minutes = isHalfHour ? '30' : '00';
                const period = hour >= 12 ? 'pm' : 'am';
                const displayHour = hour % 12 || 12;
                return `${displayHour}:${minutes}${period}`;
            }

            // Generate 1.5-hour time slots
            function generateTimeSlots() {
                const slots = [];
                const startHour = 9; // 9:00 AM
                const endHour = 20;  // 8:00 PM
                
                for(let hour = startHour; hour < endHour; hour += 1) {
                    const start = formatTime(hour);
                    const end = formatTime(hour + 1);
                    slots.push({
                        display: `${start} - ${end}`,
                        start: `${String(Math.floor(hour)).padStart(2,'0')}:${hour % 1 === 0 ? '00' : '00'}`
                    });
                }
                return slots;
            }

            // Update time slot display
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
                            $('#reserv_time')
                                .val(`${slot.start} (${slot.display})`)
                                .removeClass('is-invalid')
                                .addClass('is-valid');
                        });
                    }
                    timeSlotContainer.append(timeElement);
                });
            }

            // Load availability from server
            function loadAvailableTimes() {
                const branch = $('#branchSelect').val();
                const table = $('#tableSelect').val();
                const date = $('#reserv_date').val();

                if (!branch || !table || !date) {
                    updateTimeSlots(null);
                    $('#reserv_time').val('').removeClass('is-valid is-invalid');
                    return;
                }

                timeSlotContainer.html('<div class="loading-message">Checking availability...</div>');
                $('#reserv_time').val('').removeClass('is-valid is-invalid');

                $.ajax({
                    url: 'get_booked_times.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        branch: branch,
                        table_number: table,
                        reserv_date: date
                    },
                    success: function(data) {
                        if (data.success) {
                            updateTimeSlots(data.bookedSlots);
                            // Auto-refresh every 2 minutes
                            setTimeout(loadAvailableTimes, 120000);
                        } else {
                            timeSlotContainer.html(`<div class="error-message">${data.error || 'Error loading times'}</div>`);
                        }
                    },
                    error: function(xhr) {
                        timeSlotContainer.html(`<div class="error-message">Error loading availability</div>`);
                    }
                });
            }

            // Form validation
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

            // Initialize branch-table relationship
            $('#branchSelect').change(function() {
                const branchName = $(this).val();
                
                // AJAX call to get dynamic data
                $.ajax({
                    url: 'get_tables.php',
                    method: 'POST',
                    data: { branch: branchName },
                    dataType: 'json',
                    success: function(tables) {
                        const $tableSelect = $('#tableSelect').empty();
                        $tableSelect.append('<option value="" disabled selected>Select table</option>');
                        
                        // Processing API response
                        tables.forEach(table => {
                            const optionText = table.is_active
                                ? `Table ${table.table_number} (maximum of ${table.capacity} pax)`
                                : `Table ${table.table_number} (${table.disabled_reason || 'Not available'})`;

                            const $option = $('<option>', {
                                value: table.table_number,
                                text: optionText,
                                disabled: !table.is_active
                            });

                            $('#tableSelect').append($option);
                        });
                        loadAvailableTimes();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading tables:", error);
                        console.log("Response:", xhr.responseText);
                    }
                });
            });

            // Load available times when date changes
            $('#reserv_date').change(loadAvailableTimes);

            // Load available times when table changes
            $('#tableSelect').change(loadAvailableTimes);
        });
    </script>
</body>
</html>