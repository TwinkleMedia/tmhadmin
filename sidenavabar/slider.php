<?php
      // Database connection
include './db.php'; 

      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Handle form submission
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $title = htmlspecialchars($_POST['title']);
          $image = $_FILES['image'];

          // Validate the uploaded file
          if ($image['error'] === 0) {
              $targetDir = "uploads/slider/"; // Directory to store images
              if (!is_dir($targetDir)) {
                  mkdir($targetDir, 0777, true);
              }

              $fileName = basename($image['name']);
              $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

              // Move the file to the target directory
              if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                  // Save to database
                  $sql = "INSERT INTO sliderimages (title, image_path) VALUES (?, ?)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("ss", $title, $targetFilePath);

                  if ($stmt->execute()) {
                      echo "<script>alert('Image uploaded successfully!');</script>";
                  } else {
                      echo "<script>alert('Error saving image to database.');</script>";
                  }
              } else {
                  echo "<script>alert('Error uploading image to server.');</script>";
              }
          } else {
              echo "<script>alert('Error with the uploaded image.');</script>";
          }
      }
         // Handle delete action
         if (isset($_GET['delete_id'])) {
            $delete_id = intval($_GET['delete_id']);
            $query = "SELECT image_path FROM sliderimages WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
  
            if ($row) {
                $imagePath = $row['image_path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $deleteQuery = "DELETE FROM sliderimages WHERE id = ?";
                $stmt = $conn->prepare($deleteQuery);
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                echo "<script>alert('Image deleted successfully!'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
            }
        }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Image Upload Form</title>
 
</head>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
    color: #333;
    line-height: 1.6;
}

.form-container {
    max-width: 600px;
    margin: 20px auto;
    background: #fff;
    padding: 25px 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.form-container h2 {
    text-align: center;
    color: #2d3e50;
    font-size: 22px;
    margin-bottom: 15px;
}

.input-box {
    margin-bottom: 20px;
}

.input-box input,
.input-box select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.input-box input:focus,
.input-box select:focus {
    border-color: #2d3e50;
    outline: none;
}

.submit-btn {
    display: block;
    width: 100%;
    background-color: #2d3e50;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #1b2735;
}

.table-container {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table thead {
    background-color: #2d3e50;
    color: #fff;
}

table th,
table td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 14px;
}

table img {
    max-width: 80px;
    height: auto;
    border-radius: 5px;
    transition: transform 0.3s ease;
}

table img:hover {
    transform: scale(1.1);
}

.delete-btn {
    color: #e74c3c;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.delete-btn:hover {
    color: #c0392b;
}

@media (max-width: 768px) {
    nav h1 {
        font-size: 20px;
    }

    nav .dropdown-menu a {
        padding: 8px 10px;
    }

    .form-container,
    .table-container {
        padding: 15px;
    }

    table th,
    table td {
        font-size: 12px;
        padding: 8px;
    }
}

</style>
<body>
    <?php 
    include './sidenavbar.php';
    ?>
  <div class="form-container">
  <form method="POST" enctype="multipart/form-data">
      <h2>Upload Your Image</h2>
      <div class="input-box">
        <input type="text" name="title" placeholder="Enter Image Title" required>
      </div>
      <div class="input-box">
        <input type="file" name="image" accept="image/*" required>
      </div>
      <button type="submit" class="submit-btn">Upload</button>
    </form>
  </div>
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Image</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $fetchQuery = "SELECT * FROM sliderimages";
          $result = $conn->query($fetchQuery);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row['id'] . "</td>";
                  echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                  echo "<td><img src='" . $row['image_path'] . "' alt='Image' width='100'></td>";
                  echo "<td><a href='?delete_id=" . $row['id'] . "' class='delete-btn'>Delete</a></td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='4'>No images uploaded yet.</td></tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
