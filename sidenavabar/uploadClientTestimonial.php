<?php
require 'vendor/autoload.php'; // Cloudinary SDK autoload

use Cloudinary\Cloudinary;

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "twinkleadmin";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $title = $_POST['title'];

    if (!isset($_FILES['video']) || $_FILES['video']['error'] != 0) {
        die("Error: Please select a valid video file.");
    }

    // Cloudinary credentials
    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => 'dh9dpvul4',
            'api_key'    => '913163688842134',
            'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
        ]
    ]);

    try {
        // Upload video to Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload(
            $_FILES['video']['tmp_name'],
            [
                'resource_type' => 'video',
                'folder' => 'client_testimonials'
            ]
        );

        $videoUrl = $uploadResult['secure_url'];
        $publicId = $uploadResult['public_id'];

        // Store in database
        $stmt = $conn->prepare("INSERT INTO client_testimonials (title, video_url, public_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $videoUrl, $publicId);

        if ($stmt->execute()) {
            echo "<script>alert('Video uploaded successfully!!!!!!'); window.location.href='./';</script>";
        } else {
            echo "Database error: " . $stmt->error;
        }

    } catch (Exception $e) {
        echo "Upload failed: " . $e->getMessage();
    }
}
?>
