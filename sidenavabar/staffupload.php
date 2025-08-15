<?php
// Database configuration
include './db.php'; 

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_name = $_POST['staff_name'];
    $staff_designation = $_POST['staff_designation'];
    $staff_description = $_POST['staff_description']; // New field
    $image = $_FILES['staff_image']['name'];
    $target_dir = "uploads/staffimages/";
    $target_file = $target_dir . basename($image);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['staff_image']['tmp_name'], $target_file)) {
        // Insert data into the database
        $sql = "INSERT INTO staff (name, designation, description, image) 
                VALUES ('$staff_name', '$staff_designation', '$staff_description', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            header("Location: uploadstaff.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
}




// Close the connection
$conn->close();
?>
