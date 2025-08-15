<?php
$host = 'localhost';
$dbname = 'u694280384_twinkleadmin';
$username = 'u694280384_twinkleadmin'; // Update as needed
$password = 'Deep@0118'; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
