<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, title, video_url, cloudinary_public_id, created_at FROM reels ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reels = [];
    while ($row = $result->fetch_assoc()) {
        $reels[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'video_url' => $row['video_url'],
            'cloudinary_public_id' => $row['cloudinary_public_id'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reels' => $reels
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch reels: ' . $e->getMessage()
    ]);
}
?>