<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_code = $_POST['reservation_code'];

    // Handle file upload
    if (isset($_FILES['proof-of-payment']) && $_FILES['proof-of-payment']['error'] == 0) {
        $target_dir = "proofs/";
        $target_file = $target_dir . basename($_FILES['proof-of-payment']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image or a PDF
        $check = getimagesize($_FILES['proof-of-payment']['tmp_name']);
        if ($check !== false || $imageFileType == 'pdf') {
            $uploadOk = 1;
        } else {
            echo "File is not an image or PDF.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if ($_FILES['proof-of-payment']['size'] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "pdf") {
            echo "Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES['proof-of-payment']['tmp_name'], $target_file)) {
                $stmt = $conn->prepare("UPDATE reservations SET proof_of_payment = ? WHERE reservation_code = ?");
                $stmt->bind_param("ss", $target_file, $reservation_code);
                $stmt->execute();
                $stmt->close();
                header("Location: receipt.php?code=" . $reservation_code . "&success=1");
                exit();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file uploaded or there was an error.";
    }
}

$conn->close();
?>