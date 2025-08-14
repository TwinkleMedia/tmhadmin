<?php
set_time_limit(300);
require 'vendor/autoload.php';
use Cloudinary\Cloudinary;

// Fixed function to get public ID from Cloudinary URL
function getPublicId($url) {
    // Parse the URL
    $parsed = parse_url($url);
    $path = $parsed['path'];
    
    // Remove leading slash
    $path = ltrim($path, '/');
    
    // Split by '/'
    $parts = explode('/', $path);
    
    // Find the 'upload' part and get everything after it
    $uploadIndex = array_search('upload', $parts);
    
    if ($uploadIndex === false) {
        return false; // 'upload' not found
    }
    
    // Get everything after 'upload'
    $relevantParts = array_slice($parts, $uploadIndex + 1);
    
    // The last part contains the filename with extension
    $filename = array_pop($relevantParts);
    
    // Remove the file extension from the filename
    $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    
    // Add the filename back to the parts
    $relevantParts[] = $filenameWithoutExt;
    
    // Join to form the public ID
    return implode('/', $relevantParts);
}

// Alternative regex method (more reliable)
function getPublicIdRegex($url) {
    // Match pattern: /upload/[version/]folder/subfolder/filename.ext
    if (preg_match('/\/upload\/(?:v\d+\/)?(.+)\.([^.]+)$/', $url, $matches)) {
        return $matches[1]; // Return everything between upload/ and the file extension
    }
    return false;
}

// Improved delete function with better error handling
function deleteFromCloudinary($cloudinary, $url, $resourceType) {
    try {
        $publicId = getPublicIdRegex($url); // Using the regex method as it's more reliable
        
        if (!$publicId) {
            throw new Exception("Could not extract public ID from URL: $url");
        }
        
        echo "Attempting to delete: $publicId (type: $resourceType)<br>";
        
        $result = $cloudinary->uploadApi()->destroy($publicId, [
            "resource_type" => $resourceType
        ]);
        
        echo "Cloudinary response: " . json_encode($result) . "<br>";
        
        if ($result['result'] !== 'ok' && $result['result'] !== 'not found') {
            throw new Exception("Cloudinary deletion failed: " . json_encode($result));
        }
        
        return true;
    } catch (Exception $e) {
        echo "Error deleting from Cloudinary: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Cloudinary config
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
    ],
    'url' => ['secure' => true]
]);

// DB connection
$conn = new mysqli("localhost", "root", "", "twinkleadmin");
if ($conn->connect_error) {
    die("Database connection failed");
}

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_video'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];

    if ($_FILES['thumbnail']['size'] > 10 * 1024 * 1024) {
        die("Thumbnail size exceeds 10MB limit.");
    }
    if ($_FILES['video']['size'] > 100 * 1024 * 1024) {
        die("Video size exceeds 100MB limit.");
    }

    try {
        // Upload thumbnail
        $thumbnailUpload = $cloudinary->uploadApi()->upload(
            $_FILES['thumbnail']['tmp_name'],
            ["folder" => "TMH_Website/thumbnails", "resource_type" => "image"]
        );
        $thumbnail_url = $thumbnailUpload['secure_url'];

        // Upload video
        $videoUpload = $cloudinary->uploadApi()->upload(
            $_FILES['video']['tmp_name'],
            ["folder" => "TMH_Website/videos", "resource_type" => "video"]
        );
        $video_url = $videoUpload['secure_url'];

        // Save to DB
        $stmt = $conn->prepare("INSERT INTO videos (title, category, thumbnail_url, video_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $category, $thumbnail_url, $video_url);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Video uploaded successfully!'); window.location.href='work.php';</script>";
    } catch (Exception $e) {
        echo "Upload failed: " . $e->getMessage();
    }
}

