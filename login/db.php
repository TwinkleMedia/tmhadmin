

<!-- db.php -->


<?php
$servername = "localhost";
$username = "u694280384_twinkleadmin";  // Your database username
$password = "Deep@0118";      // Your database password
$dbname = "u694280384_twinkleadmin"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper handling of special characters
$conn->set_charset("utf8mb4");
?>