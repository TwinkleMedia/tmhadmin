<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "twinkleadmin"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/team/";
    $target_file = $target_dir . basename($image);

    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO team (name, designation, image) VALUES ('$name', '$designation', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            // Show alert and redirect
            echo "<script>
                    alert('Team member uploaded successfully!');
                    window.location.href = './uploadteam.php'; // Replace with your form page URL
                  </script>";
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading image.";
    }
}

// Handle delete request
if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    $delete_query = "DELETE FROM team WHERE id = $delete_id";

    if ($conn->query($delete_query) === TRUE) {
        echo "<script>alert('Team member deleted successfully!');</script>";
        echo "<script> window.location.href = './uploadteam.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