// Handle delete with improved error handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_video'])) {
    $id = $_POST['id'];
    $thumbnail_url = $_POST['thumbnail_url'];
    $video_url = $_POST['video_url'];

    echo "<div style='background: white; padding: 20px; margin: 20px; border-radius: 5px; border: 1px solid #ddd;'>";
    echo "<h3>Deletion Process Log:</h3>";
    
    $thumbnailDeleted = false;
    $videoDeleted = false;
    
    try {
        // Delete thumbnail from Cloudinary
        echo "<p><strong>Deleting thumbnail...</strong></p>";
        $thumbnailDeleted = deleteFromCloudinary($cloudinary, $thumbnail_url, "image");
        
        // Delete video from Cloudinary
        echo "<p><strong>Deleting video...</strong></p>";
        $videoDeleted = deleteFromCloudinary($cloudinary, $video_url, "video");
        
        // Delete from database (you can modify this logic based on your preference)
        if ($thumbnailDeleted && $videoDeleted) {
            $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            
            echo "<p style='color: green;'><strong>✓ Video and thumbnail deleted successfully from both Cloudinary and database!</strong></p>";
            echo "<script>setTimeout(function(){ window.location.href='work.php'; }, 3000);</script>";
        } elseif ($thumbnailDeleted || $videoDeleted) {
            // Partial success - you might want to delete from DB anyway
            $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            
            echo "<p style='color: orange;'><strong>⚠ Warning: Some files may not have been deleted from Cloudinary, but database record was deleted.</strong></p>";
            echo "<script>setTimeout(function(){ window.location.href='work.php'; }, 3000);</script>";
        } else {
            echo "<p style='color: red;'><strong>✗ Failed to delete files from Cloudinary. Database record was NOT deleted.</strong></p>";
            echo "<script>setTimeout(function(){ window.location.href='work.php'; }, 5000);</script>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>✗ Delete failed: " . $e->getMessage() . "</strong></p>";
        echo "<script>setTimeout(function(){ window.location.href='work.php'; }, 5000);</script>";
    }
    
    echo "</div>";
}

// Debug mode - add ?debug=1 to your URL to test public ID extraction
if (isset($_GET['debug'])) {
    echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px;'>";
    echo "<h3>Debug Mode - Testing Public ID Extraction</h3>";
    
    // Get a sample URL from your database for testing
    $result = $conn->query("SELECT thumbnail_url, video_url FROM videos LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        echo "<h4>Testing with actual URLs from your database:</h4>";
        
        echo "<p><strong>Thumbnail URL:</strong> " . $row['thumbnail_url'] . "</p>";
        echo "<p><strong>Extracted Public ID (method 1):</strong> " . getPublicId($row['thumbnail_url']) . "</p>";
        echo "<p><strong>Extracted Public ID (method 2):</strong> " . getPublicIdRegex($row['thumbnail_url']) . "</p>";
        
        echo "<p><strong>Video URL:</strong> " . $row['video_url'] . "</p>";
        echo "<p><strong>Extracted Public ID (method 1):</strong> " . getPublicId($row['video_url']) . "</p>";
        echo "<p><strong>Extracted Public ID (method 2):</strong> " . getPublicIdRegex($row['video_url']) . "</p>";
    }
    echo "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video to Cloudinary</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-top: 15px; }
        input, button { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #007bff; color: #fff; border: none; cursor: pointer; margin-top: 20px; }
        button:hover { background: #0056b3; }
        table { width: 90%; margin: 40px auto; border-collapse: collapse; background: white; }
        table, th, td { border: 1px solid #ccc; }
        th { background: #007bff; color: white; padding: 10px; }
        td { padding: 10px; text-align: center; }
        img { border-radius: 5px; }
        .delete-btn { background: red; color: white; padding: 5px 10px; border: none; cursor: pointer; border-radius: 4px; }
        .debug-link { position: fixed; top: 10px; right: 10px; background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
   <?php 
    include './sidenavbar.php'
    ?>
<a href="?debug=1" class="debug-link">Debug Mode</a>

<h2 style="text-align:center;">Upload Video</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <label for="title">Video Title</label>
    <input type="text" name="title" id="title" required>

    <label for="category">Video Category</label>
    <input type="text" name="category" id="category" placeholder="Enter video category" required>

    <label for="thumbnail">Thumbnail Image (Max 10MB)</label>
    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" required>

    <label for="video">Video File (Max 100MB)</label>
    <input type="file" name="video" id="video" accept="video/*" required>

    <button type="submit" name="upload_video">Upload Video</button>
</form>

<h2 style="text-align:center;">Uploaded Videos</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Thumbnail</th>
            <th>Video</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT * FROM videos ORDER BY id DESC");
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><img src="<?= $row['thumbnail_url'] ?>" width="100"></td>
            <td><a href="<?= $row['video_url'] ?>" target="_blank">View</a></td>
            <td>
                <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this video?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="thumbnail_url" value="<?= $row['thumbnail_url'] ?>">
                    <input type="hidden" name="video_url" value="<?= $row['video_url'] ?>">
                    <button type="submit" name="delete_video" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6">No videos uploaded yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php $conn->close(); ?>