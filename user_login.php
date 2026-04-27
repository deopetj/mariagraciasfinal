<?php
session_start();

if (isset($_SESSION['login_error'])) {
    $message = $_SESSION['login_error'];
    unset($_SESSION['login_error']); 
    echo "<script>
        alert('$message');
        window.location.href = 'home.php';
    </script>";
}
?>
