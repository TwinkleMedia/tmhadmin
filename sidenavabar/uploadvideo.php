<?php


set_time_limit(300);
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// Helper function to get public_id from Cloudinary URL
function getPublicId($url) {
    $path = parse_url($url, PHP_URL_PATH);

    // Example path: /tmh_website/thumbnails/sample.jpg  OR  /image/upload/v1234567890/tmh_website/thumbnails/sample.jpg
    // Remove the leading `/`
    $path = ltrim($path, '/');

    // Remove the first segment if it's "image/upload" or "video/upload"
    $path = preg_replace('#^(image|video)/upload/#', '', $path);

    // Remove version numbers like v1234567890/
    $path = preg_replace('#v[0-9]+/#', '', $path);

    // Remove file extension (.jpg, .mp4, etc.)
    $path = preg_replace('/\.[^.]+$/', '', $path);

    return $path;
}


// ==== CONFIGURE CLOUDINARY ====
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
    ],
    'url' => [
        'secure' => true
    ]
]);

// ==== DATABASE CONFIG ====
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "twinkleadmin";
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit();
}

// ===== DELETE VIDEO =====

// ...existing code...
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT thumbnail_url, video_url FROM videos WHERE id=$id");

    if ($row = $result->fetch_assoc()) {
        try {
            $thumbPublicId = getPublicId($row['thumbnail_url']);
            $videoPublicId = getPublicId($row['video_url']);

            // Delete thumbnail
            if ($thumbPublicId) {
                $resThumb = $cloudinary->uploadApi()->destroy($thumbPublicId, [
                    "resource_type" => "image",
                    "invalidate" => true
                ]);
                // Optional debug:
                // print_r($resThumb);
            }

            // Delete video
            if ($videoPublicId) {
                $resVideo = $cloudinary->uploadApi()->destroy($videoPublicId, [
                    "resource_type" => "video",
                    "invalidate" => true
                ]);
                // Optional debug:
                // print_r($resVideo);
            }

            // Delete from database AFTER Cloudinary delete
            $conn->query("DELETE FROM videos WHERE id=$id");

        } catch (Exception $e) {
            echo "Cloudinary delete error: " . $e->getMessage();
            exit();
        }
    }

    header("Location: http://localhost/tmhadmin/sidenavabar/work.php");
    exit();
}


// ...existing code...

// ==== PROCESS FORM ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];

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

        // Save URLs to database
        $stmt = $conn->prepare("INSERT INTO videos (title, thumbnail_url, video_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $thumbnail_url, $video_url);

        if ($stmt->execute()) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Error saving to database: " . $stmt->error;
        }

        $stmt->close();

    } catch (Exception $e) {
        http_response_code(500);
        echo "Upload failed: " . $e->getMessage();
    }
}

$conn->close();
?>