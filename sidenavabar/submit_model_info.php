<?php
// Database configuration
include './db.php'; 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $images = $_FILES['images'];

    // Validate inputs
    if (!$gender || !$height || empty($images['name'][0])) {
        die("All fields are required.");
    }

    // Insert model information into the database
    $stmt = $conn->prepare("INSERT INTO models (gender, height) VALUES (?, ?)");
    $stmt->bind_param("si", $gender, $height);

    if ($stmt->execute()) {
        $modelId = $stmt->insert_id; // Get the inserted model's ID
        $stmt->close();

        // Directory to save uploaded images
        $uploadDir = "uploads/modelimage/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Handle multiple image uploads
        foreach ($images['name'] as $key => $imageName) {
            if ($images['error'][$key] === 0) {
                $imageTmpName = $images['tmp_name'][$key];
                $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
                $imageNewName = uniqid("img_", true) . '.' . $imageExtension;

                if (move_uploaded_file($imageTmpName, $uploadDir . $imageNewName)) {
                    // Save image information into the database
                    $stmt = $conn->prepare("INSERT INTO model_images (model_id, image_path) VALUES (?, ?)");
                    $imagePath = $uploadDir . $imageNewName;
                    $stmt->bind_param("is", $modelId, $imagePath);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "<script>alert('Failed to upload image: $imageName'); window.location.href = 'uploadmodel.php';</script>";
                    exit;
                }
            }
        }

        echo "<script>alert('Model information and images uploaded successfully!'); window.location.href = 'uploadmodel.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $conn->close();
}
// Fetch and display uploaded model information
$query = "SELECT m.id, m.gender, m.height, GROUP_CONCAT(mi.image_path SEPARATOR ',') AS images 
          FROM models m 
          LEFT JOIN model_images mi ON m.id = mi.model_id 
          GROUP BY m.id";
$result = $conn->query($query);
?>
