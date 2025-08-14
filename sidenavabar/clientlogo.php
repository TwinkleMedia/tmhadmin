<?php
require 'vendor/autoload.php'; // Cloudinary SDK
// Connect to DB
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "twinkleadmin";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("DELETE FROM `client_logos`  WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        $cloudinary = new Cloudinary\Cloudinary([
            'cloud' => [
                'cloud_name' => 'dh9dpvul4',
        'api_key'    => '913163688842134',
        'api_secret' => 'FR5RjEj7it70xfBMnT53mgW-uds',
            ]
        ]);
        $cloudinary->uploadApi()->destroy($row['cloudinary_public_id']);

        $conn->query("DELETE FROM client_logos WHERE id=$id");
    }
    header("Location: clientlogo.php");
    exit;
}

// Fetch logos
$logos = [];
$result = $conn->query("SELECT id, client_name, logo_url FROM client_logos ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logos[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Client Logos</title>
    <style>
        body { font-family: Arial; background: #f7f9fc; padding: 20px; }
        h2 { text-align: center; }
        form { background: #fff; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, button { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007BFF; color: white; font-size: 16px; cursor: pointer; border: none; margin-top: 15px; }
        button:hover { background: #0056b3; }
        .gallery { margin-top: 40px; }
        .logo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .logo-card { background: #fff; padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.08); }
        .logo-card img { max-width: 100%; max-height: 120px; border-radius: 8px; object-fit: contain; }
        .delete-btn { background: #dc3545; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; display: inline-block; margin-top: 8px; }
        .delete-btn:hover { background: #c82333; }
    </style>
</head>
<body>

<h2>Upload Client Logo</h2>
<form method="POST" enctype="multipart/form-data" action="uploadlogo.php">
    <label>Client Name:</label>
    <input type="text" name="client_name" required>
    <label>Client Logo:</label>
    <input type="file" name="client_logo" accept="image/*" required>
    <button type="submit">Upload</button>
</form>

<div class="gallery">
    <h2>All Client Logos</h2>
    <div class="logo-grid">
        <?php
        if (!empty($logos)) {
            foreach ($logos as $logo) {
                echo '<div class="logo-card">';
                echo '<img src="' . $logo['logo_url'] . '" alt="Logo">';
                echo '<p>' . htmlspecialchars($logo['client_name']) . '</p>';
                echo '<a class="delete-btn" href="clientlogo.php?delete=' . $logo['id'] . '" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                echo '</div>';
            }
        } else {
            echo "<p>No logos uploaded yet.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
