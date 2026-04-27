<?php 
include 'conn.php';
$id = $_GET['id'];

$delete = "DELETE FROM reservations WHERE user_id = '$id'";
if ($conn->query($delete)) {
    header('Location: home.php');
    exit;
} else {
    echo "Error deleting record: " . $conn->error;
}


?>