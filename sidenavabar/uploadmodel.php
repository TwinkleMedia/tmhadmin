<?php
// Database configuration
include './db.php'; 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if delete request is made
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // First, fetch all images associated with this model to delete them from the filesystem
    $imageQuery = "SELECT image_path FROM model_images WHERE model_id = ?";
    $stmt = $conn->prepare($imageQuery);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Delete image from the server
        $imagePath = $row['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);  // Delete the file from the server
        }
    }

    // Delete related images from the database
    $deleteImagesQuery = "DELETE FROM model_images WHERE model_id = ?";
    $stmt = $conn->prepare($deleteImagesQuery);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();

    // Delete the model from the database
    $deleteModelQuery = "DELETE FROM models WHERE id = ?";
    $stmt = $conn->prepare($deleteModelQuery);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();

    // Redirect after deletion
    header("Location: uploadmodel.php");
    exit();
}

// Fetch and display uploaded model information
$query = "SELECT m.id, m.gender, m.height, GROUP_CONCAT(mi.image_path SEPARATOR ',') AS images 
          FROM models m 
          LEFT JOIN model_images mi ON m.id = mi.model_id 
          GROUP BY m.id";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Model Information Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .form-container {
      background: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      padding: 20px;
      max-width: 600px;
      width: 90%;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #555;
      font-weight: bold;
    }

    .form-group input, 
    .form-group select,
    .form-group button {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
      font-size: 16px;
    }

    .form-group input[type="file"] {
      padding: 5px;
    }

    .form-group button {
      background-color: #007bff;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .form-group button:hover {
      background-color: #0056b3;
    }

    .form-group .hint {
      font-size: 12px;
      color: #888;
    }

    .image-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }

    .image-preview div {
      position: relative;
      display: inline-block;
    }

    .image-preview img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .image-preview .remove-btn {
      position: absolute;
      top: -5px;
      right: -5px;
      background: red;
      color: white;
      border: none;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 14px;
      cursor: pointer;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .form-container {
        padding: 15px;
      }

      .form-group input,
      .form-group select,
      .form-group button {
        font-size: 14px;
      }
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    .images {
        display: flex;
        gap: 10px;
    }

    .images img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
    }
  </style>
</head>
<body>
<?php 
    include './sidenavbar.php'
    ?>
  <div class="form-container">
    <h2>Upload Model Information</h2>
    <form action="submit_model_info.php" method="POST" enctype="multipart/form-data" id="modelForm">
      <div class="form-group">
        <label for="gender">Gender</label>
        <select name="gender" id="gender" required>
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <div class="form-group">
        <label for="images">Upload Model Images</label>
        <input type="file" name="images[]" id="images" accept="image/*" multiple required>
        <span class="hint">You can upload 1 to 6 images.</span>
        <div class="image-preview" id="imagePreview"></div>
      </div>

      <div class="form-group">
        <label for="height">Model Height (in cm)</label>
        <input type="number" name="height" id="height" placeholder="Enter height" min="50" max="300" required>
      </div>

      <div class="form-group">
        <button type="submit">Submit</button>
      </div>
    </form>
    <div>
    <div class="form-container">
    <h2>Uploaded Model Information</h2>
    <table>
      <thead>
        <tr>
          <th>Model ID</th>
          <th>Gender</th>
          <th>Height (cm)</th>
          <th>Images</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                $images = explode(',', $row['images']);
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "<td>" . $row['height'] . "</td>";
                echo "<td><div class='images'>";
                foreach ($images as $image) {
                    echo "<img src='" . $image . "' alt='Image'>";
                }
                echo "</div></td>";
                echo "<td><a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No models found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  </div>
 
  <script>
    const imagesInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');

    imagesInput.addEventListener('change', function () {
      imagePreview.innerHTML = '';
      const files = Array.from(imagesInput.files);

      files.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function (e) {
          const div = document.createElement('div');
          const img = document.createElement('img');
          const button = document.createElement('button');

          img.src = e.target.result;
          button.textContent = 'x';
          button.classList.add('remove-btn');
          button.setAttribute('data-index', index);

          button.addEventListener('click', function (e) {
            e.preventDefault();
            files.splice(index, 1);
            const dataTransfer = new DataTransfer();
            files.forEach(f => dataTransfer.items.add(f));
            imagesInput.files = dataTransfer.files;
            div.remove();
          });

          div.appendChild(img);
          div.appendChild(button);
          imagePreview.appendChild(div);
        };

        reader.readAsDataURL(file);
      });
    });
  </script>
</body>
</html>
