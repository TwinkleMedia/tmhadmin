<?php
set_time_limit(300);
require 'vendor/autoload.php';
use Cloudinary\Cloudinary;

function getPublicId($url) {
    $path = ltrim(parse_url($url, PHP_URL_PATH), '/');
    $path = preg_replace('#^(image|video)/upload/#', '', $path);
    $path = preg_replace('#v[0-9]+/#', '', $path);
    return preg_replace('/\.[^.]+$/', '', $path);
}

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
    ],
    'url' => ['secure' => true]
]);

$conn = new mysqli("localhost", "root", "", "twinkleadmin");
if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];

    // Check file sizes
    if ($_FILES['thumbnail']['size'] > 10 * 1024 * 1024) {
        die("Thumbnail size exceeds 10MB limit.");
    }
    if ($_FILES['video']['size'] > 100 * 1024 * 1024) {
        die("Video size exceeds 100MB limit.");
    }

    try {
        // Upload Thumbnail
        $thumbnailUpload = $cloudinary->uploadApi()->upload(
            $_FILES['thumbnail']['tmp_name'],
            ["folder" => "TMH_Website/thumbnails", "resource_type" => "image"]
        );
        $thumbnail_url = $thumbnailUpload['secure_url'];

        // Upload Video
        $videoUpload = $cloudinary->uploadApi()->upload(
            $_FILES['video']['tmp_name'],
            ["folder" => "TMH_Website/videos", "resource_type" => "video"]
        );
        $video_url = $videoUpload['secure_url'];

        // Save to database
        $stmt = $conn->prepare("INSERT INTO videos (title, category, thumbnail_url, video_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $category, $thumbnail_url, $video_url);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Video uploaded successfully!');
                    window.location.href = 'work.php';
                  </script>";
        } else {
            echo "Database error: " . $stmt->error;
        }
        $stmt->close();

    } catch (Exception $e) {
        echo "Upload failed: " . $e->getMessage();
    }
}

$conn->close();
?>
