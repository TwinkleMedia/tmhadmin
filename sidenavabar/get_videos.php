<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
// ...existing code...
// Database config
$servername = "localhost";
$username   = "root"; 
$password   = "";     
$dbname     = "twinkleadmin";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Fetch videos
$sql = "SELECT id, thumbnail_url AS thumbnail, video_url AS videoUrl FROM videos ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

$videos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

echo json_encode($videos);
$conn->close();
