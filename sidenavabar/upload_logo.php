<?php
// Database Config
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "twinkleadmin"; // Change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Cloudinary Config
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
    ]
]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientName = $conn->real_escape_string($_POST['client_name']);

    if (isset($_FILES['client_logo']) && $_FILES['client_logo']['error'] == 0) {
        $fileTmpPath = $_FILES['client_logo']['tmp_name'];

        // Upload to Cloudinary
        try {
            $uploadResult = $cloudinary->uploadApi()->upload($fileTmpPath, [
                'folder' => 'TMH_Website/client_logos'
            ]);

            $imageUrl = $uploadResult['secure_url'];

            // Save to DB
            $sql = "INSERT INTO client_logos (client_name, logo_url) VALUES ('$clientName', '$imageUrl')";
            if ($conn->query($sql) === TRUE) {
                echo "Client logo uploaded successfully!<br>";
                echo "<img src='$imageUrl' width='150'><br>";
                echo "<a href='admin_client_logo_form.html'>Upload Another</a>";
            } else {
                echo "Database Error: " . $conn->error;
            }
        } catch (Exception $e) {
            echo "Cloudinary Upload Failed: " . $e->getMessage();
        }
    } else {
        echo "Please select a valid image file.";
    }
}
?>
