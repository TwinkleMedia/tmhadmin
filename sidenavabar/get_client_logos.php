<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include './db.php'; // Make sure this file defines $conn

// Check DB connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

// Fetch id, logo_url, and cloudinary_public_id
$sql = "SELECT id, logo_url AS image, cloudinary_public_id 
        FROM client_logos 
        ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

$logos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logos[] = $row;
    }
}

echo json_encode($logos);
$conn->close();
?>
