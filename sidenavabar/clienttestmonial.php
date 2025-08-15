<?php
require 'vendor/autoload.php'; // Cloudinary SDK autoload
use Cloudinary\Cloudinary;

// Database connection
include './db.php'; 

// Check DB connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Cloudinary credentials
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
    ]
]);

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Get public_id from DB
    $result = $conn->query("SELECT public_id FROM client_testimonials WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        $publicId = $row['public_id'];

        // Delete from Cloudinary
        $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'video']);

        // Delete from database
        $conn->query("DELETE FROM client_testimonials WHERE id = $id");
    }
    header("Location: ./clienttestmonial.php");
    exit();
}

// Handle upload request
if (isset($_POST['submit'])) {
    $title = $_POST['title'];

    if (!isset($_FILES['video']) || $_FILES['video']['error'] != 0) {
        die("Error: Please select a valid video file.");
    }

    try {
        // Upload to Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload(
            $_FILES['video']['tmp_name'],
            [
                'resource_type' => 'video',
                'folder' => 'client_testimonials'
            ]
        );

        $videoUrl = $uploadResult['secure_url'];
        $publicId = $uploadResult['public_id'];

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO client_testimonials (title, video_url, public_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $videoUrl, $publicId);
        $stmt->execute();

        echo "<script>alert('Video uploaded successfully!'); window.location.href='./clienttestmonial.php';</script>";
        exit();
    } catch (Exception $e) {
        echo "Upload failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Client Testimonial Video</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .upload-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 350px;
            margin-bottom: 30px;
        }
        .upload-form h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #1a3965;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #1452a1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1a3965;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .delete-btn {
            background: red;
            color: white;
            padding: 5px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-btn:hover {
            background: darkred;
        }
    </style>
</head>
<body>
<?php include './sidenavbar.php'; ?>
    <form class="upload-form" action="./uploadClientTestimonial.php" method="POST" enctype="multipart/form-data">
        <h2>Upload Testimonial Video</h2>
        <label for="title">Video Title</label>
        <input type="text" name="title" id="title" placeholder="Enter video title" required>

        <label for="video">Select Video File</label>
        <input type="file" name="video" id="video" accept="video/*" required>

        <button type="submit" name="submit">Upload</button>
    </form>

    <!-- Uploaded Videos Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Video</th>
            <th>Action</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM client_testimonials ORDER BY uploaded_at DESC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['title']}</td>
                        <td><a href='{$row['video_url']}' target='_blank'>View Video</a></td>
                        <td><a class='delete-btn' href='?delete={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this video?');\">Delete</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No videos uploaded yet.</td></tr>";
        }
        ?>
    </table>

</body>
</html>
