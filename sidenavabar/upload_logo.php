<?php
set_time_limit(300);

require 'vendor/autoload.php';
use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'doe3ms4zs',
        'api_key'    => '299873369678814',
        'api_secret' => 'I0ulV-BnLSUk5CCF9mn0bRcNF4k',
    ],
    'url' => ['secure' => true]
]);

// Database connection
$conn = new mysqli("localhost", "root", "", "twinkleadmin");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['client_logo'])) {
    $clientName = $conn->real_escape_string($_POST['client_name']);
    $fileTmpPath = $_FILES['client_logo']['tmp_name'];

    try {
        // Upload to Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload($fileTmpPath, [
            "folder" => "client_logos"
        ]);

        // Extract URL and Public ID
        $logoUrl   = $uploadResult['secure_url'];
        $publicId  = $uploadResult['public_id'];

        // Save to DB
        $stmt = $conn->prepare("INSERT INTO client_logos (client_name, logo_url, cloudinary_public_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $clientName, $logoUrl, $publicId);
        $stmt->execute();

        header("Location: clientlogo.php");
        exit();

    } catch (Exception $e) {
        echo "Upload error: " . $e->getMessage();
    }
}
?>
