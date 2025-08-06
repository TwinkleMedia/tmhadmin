<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "twinkleadmin";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Query to fetch model details
$query = "SELECT m.id, m.gender, m.height, GROUP_CONCAT(mi.image_path SEPARATOR ',') AS images 
          FROM models m 
          LEFT JOIN model_images mi ON m.id = mi.model_id 
          GROUP BY m.id";

$result = $conn->query($query);

// Check if data exists
if ($result->num_rows > 0) {
    $models = [];

    while ($row = $result->fetch_assoc()) {
        $row['images'] = explode(',', $row['images']); // Convert images string to array
        $models[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $models
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No models found."
    ]);
}

// Close connection
$conn->close();
?>
