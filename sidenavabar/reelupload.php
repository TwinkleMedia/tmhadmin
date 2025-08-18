<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['reel_title']) || !isset($_FILES['reel_video'])) {
    echo json_encode(['success' => false, 'message' => 'Title and video are required']);
    exit;
}

$title = trim($_POST['reel_title']);
$video = $_FILES['reel_video'];

// Validate input
if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Title cannot be empty']);
    exit;
}

if ($video['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Video upload failed']);
    exit;
}

// Validate video file
$allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv'];
if (!in_array($video['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid video format']);
    exit;
}

try {
    // Upload to Cloudinary
    $cloudinaryResult = uploadToCloudinary($video['tmp_name'], $video['name']);
    
    if (!$cloudinaryResult) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload video to Cloudinary']);
        exit;
    }
    
    // Save to database
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO reels (title, video_url, cloudinary_public_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $cloudinaryResult['secure_url'], $cloudinaryResult['public_id']);
    
    if ($stmt->execute()) {
        $reelId = $conn->insert_id;
        echo json_encode([
            'success' => true, 
            'message' => 'Video uploaded successfully',
            'reel_id' => $reelId,
            'video_url' => $cloudinaryResult['secure_url']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save to database']);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()]);
}

function uploadToCloudinary($filePath, $fileName) {
    $url = 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/video/upload';
    
    // Generate timestamp
    $timestamp = time();
    
    // Create signature
    $paramsToSign = [
        'timestamp' => $timestamp
    ];
    
    ksort($paramsToSign);
    $signatureString = '';
    foreach ($paramsToSign as $key => $value) {
        $signatureString .= $key . '=' . $value . '&';
    }
    $signatureString = rtrim($signatureString, '&') . CLOUDINARY_API_SECRET;
    $signature = sha1($signatureString);
    
    // Prepare POST data
    $postData = [
        'file' => new CURLFile($filePath, 'video/mp4', $fileName),
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];
    
    // Upload using cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 60
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return false;
}
?>