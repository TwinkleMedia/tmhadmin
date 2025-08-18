<?php
require 'config.php';
require 'vendor/autoload.php'; // Cloudinary PHP SDK

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

// Initialize Cloudinary
$cloudinary = new Cloudinary(
    [
        'cloud' => [
            'cloud_name' => CLOUDINARY_CLOUD_NAME,
            'api_key'    => CLOUDINARY_API_KEY,
            'api_secret' => CLOUDINARY_API_SECRET,
        ]
    ]
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDbConnection();

    $model_name = $conn->real_escape_string($_POST['model_name']);
    $gender     = $conn->real_escape_string($_POST['gender']);
    $age        = intval($_POST['age']);

    $imageUrls = [];
    $videoUrls = [];
    $pdfUrl    = null;

    // ---------- Upload Images ----------
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $filePath = $_FILES['images']['tmp_name'][$key];
                $uploadResult = $cloudinary->uploadApi()->upload($filePath, [
                    'folder' => 'new_models/images'
                ]);
                $imageUrls[] = $uploadResult['secure_url'];
            }
        }
    }

    // ---------- Upload Videos ----------
    if (!empty($_FILES['videos']['name'][0])) {
        foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['videos']['error'][$key] === UPLOAD_ERR_OK) {
                $filePath = $_FILES['videos']['tmp_name'][$key];
                $uploadResult = $cloudinary->uploadApi()->upload($filePath, [
                    'resource_type' => 'video',
                    'folder' => 'new_models/videos'
                ]);
                $videoUrls[] = $uploadResult['secure_url'];
            }
        }
    }

    // ---------- Upload PDF ----------
    if (!empty($_FILES['pdf']['name'])) {
        if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $filePath = $_FILES['pdf']['tmp_name'];
            $uploadResult = $cloudinary->uploadApi()->upload($filePath, [
                'resource_type' => 'raw',
                'folder' => 'new_models/pdf'
            ]);
            $pdfUrl = $uploadResult['secure_url'];
        }
    }

    // ---------- Save in Database ----------
    $sql = "INSERT INTO new_models (model_name, gender, age, images, videos, pdf_url) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
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
        echo "<script>alert('Model uploaded successfully!'); window.location.href='uploadmodel.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
