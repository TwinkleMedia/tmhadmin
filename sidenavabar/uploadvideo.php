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
    echo "Database connection failed";
    exit();
}

// ===== DELETE VIDEO ==

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
