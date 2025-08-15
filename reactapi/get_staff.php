


<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
include './sidenavabar/db.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch staff details
$sql = "SELECT id, name, designation ,description, image FROM staff";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $staffDetails = array();
    while ($row = $result->fetch_assoc()) {
        $staffDetails[] = $row;
    }
    echo json_encode($staffDetails);
} else {
    echo json_encode([]);
}

// Close the connection
$conn->close();
?>
