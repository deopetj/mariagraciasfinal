<?php
header('Content-Type: application/json');
include 'conn.php';

$branch = $_GET['branch'] ?? '';
$table = $_GET['table'] ?? '';
$date = $_GET['date'] ?? '';

// Validate inputs
if (empty($branch) || empty($table) || empty($date)) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing parameters']));
}

// Query database for existing reservations
$stmt = $conn->prepare("
    SELECT reserv_time 
    FROM reservations 
    WHERE branch = ? 
    AND table_number = ? 
    AND reserv_date = ?
");
$stmt->bind_param("sis", $branch, $table, $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedTimes = [];
while ($row = $result->fetch_assoc()) {
    $bookedTimes[] = $row['reserv_time'];
}

// Generate all possible time slots (11AM-9PM, 30-min intervals)
$allTimes = [];
for ($hour = 11; $hour <= 21; $hour++) {
    $allTimes[] = sprintf("%02d:00", $hour);
    if ($hour < 21) $allTimes[] = sprintf("%02d:30", $hour);
}

// Filter out booked times
$availableTimes = array_diff($allTimes, $bookedTimes);

echo json_encode([
    'availableTimes' => array_values($availableTimes) // reindex array
]);
?>