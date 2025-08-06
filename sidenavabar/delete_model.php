<?php
// Include database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "twinkleadmin";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if model_id is set
if (isset($_POST['model_id'])) {
    $model_id = intval($_POST['model_id']);

    // Fetch associated images
    $image_query = "SELECT image_path FROM model_images WHERE model_id = $model_id";
    $image_result = $conn->query($image_query);

    if ($image_result->num_rows > 0) {
        while ($image_row = $image_result->fetch_assoc()) {
            $image_path = $image_row['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the image file
            }
        }
    }

    // Delete from model_images table
    $delete_images_query = "DELETE FROM model_images WHERE model_id = $model_id";
    $conn->query($delete_images_query);

    // Delete from models table
    $delete_model_query = "DELETE FROM models WHERE id = $model_id";
    $conn->query($delete_model_query);

    // Redirect back to the main page with a success message
    header("Location: index.php?message=Model+deleted+successfully");
    exit();
} else {
    // Redirect back with an error message
    header("Location: index.php?error=Invalid+request");
    exit();
}
?>
