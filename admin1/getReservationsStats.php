<?php
include '../conn.php';

// Determine filter type: daily, weekly, or monthly
$filter = $_GET['filter'] ?? 'daily';

$reservations = [];

// Choose SQL format and label formatting based on filter
switch ($filter) {
    case 'weekly':
        $query = "SELECT YEAR(created_at) AS year, WEEK(created_at) AS week, COUNT(*) AS count 
                  FROM reservations 
                  GROUP BY year, week";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $label = "Week {$row['week']} ({$row['year']})";
            $reservations[$label] = (int)$row['count'];
        }
        break;

    case 'monthly':
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
                  FROM reservations 
                  GROUP BY month";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $reservations[$row['month']] = (int)$row['count'];
        }
        break;

    case 'daily':
    default:
        $query = "SELECT DATE(created_at) AS date, COUNT(*) AS count 
                  FROM reservations 
                  GROUP BY date";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $reservations[$row['date']] = (int)$row['count'];
        }
        break;
}

// Initialize status counts
$possibleStatuses = ["Pending", "Approved", "Declined", "Cancelled"];
$statusCounts = array_fill_keys($possibleStatuses, 0);

$query = "SELECT status, COUNT(*) AS count FROM reservations GROUP BY status";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $status = $row['status'];
    if (in_array($status, $possibleStatuses)) {
        $statusCounts[$status] = (int)$row['count'];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    "reservations" => $reservations,
    "statusCounts" => $statusCounts
]);
?>
