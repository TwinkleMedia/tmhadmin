<?php
require 'vendor/autoload.php'; // Composer autoload for Cloudinary

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

// Cloudinary config
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
    ],
    'url' => ['secure' => true]
]);

// DB connection
include 'db.php'; // Include your database configuration file
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$message = "";

// Handle DELETE request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT image_public_id FROM creative_form_work WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        $publicId = $row['image_public_id'];

        try {
            (new AdminApi())->deleteAssets([$publicId]);
        } catch (Exception $e) {
            $message = "Error deleting image from Cloudinary: " . $e->getMessage();
        }

        $conn->query("DELETE FROM creative_form_work WHERE id=$id");
        $message = "Creative work deleted successfully!";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);

    if (empty($title) || empty($category)) {
        $message = "Please fill in all fields.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $message = "Error uploading file.";
    } elseif ($_FILES['image']['size'] > 10 * 1024 * 1024) {
        $message = "File too large. Max 10MB allowed.";
    } else {
        $file = $_FILES['image'];

        try {
            $uploadResult = (new UploadApi())->upload($file['tmp_name'], [
                'folder' => 'creative_form_work_uploads'
            ]);
            $imageUrl = $uploadResult['secure_url'];
            $publicId = $uploadResult['public_id'];

            $stmt = $conn->prepare("INSERT INTO creative_form_work (title, category, image_url, image_public_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $category, $imageUrl, $publicId);
            $stmt->execute();

            $message = "Creative work uploaded successfully!";
        } catch (Exception $e) {
            $message = "Cloudinary Upload Error: " . $e->getMessage();
        }
    }
}

// Fetch all records
$result = $conn->query("SELECT * FROM creative_form_work ORDER BY id DESC");
