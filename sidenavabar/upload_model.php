<?php
require 'config.php';
require 'vendor/autoload.php'; // Cloudinary PHP SDK

use Cloudinary\Cloudinary;

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

$conn = getDbConnection();

// ---------- Handle Delete ----------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Fetch record
    $res = $conn->query("SELECT images, videos, pdf_url FROM new_models WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // Delete images
        if (!empty($row['images'])) {
            $images = json_decode($row['images'], true);
            foreach ($images as $imgUrl) {
                $publicId = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_FILENAME);
                $cloudinary->uploadApi()->destroy("models/images/$publicId", ["resource_type" => "image"]);
            }
        }

        // Delete videos
        if (!empty($row['videos'])) {
            $videos = json_decode($row['videos'], true);
            foreach ($videos as $vidUrl) {
                $publicId = pathinfo(parse_url($vidUrl, PHP_URL_PATH), PATHINFO_FILENAME);
                $cloudinary->uploadApi()->destroy("models/videos/$publicId", ["resource_type" => "video"]);
            }
        }

        // Delete PDF
        if (!empty($row['pdf_url'])) {
            $pdfUrl = $row['pdf_url'];
            $publicId = pathinfo(parse_url($pdfUrl, PHP_URL_PATH), PATHINFO_FILENAME);
            $cloudinary->uploadApi()->destroy("models/pdf/$publicId", ["resource_type" => "raw"]);
        }

        // Delete DB record
        $conn->query("DELETE FROM new_models WHERE id=$id");
    }

    header("Location: upload_model.php");
    exit;
}

// ---------- Handle Upload ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['model_name'])) {
    $model_name = $conn->real_escape_string($_POST['model_name']);
    $gender     = $conn->real_escape_string($_POST['gender']);
    $age        = intval($_POST['age']);

    $imageUrls = [];
    $videoUrls = [];
    $pdfUrl    = null;

    // Upload Images
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $filePath = $_FILES['images']['tmp_name'][$key];
                $uploadResult = $cloudinary->uploadApi()->upload($filePath, [
                    'folder' => 'models/images'
                ]);
                $imageUrls[] = $uploadResult['secure_url'];
            }
        }
    }

    // Upload Videos
    if (!empty($_FILES['videos']['name'][0])) {
        foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['videos']['error'][$key] === UPLOAD_ERR_OK) {
                $filePath = $_FILES['videos']['tmp_name'][$key];
                $uploadResult = $cloudinary->uploadApi()->upload($filePath, [
                    'resource_type' => 'video',
                    'folder' => 'models/videos'
                ]);
                $videoUrls[] = $uploadResult['secure_url'];
            }
        }
    }

    // Upload PDF
    if (!empty($_FILES['pdf']['name'])) {
        if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $filePath = $_FILES['pdf']['tmp_name'];
            $uploadResult = $cloudinary->uploadApi()->upload($filePath, [
                'resource_type' => 'raw',
                'folder' => 'models/pdf'
            ]);
            $pdfUrl = $uploadResult['secure_url'];
        }
    }

    // Save in DB
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
    $stmt->execute();
    $stmt->close();

    header("Location: upload_model.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Model Details</title>
  <style>
    body {font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 20px;}
    .upload-container {background: #fff; padding: 20px; border-radius: 10px; max-width: 900px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
    table {width: 100%; border-collapse: collapse; margin-top: 20px;}
    table th, table td {border: 1px solid #ddd; padding: 8px; text-align: center;}
    table th {background: #1a73e8; color: white;}
    img {width: 60px; height: 60px; object-fit: cover; border-radius: 6px;}
    a {color: #1a73e8; text-decoration: none;}
    a:hover {text-decoration: underline;}
    .delete-btn {padding: 5px 10px; background: red; color: white; border-radius: 5px; text-decoration: none;}
    
    .upload-container {
      background: #fff;
      margin: 30px;
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 600px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    label {
      font-weight: bold;
      display: block;
      margin: 10px 0 5px;
      color: #555;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
      font-size: 14px;
    }
    input[type="file"] {
      padding: 5px;
    }
    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      background: #1a73e8;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #155ab6;
    }
    .note {
      font-size: 12px;
      color: #888;
      margin-top: -10px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="upload-container">
    <h2>Upload Model Details</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <label>Model Name</label>
      <input type="text" name="model_name" required>
      <label>Gender</label>
      <select name="gender" required>
        <option value="">--Select--</option>
        <option>Male</option><option>Female</option><option>Other</option>
      </select>
      <label>Age</label>
      <input type="number" name="age" required>
      <label>Upload Images (max 10)</label>
      <input type="file" name="images[]" accept="image/*" multiple>
      <label>Upload Videos (max 10)</label>
      <input type="file" name="videos[]" accept="video/*" multiple>
      <label>Upload PDF</label>
      <input type="file" name="pdf" accept="application/pdf">
      <button type="submit">Upload</button>
    </form>

    <h2>Uploaded Models</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Model Name</th>
        <th>Gender</th>
        <th>Age</th>
        <th>Images</th>
        <th>Videos</th>
        <th>PDF</th>
        <th>Action</th>
      </tr>
      <?php
      $result = $conn->query("SELECT * FROM new_models ORDER BY id DESC");
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>".$row['id']."</td>";
              echo "<td>".$row['model_name']."</td>";
              echo "<td>".$row['gender']."</td>";
              echo "<td>".$row['age']."</td>";

              // Images
              echo "<td>";
              if (!empty($row['images'])) {
                  $imgs = json_decode($row['images'], true);
                  foreach ($imgs as $img) {
                      echo "<a href='$img' target='_blank'><img src='$img'></a> ";
                  }
              }
              echo "</td>";

              // Videos
              echo "<td>";
              if (!empty($row['videos'])) {
                  $vids = json_decode($row['videos'], true);
                  foreach ($vids as $vid) {
                      echo "<a href='$vid' target='_blank'>Video</a><br>";
                  }
              }
              echo "</td>";

              // PDF
              echo "<td>";
              if (!empty($row['pdf_url'])) {
                  echo "<a href='".$row['pdf_url']."' target='_blank'>View PDF</a>";
              }
              echo "</td>";

              echo "<td><a class='delete-btn' href='?delete=".$row['id']."' onclick=\"return confirm('Delete this record?')\">Delete</a></td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='8'>No records found</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>
