<?php
$servername = 'localhost'; // changed to match variable used later
$dbname = 'u694280384_twinkleadmin';
$username = 'u694280384_twinkleadmin';
$password = 'Deep@0118';

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


