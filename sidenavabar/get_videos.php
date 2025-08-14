<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

// Get category from query parameter, default to null (all videos)
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Build SQL query based on category
if ($category) {
    $sql = "SELECT id, thumbnail_url AS thumbnail, video_url AS videoUrl, category 
            FROM videos 
            WHERE category = ? 
            ORDER BY uploaded_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no category specified, fetch all videos
    $sql = "SELECT id, thumbnail_url AS thumbnail, video_url AS videoUrl, category 
            FROM videos 
            ORDER BY uploaded_at DESC";
    $result = $conn->query($sql);
}

$videos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

echo json_encode($videos);

// Close prepared statement if it exists
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>