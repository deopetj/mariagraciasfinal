<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

// Get reservation code from URL
if (!isset($_GET['code'])) {
    echo "Invalid request!";
    exit();
} 

$reservation_code = $_GET['code'];

// Fetch reservation details
$stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_code = ?");
$stmt->bind_param("s", $reservation_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Reservation not found!";
    exit();
}

$reservation = $result->fetch_assoc();
$stmt->close();

// Convert absolute path to relative URL
$qr_code_url = '';
if (!empty($reservation['qr_code'])) {
    $qr_code_url = str_replace('/opt/lampp/htdocs/new/', '', $reservation['qr_code']);
}

// Close the database connection
// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <style>
        body {
            opacity: 0.9;
            width: 100%;
            float: left;
            height: auto;
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }
        .receipt {
            width: 350px;
            background: white;
            padding: 20px;
            display: inline-block;
            text-align: left;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        }
        .receipt h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        .receipt p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .receipt .separator {
            border-top: 2px dashed #333;
            margin: 10px 0;
        }
        .qr-container {
            text-align: center;
            margin: 15px 0;
        }
        .qr-container img {
            width: 150px;
            height: 150px;
        }
        .buttons {
            margin-top: 15px;
            text-align: center;
        }
        .buttons button {
            font-size: 14px;
            padding: 8px 12px;
            margin: 5px;
            border: none;
            cursor: pointer;
            background: #333;
            color: white;
            font-weight: bold;
            border-radius: 5px;
        }
        .buttons button:hover {
            background: #555;
        }
        .proof-of-payment {
            margin-top: 15px;
            text-align: center;
        }
        .proof-of-payment input[type="file"] {
            font-size: 14px;
            padding: 8px;
            border: 1px solid #333;
            border-radius: 5px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<center>
    <div class="receipt">
        <h2>Reservation Details</h2>
        <p>Maria Gracias Samgyupsal</p>
        <p>Tel No: 09103857426</p>
        <div class="separator"></div>
        <p><strong>Email:</strong> <?= htmlspecialchars($reservation['email']) ?></p>
        <p><strong>Branch:</strong> <?= htmlspecialchars($reservation['branch']) ?></p>
        <p><strong>Table Number:</strong> <?= htmlspecialchars($reservation['table_number']) ?></p>
        <p><strong>Date & Time:</strong> <?= htmlspecialchars($reservation['reserv_date'] . " at " . $reservation['reserv_time']) ?></p>
        <p><strong>Amount:</strong> ₱<?= htmlspecialchars($reservation['amount']) ?></p>
        <div class="separator"></div>
        
        <div class="qr-container">
            <p><strong>Scan QR Code to Pay Downpayment</strong></p>
            <img src="images/paymentqr.jpg" alt="GCash QR Code">
        </div>
        <center>
        <div>
        <p>DE***T J.</p>
            <p>Mobile No.: 096*****925</p>

        </div>
        </center>
        <!-- Add a form for proof of payment -->
        <form id="proofForm" action="upload_proof.php" method="POST" enctype="multipart/form-data">
            <div class="proof-of-payment">
                <label for="proof-of-payment"><strong>Proof of Payment:</strong></label>
                <p>(GCash Receipt Screenshot)</p>
                <input type="file" id="proof-of-payment" name="proof-of-payment" accept="image/*,.pdf" required>
            </div>
            <input type="hidden" name="reservation_code" value="<?= htmlspecialchars($reservation['reservation_code']) ?>">
        </form>

        <div class="separator"></div>
        
        <?php 
        $sql = "SELECT * FROM reservations where user_id = $reservation[user_id]";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                ?>
                <form action="delete_reservation.php" method="get">
                <div class="buttons">
             <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">
             <button type="submit" name="delete" value="delete" class="btn btn-danger">Go Back</button>
            <button type="button" onclick="submitProofForm()">Reserve</button>
        </div>
                </form>
                <?php
            }
        }
        ?>
        
    </div>
    </center>
    <br>

    <script>
        // Function to submit the proof of payment form
        function submitProofForm() {
            const form = document.getElementById('proofForm');
            const fileInput = document.getElementById('proof-of-payment');

            // Check if a file is selected
            if (fileInput.files.length > 0) {
                form.submit(); // Submit the form
            } else {
                alert('Please upload proof of payment before reserving.');
            }
        }
    </script>
</body>
</html>