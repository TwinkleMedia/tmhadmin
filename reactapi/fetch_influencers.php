<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
// Database configuration
include './sidenavabar/db.php';

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Query to fetch influencer data
$query = "SELECT category, gender, image_path, insta_followers, insta_avg_views, youtube_subs, youtube_avg_views, created_at FROM influencers";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $influencers = [];
    while ($row = $result->fetch_assoc()) {
        $influencers[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $influencers]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found']);
}

$conn->close();
?>
