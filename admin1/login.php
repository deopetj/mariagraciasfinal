<?php
session_start();
include 'conn.php';
if ($role === 'admin') {
    $redirect_page = "admin1/index.php";
} else {
    $redirect_page = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : "home.php";
}

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'");

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Validate inputs
if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit();
}

// Check if email exists
$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;  // Store email in session
        $_SESSION['login_time'] = time();

    
        // Clear last page session variable
        unset($_SESSION['last_page']);

        echo json_encode(["success" => true, "redirect" => $redirect_page]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Account not found. Please register."]);
}

$stmt->close();
$conn->close();
exit();