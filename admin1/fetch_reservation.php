<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../conn.php';

file_put_contents("debug.log", "GET Data: " . print_r($_GET, true) . "\n", FILE_APPEND);

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    file_put_contents("debug.log", "Received date: " . $date . "\n", FILE_APPEND);

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
        echo json_encode(["error" => "Invalid date format"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT last_name, message, reserv_date FROM reservations WHERE reserv_date = ?");
    if (!$stmt) {
        echo json_encode(["error" => "SQL Prepare Failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservations = [];

    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }

    echo json_encode($reservations);
    exit;
} else {
    file_put_contents("debug.log", "Error: Date parameter missing\n", FILE_APPEND);
    echo json_encode(["error" => "Date parameter missing"]);
    exit;
}
?>