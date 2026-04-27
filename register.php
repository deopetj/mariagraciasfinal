<?php
header('Content-Type: application/json');
require_once 'conn.php'; 

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $cnum = $_POST['cnum'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
        $role = 'user';
        $status = 'active'; // Set status to active immediately

        // Check if the contact number already exists
        $checkCnumQuery = "SELECT cnum FROM users WHERE cnum = ?";
        $checkCnumStmt = $conn->prepare($checkCnumQuery);
        $checkCnumStmt->bind_param("s", $cnum);
        $checkCnumStmt->execute();
        $checkCnumStmt->store_result();
        
        if ($checkCnumStmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Contact number already registered. Please use a different contact number.']);
            exit();
        }
        if (strlen($cnum) < 11) {
            echo json_encode(['success' => false, 'message' => 'Contact number must be 11 digits.']);
            exit();
        }
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit();
        }
        $checkCnumStmt->close();

        // Check if the email already exists
        $checkEmailQuery = "SELECT email FROM users WHERE email = ?";
        $checkEmailStmt = $conn->prepare($checkEmailQuery);
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already registered. Please use a different email.']);
            exit();
        }
        $checkEmailStmt->close();

        // Insert into the database with active status
        $stmt = $conn->prepare("INSERT INTO users (cnum, password, role, email, status) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $cnum, $password, $role, $email, $status);

        if ($stmt->execute()) {
            // Automatically log the user in after registration
            session_start();
            $_SESSION['id'] = $stmt->insert_id;
            $_SESSION['role'] = $role;
            $_SESSION['cnum'] = $cnum;
            $_SESSION['email'] = $email;
            
            echo json_encode([
                'success' => true, 
                'redirect' => 'home.php', // Changed from index.php to home.php
                'message' => 'Registration successful!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>