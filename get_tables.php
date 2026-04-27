<?php
ob_start(); // Start output buffering
header('Content-Type: application/json');
include 'conn.php';

$branchName = $_POST['branch'] ?? '';

$stmt = $conn->prepare("
    SELECT table_number, capacity, is_active, disabled_reason
    FROM restaurant_tables 
    WHERE branch_id = (
        SELECT id FROM branches WHERE name = ?
        LIMIT 1
    )
    ORDER BY table_number
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("s", $branchName);
$stmt->execute();
$result = $stmt->get_result();

$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

header('Content-Type: application/json');
echo json_encode($tables);
?>