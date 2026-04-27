<?php
require 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$branch = $conn->real_escape_string($_POST['branch'] ?? '');
$table_number = $conn->real_escape_string($_POST['table_number'] ?? '');
$reserv_date = $conn->real_escape_string($_POST['reserv_date'] ?? '');

if (empty($branch) || empty($table_number) || empty($reserv_date)) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

try {
    // Get disabled time slots
    $disabled_slots_query = "SELECT TIME_FORMAT(time_slot, '%H:%i') as time_slot 
                            FROM time_slots 
                            WHERE is_active = 0 
                            ORDER BY time_slot";
    $disabled_result = $conn->query($disabled_slots_query);
    $disabled_slots = [];
    while ($row = $disabled_result->fetch_assoc()) {
        $disabled_slots[] = $row['time_slot'];
    }

    // Get already booked times for this table and date
    $booked_query = "SELECT TIME_FORMAT(reserv_time, '%H:%i') as reserv_time 
                    FROM reservations 
                    WHERE branch = ? 
                    AND table_number = ? 
                    AND reserv_date = ? 
                    AND status IN ('Approved', 'Pending')";
    $stmt = $conn->prepare($booked_query);
    $stmt->bind_param("sis", $branch, $table_number, $reserv_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booked_slots = [];
    while ($row = $result->fetch_assoc()) {
        $booked_slots[] = $row['reserv_time'];
    }
    $stmt->close();

    // Get all active time slots with capacity info
    $active_slots_query = "SELECT 
        TIME_FORMAT(ts.time_slot, '%H:%i') as time_slot,
        ts.max_capacity,
        ts.current_bookings,
        (SELECT COUNT(*) FROM reservations r 
         WHERE DATE(r.reserv_time) = ? 
         AND TIME_FORMAT(r.reserv_time, '%H:%i') = TIME_FORMAT(ts.time_slot, '%H:%i')
         AND r.status IN ('Approved', 'Pending')) as total_bookings
    FROM time_slots ts 
    WHERE ts.is_active = 1 
    ORDER BY ts.time_slot";
    
    $stmt = $conn->prepare($active_slots_query);
    $stmt->bind_param("s", $reserv_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $available_slots = [];
    while ($row = $result->fetch_assoc()) {
        $available_slots[] = [
            'time_slot' => $row['time_slot'],
            'max_capacity' => $row['max_capacity'],
            'current_bookings' => $row['total_bookings'],
            'available' => $row['total_bookings'] < $row['max_capacity']
        ];
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'disabledSlots' => $disabled_slots,
        'bookedSlots' => $booked_slots,
        'availableSlots' => $available_slots
    ]);

} catch (Exception $e) {
    error_log("Error in get_booked_times.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
}

$conn->close();
?>