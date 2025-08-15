<?php
set_time_limit(300);

require 'vendor/autoload.php';
use Cloudinary\Cloudinary;

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
include './db.php'; // Ensure this file contains the correct DB connection code

// DELETE logo
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // 1. Get public_id from DB
    $result = $conn->query("SELECT cloudinary_public_id FROM client_logos WHERE id=$id");

    if ($row = $result->fetch_assoc()) {
        $logoPublicId = $row['cloudinary_public_id'];

        try {
            // 2. Delete from Cloudinary
            if (!empty($logoPublicId)) {
                $cloudinary->uploadApi()->destroy($logoPublicId, [
                    "resource_type" => "image",
                    "invalidate" => true
                ]);
            }

            // 3. Delete from DB
            $conn->query("DELETE FROM client_logos WHERE id=$id");

        } catch (Exception $e) {
            echo "Cloudinary delete error: " . $e->getMessage();
            exit();
        }
    }

    // Redirect back
    header("Location: clientlogo.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Manage Client Logos</title>
    <style>
       body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

form {
    background: #fff;
    padding: 20px;
    max-width: 600px;
    margin: 20px auto;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    margin-top: 10px;
}

input, 
button {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    background: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border: none;
    margin-top: 15px;
}

button:hover {
    background: #0056b3;
}

.gallery {
    margin-top: 40px;
    max-width: 90%;
    margin-left: auto;
    margin-right: auto;
}

.logo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 20px;
}

.logo-card {
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.logo-card img {
    max-width: 100%;
    max-height: 120px;
    border-radius: 4px;
    object-fit: contain;
}

.delete-btn {
    background: #dc3545;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-block;
    margin-top: 8px;
    border: none;
    cursor: pointer;
}

.delete-btn:hover {
    background: #c82333;
}

.table-container {
    width: 100%;
    max-width: 90%;
    margin: 20px auto;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

table th, 
table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

table th {
    background-color: #007bff;
    color: white;
}

table td img {
    max-width: 100px;
    height: auto;
    border-radius: 4px;
}

@media (max-width: 768px) {
    form {
        width: 90%;
    }
    .gallery, .table-container {
        max-width: 100%;
    }
}

    </style>
</head>
<body>
<?php 
    include './sidenavbar.php'
    ?>
<h2>Upload Client Logo</h2>
<form method="POST" enctype="multipart/form-data" action="upload_logo.php">
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
<h2 style="text-align:center;">Uploaded Client Logos</h2>
<table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse; background: #fff;">
    <thead>
        <tr style="background:#f2f2f2;">
            <th>ID</th>
            <th>Client Name</th>
            <th>Logo</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT id, client_name, logo_url FROM client_logos ORDER BY id DESC");
        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['client_name']) ?></td>
            <td><img src="<?= $row['logo_url'] ?>" width="100" style="border-radius:5px;"></td>
            <td>
                <form action="" method="GET" onsubmit="return confirm('Are you sure you want to delete this logo?');" style="display:inline;">
                    <input type="hidden" name="delete" value="<?= $row['id'] ?>">
                    <button type="submit" class="delete-btn" style="background:#dc3545;color:white;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="4">No logos uploaded yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
