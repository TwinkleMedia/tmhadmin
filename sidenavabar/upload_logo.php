<?php
set_time_limit(300);

require 'vendor/autoload.php';
use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
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
