<?php
require_once 'config.php';

// Initialize variables
$message = '';
$messageType = '';
$reels = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload':
                $result = handleUpload();
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'delete':
                $result = handleDelete();
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
        }
    }
}

// Load reels from database
try {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, title, video_url, cloudinary_public_id, created_at FROM reels ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $reels[] = $row;
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $message = 'Failed to load reels: ' . $e->getMessage();
    $messageType = 'error';
}

// Handle upload
function handleUpload() {
    if (!isset($_POST['reel_title']) || !isset($_FILES['reel_video'])) {
        return ['success' => false, 'message' => 'Title and video are required'];
    }

    $title = trim($_POST['reel_title']);
    $video = $_FILES['reel_video'];

    // Validate input
    if (empty($title)) {
        return ['success' => false, 'message' => 'Title cannot be empty'];
    }

    if ($video['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Video upload failed'];
    }

    // Validate video file
    $allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv'];
    if (!in_array($video['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid video format'];
    }

    // Check file size (100MB limit)
    if ($video['size'] > 100 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Video file is too large. Maximum size is 100MB'];
    }

    try {
        // Upload to Cloudinary
        $cloudinaryResult = uploadToCloudinary($video['tmp_name'], $video['name']);
        
        if (!$cloudinaryResult) {
            return ['success' => false, 'message' => 'Failed to upload video to Cloudinary'];
        }
        
        // Save to database
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO reels (title, video_url, cloudinary_public_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $cloudinaryResult['secure_url'], $cloudinaryResult['public_id']);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Video uploaded successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to save to database'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()];
    }
}

// Handle delete
function handleDelete() {
    if (!isset($_POST['reel_id']) || !is_numeric($_POST['reel_id'])) {
        return ['success' => false, 'message' => 'Invalid reel ID'];
    }

    $reelId = intval($_POST['reel_id']);

    try {
        $conn = getDbConnection();
        
        // Get reel details before deletion
        $stmt = $conn->prepare("SELECT cloudinary_public_id FROM reels WHERE id = ?");
        $stmt->bind_param("i", $reelId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Reel not found'];
        }
        
        $row = $result->fetch_assoc();
        $cloudinaryPublicId = $row['cloudinary_public_id'];
        $stmt->close();
        
        // Delete from Cloudinary first
        $cloudinaryDeleted = deleteFromCloudinary($cloudinaryPublicId);
        
        if (!$cloudinaryDeleted) {
            return ['success' => false, 'message' => 'Failed to delete video from Cloudinary'];
        }
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM reels WHERE id = ?");
        $stmt->bind_param("i", $reelId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $conn->close();
                return ['success' => true, 'message' => 'Reel deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Reel not found'];
            }
        } else {
            return ['success' => false, 'message' => 'Failed to delete from database'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()];
    }
}

// Upload to Cloudinary
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

// Delete from Cloudinary
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reel Video Upload</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

    /* Reset and Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary-color: #3182ce;
      --secondary-color: #2c5282;
      --background-color: #f7fafc;
      --text-color: #2d3748;
      --white: #ffffff;
      --success-color: #38a169;
      --error-color: #e53e3e;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      line-height: 1.6;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Upload Form Styles */
    .form-container {
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin-bottom: 30px;
    }

    .form-container h2 {
      text-align: center;
      color: var(--secondary-color);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-container h2 i {
      margin-right: 15px;
      color: var(--primary-color);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--text-color);
    }

    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }

    .form-group input[type="file"] {
      border: 2px dashed #e2e8f0;
      padding: 15px;
    }

    .form-group button {
      width: 100%;
      padding: 15px;
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
    }

    .form-group button:hover {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-group button i {
      margin-right: 10px;
    }

    /* Alert styles */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      display: block;
    }

    .alert-success {
      background-color: #c6f6d5;
      color: #2f855a;
      border: 1px solid #9ae6b4;
    }

    .alert-error {
      background-color: #fed7d7;
      color: #c53030;
      border: 1px solid #feb2b2;
    }

    /* Uploaded Reels Table Styles */
    .table-container {
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }

    .table-header {
      padding: 20px;
      border-bottom: 1px solid #e2e8f0;
    }

    .table-header h3 {
      color: var(--secondary-color);
      display: flex;
      align-items: center;
    }

    .table-header h3 i {
      margin-right: 10px;
      color: var(--primary-color);
    }

    .uploaded-reels-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .uploaded-reels-table thead {
      background-color: #f0f4f8;
    }

    .uploaded-reels-table th,
    .uploaded-reels-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    .uploaded-reels-table th {
      font-weight: 600;
      color: var(--secondary-color);
      text-transform: uppercase;
      font-size: 14px;
    }

    .uploaded-reels-table tr:last-child td {
      border-bottom: none;
    }

    .uploaded-reels-table .video-preview {
      max-width: 200px;
      max-height: 150px;
      border-radius: 8px;
    }

    .delete-btn {
      background-color: var(--error-color);
      color: var(--white);
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .delete-btn:hover {
      background-color: #c53030;
      transform: translateY(-1px);
    }

    .delete-btn i {
      margin-right: 5px;
    }

    .no-data {
      text-align: center;
      padding: 40px;
      color: #718096;
    }

    .no-data i {
      font-size: 3em;
      margin-bottom: 15px;
      opacity: 0.5;
    }

    /* Delete form styling */
    .delete-form {
      display: inline;
    }

    .delete-form button {
      background: none;
      border: none;
      padding: 0;
      font: inherit;
      cursor: pointer;
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
      .container {
        padding: 10px;
        width: 100%;
      }

      .form-container,
      .table-container {
        border-radius: 0;
        box-shadow: none;
        padding: 15px;
        margin-bottom: 15px;
      }

      .uploaded-reels-table thead {
        display: none;
      }

      .uploaded-reels-table tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background: var(--white);
      }

      .uploaded-reels-table td {
        display: block;
        text-align: right;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 15px;
        position: relative;
        min-height: 40px;
      }

      .uploaded-reels-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        top: 10px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
        color: var(--secondary-color);
      }

      .uploaded-reels-table td:last-child {
        border-bottom: none;
      }

      .uploaded-reels-table .video-preview {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        display: block;
      }

      .delete-btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <?php
    // Uncomment the line below if you have a side navigation
    // include './sidenavbar.php';
  ?>
  
  <div class="container">
    <!-- Upload Form -->
    <div class="form-container">
      <h2><i class="fas fa-cloud-upload-alt"></i>Upload Reel Video</h2>
      
      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">
        
        <div class="form-group">
          <label for="reel_title"><i class="fas fa-heading"></i> Reel Title</label>
          <input type="text" id="reel_title" name="reel_title" placeholder="Enter reel title" required>
        </div>
        
        <div class="form-group">
          <label for="reel_video"><i class="fas fa-video"></i> Reel Video</label>
          <input type="file" id="reel_video" name="reel_video" accept="video/*" required>
        </div>
        
        <div class="form-group">
          <button type="submit">
            <i class="fas fa-upload"></i>Upload
          </button>
        </div>
      </form>
    </div>

    <!-- Uploaded Reels Table -->
    <div class="table-container">
      <div class="table-header">
        <h3><i class="fas fa-film"></i>Uploaded Reels</h3>
      </div>
      
      <?php if (empty($reels)): ?>
        <div class="no-data">
          <i class="fas fa-film"></i>
          <p>No reels uploaded yet. Upload your first reel above!</p>
        </div>
      <?php else: ?>
        <table class="uploaded-reels-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Preview</th>
              <th>Upload Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reels as $reel): ?>
              <tr>
                <td data-label="ID"><?php echo $reel['id']; ?></td>
                <td data-label="Title"><?php echo htmlspecialchars($reel['title']); ?></td>
                <td data-label="Preview">
                  <video class="video-preview" controls preload="metadata">
                    <source src="<?php echo htmlspecialchars($reel['video_url']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
                </td>
                <td data-label="Upload Date"><?php echo date('M j, Y', strtotime($reel['created_at'])); ?></td>
                <td data-label="Actions">
                  <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this reel? This action cannot be undone.');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="reel_id" value="<?php echo $reel['id']; ?>">
                    <button type="submit" class="delete-btn">
                      <i class="fas fa-trash"></i>Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>