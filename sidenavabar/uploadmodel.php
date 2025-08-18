<?php
require 'config.php';
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// Initialize Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ]
]);

$conn = getDbConnection();

class ModelHandler {
    private $conn;
    private $cloudinary;
    
    public function __construct($conn, $cloudinary) {
        $this->conn = $conn;
        $this->cloudinary = $cloudinary;
    }
    
    public function getAllModels() {
        $result = $this->conn->query("SELECT * FROM new_models ORDER BY id DESC");
        $models = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['images'] = !empty($row['images']) ? json_decode($row['images'], true) : [];
                $row['videos'] = !empty($row['videos']) ? json_decode($row['videos'], true) : [];
                $models[] = $row;
            }
        }
        
        return $models;
    }
    
    public function uploadModel($data, $files) {
        try {
            $model_name = $this->conn->real_escape_string($data['model_name']);
            $gender = $this->conn->real_escape_string($data['gender']);
            $age = intval($data['age']);

            $imageUrls = [];
            $videoUrls = [];
            $pdfUrl = null;

            // Upload Images
            if (!empty($files['images']['name'][0])) {
                foreach ($files['images']['tmp_name'] as $key => $tmp_name) {
                    if ($files['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $uploadResult = $this->cloudinary->uploadApi()->upload($tmp_name, [
                            'folder' => 'models/images'
                        ]);
                        $imageUrls[] = $uploadResult['secure_url'];
                    }
                }
            }

            // Upload Videos
            if (!empty($files['videos']['name'][0])) {
                foreach ($files['videos']['tmp_name'] as $key => $tmp_name) {
                    if ($files['videos']['error'][$key] === UPLOAD_ERR_OK) {
                        $uploadResult = $this->cloudinary->uploadApi()->upload($tmp_name, [
                            'resource_type' => 'video',
                            'folder' => 'models/videos'
                        ]);
                        $videoUrls[] = $uploadResult['secure_url'];
                    }
                }
            }

            // Upload PDF
            if (!empty($files['pdf']['name'])) {
                if ($files['pdf']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->cloudinary->uploadApi()->upload($files['pdf']['tmp_name'], [
                        'resource_type' => 'raw',
                        'folder' => 'models/pdf'
                    ]);
                    $pdfUrl = $uploadResult['secure_url'];
                }
            }

            // Save in DB
            $sql = "INSERT INTO new_models (model_name, gender, age, images, videos, pdf_url) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "ssisss",
                $model_name,
                $gender,
                $age,
                json_encode($imageUrls),
                json_encode($videoUrls),
                $pdfUrl
            );
            
            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => 'Model uploaded successfully!'];
            } else {
                throw new Exception('Failed to save to database');
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    public function deleteModel($id) {
        try {
            $id = intval($id);
            
            // Fetch record
            $res = $this->conn->query("SELECT images, videos, pdf_url FROM new_models WHERE id=$id");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();

                // Delete images from Cloudinary
                if (!empty($row['images'])) {
                    $images = json_decode($row['images'], true);
                    foreach ($images as $imgUrl) {
                        $publicId = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_FILENAME);
                        try {
                            $this->cloudinary->uploadApi()->destroy("models/images/$publicId", ["resource_type" => "image"]);
                        } catch (Exception $e) {
                            // Continue with deletion even if Cloudinary deletion fails
                        }
                    }
                }

                // Delete videos from Cloudinary
                if (!empty($row['videos'])) {
                    $videos = json_decode($row['videos'], true);
                    foreach ($videos as $vidUrl) {
                        $publicId = pathinfo(parse_url($vidUrl, PHP_URL_PATH), PATHINFO_FILENAME);
                        try {
                            $this->cloudinary->uploadApi()->destroy("models/videos/$publicId", ["resource_type" => "video"]);
                        } catch (Exception $e) {
                            // Continue with deletion even if Cloudinary deletion fails
                        }
                    }
                }

                // Delete PDF from Cloudinary
                if (!empty($row['pdf_url'])) {
                    $publicId = pathinfo(parse_url($row['pdf_url'], PHP_URL_PATH), PATHINFO_FILENAME);
                    try {
                        $this->cloudinary->uploadApi()->destroy("models/pdf/$publicId", ["resource_type" => "raw"]);
                    } catch (Exception $e) {
                        // Continue with deletion even if Cloudinary deletion fails
                    }
                }

                // Delete DB record
                if ($this->conn->query("DELETE FROM new_models WHERE id=$id")) {
                    return ['success' => true, 'message' => 'Model deleted successfully!'];
                } else {
                    throw new Exception('Failed to delete from database');
                }
            } else {
                return ['success' => false, 'message' => 'Model not found'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()];
        }
    }
}

// Create handler instance
$handler = new ModelHandler($conn, $cloudinary);

// Process form submissions and actions
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['model_name'])) {
    $result = $handler->uploadModel($_POST, $_FILES);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

if (isset($_GET['delete'])) {
    $result = $handler->deleteModel($_GET['delete']);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
    
    // Redirect to avoid resubmission
    header("Location: index.php?msg=" . urlencode($message) . "&type=" . $messageType);
    exit;
}

// Check for redirect messages
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'success';
}

// Get all models
$models = $handler->getAllModels();
?>