<?php
include './cloudnairy/config_cloudinary.php';
$conn = new mysqli("localhost", "root", "", "sidenavabar/db.php");

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'];

// Upload Thumbnail to Cloudinary
$thumbUpload = $cloudinary->uploadApi()->upload($_FILES['thumbnail']['tmp_name'], [
    'folder' => 'thumbnails',
    'resource_type' => 'image'
]);

// Upload Video to Cloudinary
$videoUpload = $cloudinary->uploadApi()->upload($_FILES['video']['tmp_name'], [
    'folder' => 'videos',
    'resource_type' => 'video'
]);

$thumbUrl = $thumbUpload['secure_url'];
$videoUrl = $videoUpload['secure_url'];

// Save in DB
$sql = "INSERT INTO videos (title, thumbnail, video_url) VALUES ('$title', '$thumbUrl', '$videoUrl')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Video uploaded to Cloudinary & saved in database.";
    echo "<br><a href='upload_video_form.php'>Upload Another</a>";
} else {
    echo "❌ Error: " . $conn->error;
}
$conn->close();
