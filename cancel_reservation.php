<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = intval($_POST['reservation_id']);

    // Fetch user's email to verify ownership of the reservation
    $user_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_email);
    $stmt->fetch();
    $stmt->close();

    if ($user_email) {
        // Ensure the reservation belongs to the logged-in user before cancelling
        $stmt = $conn->prepare("UPDATE reservations SET status = 'Cancelled' WHERE user_id = ? AND email = ?");
        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("is", $reservation_id, $user_email);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<script>
                    alert('Reservation cancelled successfully.');
                    window.location.href='profile.php';
                </script>";
            } else {
                echo "<script>
                    alert('Failed to cancel reservation or reservation not found.');
                    window.location.href='profile.php';
                </script>";
            }
        } else {
            echo "<script>
                alert('Database error: " . addslashes($stmt->error) . "');
                window.location.href='profile.php';
            </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
            alert('Unauthorized action.');
            window.location.href='profile.php';
        </script>";
    }

    $conn->close();
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='profile.php';
    </script>";
}
?>