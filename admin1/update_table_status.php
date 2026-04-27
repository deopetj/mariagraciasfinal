<?php
session_start();
include '../conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $tableId = isset($_POST['table_id']) ? intval($_POST['table_id']) : null;
    $isActive = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;
    $disabledReason = isset($_POST['disabled_reason']) ? trim($_POST['disabled_reason']) : '';

    // Validate table ID
    if (!$tableId || $tableId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid table ID']);
        exit;
    }

    try {
        // Update table status using prepared statement
        $stmt = $conn->prepare("UPDATE restaurant_tables 
                               SET is_active = ?, disabled_reason = ?
                               WHERE table_id = ?");
        
        $stmt->bind_param("isi", $isActive, $disabledReason, $tableId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            error_log("Update failed: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
$conn->close();
?>