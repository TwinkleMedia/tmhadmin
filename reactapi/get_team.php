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

// Fetch all team members
$sql = "SELECT * FROM team";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $team = [];

    while ($row = $result->fetch_assoc()) {
        $team[] = $row;
    }

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($team);
} else {
    echo json_encode([]);
}

// Close the connection
$conn->close();
?>
