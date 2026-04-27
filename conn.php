<?php

// Database credentials
define('DB_SERVER', 'sql312.infinityfree.com');
define('DB_USERNAME', 'if0_41647347');
define('DB_PASSWORD', 'EOPPvTDKIzp6vC5');
define('DB_NAME', 'if0_41647347_mgdbfinal');

// Connect to the database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>