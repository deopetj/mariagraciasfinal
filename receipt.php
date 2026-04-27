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

$ratingSubmitted = isset($_GET['rating_submitted']) ? $_GET['rating_submitted'] : 0;
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Receipt</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="css/receipt.css">
</head>
<body>
    <div class="receipt">
        <h2>Official Receipt</h2>
        <p>Maria Gracias Samgyupsal</p>
        <p>Tel No: 09103857426</p>
        
        <div class="separator"></div>

        <p><strong>Reservation Code:</strong> <?= htmlspecialchars($reservation['reservation_code']) ?></p>
        <div class="separator"></div>
        <p><strong>Branch:</strong> <?= htmlspecialchars($reservation['branch']) ?></p>
        <p><strong>Table Number:</strong> <?= htmlspecialchars($reservation['table_number']) ?></p>
        <p><strong>Date & Time:</strong> <?= htmlspecialchars($reservation['reserv_date'] . " at " . $reservation['reserv_time']) ?></p>
        <div class="separator"></div>
        <div class="separator"></div>
        
        <div class="buttons">
            <button onclick="window.print()">Print Receipt</button>
            <button onclick="window.location.href='home.php'">Home</button>
        </div>
    </div>
<p>Note: Take a screenshot of your receipt.</p>
<p>Present to the admin when dining in.</p>

<?php if ($ratingSubmitted == 1): ?>
        <div class="thank-you-message">
            Thank you for your feedback!
        </div>
    <?php else: ?>
        <div class="ratings-form">
            <h3>Rate Your Experience</h3>
            <form action="submit_rating.php" method="POST" onsubmit="return validateRatingForm()">
                <input type="hidden" name="reservation_code" value="<?= htmlspecialchars($reservation['reservation_code']) ?>">
                
                <div class="rating-stars">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>">
                        <label for="star<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>

                <textarea class="rating-comment" name="comment" placeholder="Optional comments..." rows="3"></textarea>

                <div class="buttons">
                    <button type="submit">Submit Rating</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
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

        // Check if the URL has a success parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            alert('Reservation successful! Please wait for the admin to approve your reservation.');
        }

        
        function validateRatingForm() {
            const ratingSelected = document.querySelector('input[name="rating"]:checked');
            if (!ratingSelected) {
                alert('Please select a star rating before submitting.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>