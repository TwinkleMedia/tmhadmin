<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input for DELETE request or POST data
$input = json_decode(file_get_contents('php://input'), true);
$reelId = isset($input['id']) ? intval($input['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($reelId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid reel ID']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Get reel details before deletion
    $stmt = $conn->prepare("SELECT cloudinary_public_id FROM reels WHERE id = ?");
    $stmt->bind_param("i", $reelId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Reel not found']);
        exit;
    }
    
    $row = $result->fetch_assoc();
    $cloudinaryPublicId = $row['cloudinary_public_id'];
    $stmt->close();
    
    // Delete from Cloudinary first
    $cloudinaryDeleted = deleteFromCloudinary($cloudinaryPublicId);
    
    if (!$cloudinaryDeleted) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete video from Cloudinary']);
        exit;
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM reels WHERE id = ?");
    $stmt->bind_param("i", $reelId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Reel deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reel not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete from database']);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
}

function deleteFromCloudinary($publicId) {
    $url = 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/video/destroy';
    
    // Generate timestamp
    $timestamp = time();
    
    // Create signature
    $paramsToSign = [
        'public_id' => $publicId,
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
        'public_id' => $publicId,
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];
    
    // Delete using cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        return isset($result['result']) && $result['result'] === 'ok';
    }
    
    return false;
}
?>