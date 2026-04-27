<?php
    session_start();
    include '../conn.php';

    if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
    // Check admin authentication
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

     $tables_result = $conn->query("SELECT is_active FROM restaurant_tables");
    $total_tables = 0;
    $active_tables = 0;
    $inactive_tables = 0;
    if ($tables_result) {
        while ($row = $tables_result->fetch_assoc()) {
            $total_tables++;
            if ($row['is_active']) {
                $active_tables++;
            } else {
                $inactive_tables++;
            }
        }
    }
    // Fetch all branches and their tables
    $messages = [];
    $messages[] = "Welcome to the Admin Dashboard!";
    $username = ("SELECT email FROM users WHERE id = ?");

    $branches = $conn->query("SELECT * FROM branches");
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Tables - Maria Gracias</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="icon" href="../images/logo.png" type="image/x-icon">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
        <link rel="stylesheet" href="../css/manage_tables.css">
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
                            <a class="show-cat-btn active" href="manage_tables.php">
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
                        <h1 class="dashboard-title">Manage Tables</h1>
                        <p class="dashboard-subtitle">Configure and manage restaurant tables across all branches</p>
                    </div>
                    <div>
                        <a href="admin_add_reservation.php" class="btn btn-primary" style="background-color:#5979D6;">
  <i class="bi bi-plus-circle"></i> Add occupied table
</a>
                    </div>
                </div>
            </div>
            
            
            <!-- Stats Overview -->
            <div class="stats-grid fade-in delay-1">
                    <div class="stat-card">
        <div class="stat-icon bg-primary-light">
            <i class="bi bi-shop"></i>
        </div>
        <div class="stat-title">Active Branches</div>
        <div class="stat-value"><?= $branches->num_rows ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success-light">
            <i class="bi bi-grid-3x3"></i>
        </div>
        <div class="stat-title">Total Tables</div>
        <div class="stat-value"><?= $total_tables ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info-light">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-title">Active Tables</div>
        <div class="stat-value"><?= $active_tables ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning-light">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="stat-title">Inactive Tables</div>
        <div class="stat-value"><?= $inactive_tables ?></div>
    </div>
</div>
            
            <!-- Branch Cards -->
            <div class="fade-in delay-2">
                <?php while ($branch = $branches->fetch_assoc()): ?>
                <div class="branch-card">
                    <div class="branch-header">
                        <h3 class="branch-title"><?= htmlspecialchars($branch['name']) ?> Branch</h3>
                        <span class="branch-badge">
                            <?php
                            $table_count_result = $conn->query("SELECT COUNT(*) as cnt FROM restaurant_tables WHERE branch_id = {$branch['id']}");
                            $table_count = $table_count_result ? $table_count_result->fetch_assoc()['cnt'] : 0;
                            echo $table_count . ' table' . ($table_count == 1 ? '' : 's');
                            ?>  
                        </span>
                    </div>
                    <div class="tables-grid">
                        <?php
                        $tables = $conn->query("SELECT * FROM restaurant_tables 
                                                WHERE branch_id = {$branch['id']}
                                                ORDER BY table_number");
                        while ($table = $tables->fetch_assoc()):
                            $isActive = $table['is_active'] ?? true;
                        ?>
                        <div class="table-card <?= !$isActive ? 'inactive' : '' ?>">
                            <div class="table-card-header">
                                <div class="table-number">Table #<?= $table['table_number'] ?></div>
                                <div class="table-status <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                </div>
                            </div>
                            <div class="table-info">
                                <div><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($branch['name']) ?></div>
                                <div><i class="bi bi-people me-2"></i> Capacity: <?= $table['capacity'] ?? 4 ?> seats</div>
                                <div><i class="bi bi-info-circle me-2"></i> Section: <?= $table['section'] ?? 'Main Hall' ?></div>
                            </div>
                            <div class="table-card-footer">
                                <div>
                                    <?php if (!$isActive && !empty($table['disabled_reason'])): ?>
                                    <small class="text-danger">Reason: <?= htmlspecialchars($table['disabled_reason']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <label class="table-toggle">
                                    <input type="checkbox" class="table-toggle" 
                                        data-table-id="<?= $table['table_id'] ?>"
                                        <?= $isActive ? 'checked' : '' ?>>
                                    <span class="table-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        $(document).ready(function() {
            // Toggle table status
            $('.table-toggle input').change(function() {
                const tableId = $(this).data('table-id');
                const isActive = this.checked;
                const $toggle = $(this);
                
                // Store original state for reverting
                const originalState = !isActive;
                
                let disabledReason = '';
                
                if (!isActive) {
                    Swal.fire({
                        title: 'Disable Table',
                        input: 'text',
                        inputLabel: 'Reason for disabling this table:',
                        inputPlaceholder: 'Enter the reason...',
                        showCancelButton: true,
                        confirmButtonText: 'Disable',
                        cancelButtonText: 'Cancel',
                        inputValidator: (value) => {
                            if (!value.trim()) {
                                return 'You need to provide a reason!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            disabledReason = result.value;
                            updateTableStatus(tableId, isActive, disabledReason, $toggle);
                        } else {
                            // Revert toggle if canceled
                            $toggle.prop('checked', originalState);
                        }
                    });
                } else {
                    // Enable table
                    updateTableStatus(tableId, isActive, disabledReason, $toggle);
                }
            });
            
            function updateTableStatus(tableId, isActive, reason, $toggle) {
                $.ajax({
                    url: 'update_table_status.php',
                    method: 'POST',
                    data: {
                        table_id: tableId,
                        is_active: isActive ? 1 : 0,
                        disabled_reason: reason
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const statusEl = $(`.table-card:has([data-table-id="${tableId}"]) .table-status`);
                            const cardEl = $(`.table-card:has([data-table-id="${tableId}"])`);
                            
                            if (isActive) {
                                statusEl.removeClass('status-inactive').addClass('status-active').text('Active');
                                cardEl.removeClass('inactive');
                                Swal.fire('Success', 'Table has been activated', 'success');
                            } else {
                                statusEl.removeClass('status-active').addClass('status-inactive').text('Inactive');
                                cardEl.addClass('inactive');
                                Swal.fire('Success', 'Table has been disabled', 'success');
                            }
                        } else {
                            Swal.fire('Error', response.message || 'Update failed', 'error');
                            // Revert toggle on failure
                            $toggle.prop('checked', !isActive);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Server error: ' + xhr.status, 'error');
                        $toggle.prop('checked', !isActive);
                    }
                });
            }
            
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