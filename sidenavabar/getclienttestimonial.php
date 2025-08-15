<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include './db.php'; 

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$sql = "SELECT id, title, video_url, public_id, uploaded_at FROM client_testimonials ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

$videos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

echo json_encode($videos);
$conn->close();
?>
