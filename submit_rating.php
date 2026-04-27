<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_code = $_POST['reservation_code'];
    $rating = (int)$_POST['rating'];
    // Remove real_escape_string - prepared statements handle escaping
    $comment = $_POST['comment'] ?? '';

    // Fetch reservation data
    $stmt = $conn->prepare("SELECT email, contact_number, branch, reserv_date, reserv_time 
        FROM reservations WHERE reservation_code = ?");
    $stmt->bind_param("s", $reservation_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Reservation not found!");
    }
    
    $reservation = $result->fetch_assoc();
    $stmt->close();

    // Handle NULL values from reservation
    $email = $reservation['email'] ?? '';
    $contact = $reservation['contact_number'] ?? '';
    $branch = $reservation['branch'] ?? '';
    $res_date = $reservation['reserv_date'] ?? '';
    $res_time = $reservation['reserv_time'] ?? '';

    // Insert into ratings
    $stmt = $conn->prepare("INSERT INTO ratings (
        reservation_code, rating, comment, 
        email, contact_number, branch, reservation_date, reservation_time
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Use proper data types
    $stmt->bind_param("sissssss",
        $reservation_code,
        $rating,
        $comment,
        $email,          // Now guaranteed string
        $contact,        // Now guaranteed string
        $branch,         // Now guaranteed string
        $res_date,       // Now guaranteed string
        $res_time        // Now guaranteed string
    );
    
    if ($stmt->execute()) {
        header("Location: receipt.php?code=$reservation_code&rating_submitted=1");
        exit; // Always exit after header redirect
    } else {
        error_log("Rating submit error: " . $conn->error);
        die("Error submitting rating. Please try again.");
    }

    $stmt->close();
    $conn->close(); 
}