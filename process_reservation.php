<?php
// Start session
session_start();
// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to user_login.php with an error message
    $_SESSION['login_error'] = "YOU NEED TO LOGIN TO BOOK A RESERVATION";
    header("Location: user_login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conn.php';


// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "not_logged_in", "message" => "PLEASE LOGIN TO BOOK A RESERVATION"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ensure `amount` is set before validation
    if (!isset($_POST['amount']) || empty(trim($_POST['amount']))) {
        $_POST['amount'] = '100'; // Default to 100
    }

    // Required fields
    $required_fields = ['branch', 'table_number', 'reserv_date', 'reserv_time', 'amount'];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            die("Error: Missing required field '$field'.");
        }
    }

    // Sanitize and assign values
    $email = isset($_POST['email']) ? trim($_POST['email']) : NULL;
    $contact_number = isset($_POST['contactnum']) ? trim($_POST['contactnum']) : NULL;
    $branch = trim($_POST['branch']);
    $table_number = trim($_POST['table_number']);
    $reserv_date = trim($_POST['reserv_date']);
    $reserv_time = trim($_POST['reserv_time']);
    $amount = trim($_POST['amount']);

    // Handle optional fields (email, contact number, message)
    $email = isset($_POST['email']) ? trim($_POST['email']) : NULL;
    $contact_number = isset($_POST['contactnum']) ? trim($_POST['contactnum']) : NULL;
    $message = isset($_POST['message']) ? trim($_POST['message']) : NULL;

    // Generate a unique reservation code
    $reservation_code = "RES" . uniqid();

    
  
   

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO reservations (reservation_code, email, contact_number, branch, table_number, reserv_date, reserv_time, amount, qr_code, message) 
    VALUES (?,?,?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Error: " . $conn->error);
    }

    $stmt->bind_param("ssssssssss", $reservation_code, $email, $contact_number, $branch, $table_number, $reserv_date, $reserv_time, $amount, $qr_code_path, $message);

    if ($stmt->execute()) {
        // Redirect to receipt.php with the reservation code as a URL parameter
        header("Location: reservation_details.php?code=$reservation_code");
        exit();
    } else {
        echo json_encode(["error" => $stmt->error]);
    }
// Handle approval action
if (isset($_POST['approve'])) {
    $id = intval($_POST['id']);

    $query = "UPDATE reservations SET status = 'Approved' WHERE user_id = $id";
    if ($conn->query($query)) {
        $emailQuery = "SELECT email FROM reservations WHERE user_id = $id";
        $result = $conn->query($emailQuery);
        $email = $result->fetch_assoc()['email'];

        sendEmail($email, 'Reservation Approved', 'Your reservation has been approved, thanks for dining with us! -Maria Gracias Samgyupsal');

        header('Location: manage_reservation.php');
        exit;
    }
}

// Handle decline action
if (isset($_POST['decline'])) {
    $id = intval($_POST['id']);

    $query = "UPDATE reservations SET status = 'Declined' WHERE user_id = $id";
    if ($conn->query($query)) {
        $emailQuery = "SELECT email FROM reservations WHERE user_id = $id";
        $result = $conn->query($emailQuery);
        $email = $result->fetch_assoc()['email'];

        sendEmail($email, 'Reservation Declined', 'Your reservation has been declined.');

        header('Location: manage_reservation.php');
        exit;
    }
}
    

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
