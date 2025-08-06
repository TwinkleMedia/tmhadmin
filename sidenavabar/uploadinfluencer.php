<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database configuration
    $host = 'localhost';
    $db = 'twinkleadmin';
    $user = 'root';
    $pass = '';

    // Connect to the database
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $category = $_POST['category'];
    $gender = $_POST['gender'];
    $insta_followers = $_POST['insta_followers'];
    $insta_avg_views = $_POST['insta_avg_views'];
    $youtube_subs = $_POST['youtube_subs'];
    $youtube_avg_views = $_POST['youtube_avg_views'];

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = 'uploads/infuencer/' . basename($image);

    if (move_uploaded_file($image_tmp, $image_path)) {
        $query = "INSERT INTO influencers (category, gender, image_path, insta_followers, insta_avg_views, youtube_subs, youtube_avg_views, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            'sssssss',
            $category,
            $gender,
            $image_path,
            $insta_followers,
            $insta_avg_views,
            $youtube_subs,
            $youtube_avg_views
        );

        if ($stmt->execute()) {
            echo "<script>alert('Data saved successfully!');</script>";
        } else {
            echo "<script>alert('Error saving data!');</script>";
        }
    } else {
        echo "<script>alert('Error uploading image!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Upload / Influencer</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f8fc;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .form-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 800px;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #1a3965;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-group input[type="file"] {
        padding: 5px;
    }

    .form-group textarea {
        resize: none;
        height: 80px;
    }

    .form-group .radio-group {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .form-group .radio-group input {
        margin-right: 5px;
    }

    .form-submit {
        text-align: center;
    }

    .form-submit button {
        background-color: #1a3965;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .form-submit button:hover {
        background-color: #145293;
    }

    @media (max-width: 480px) {
        .form-container {
            padding: 15px;
        }

        .form-submit button {
            width: 100%;
        }
    }
    .table-container {
            width: 90%;
            max-width: 1000px;
            overflow-x: auto;
        }

        table {
            width: 748px;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #1a3965;
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f4f8fc;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 15px;
            }

            .form-submit button {
                width: 100%;
            }

            .table-container {
                font-size: 14px;
            }
        }
</style>

<body>
    <?php
    include './sidenavbar.php'
    ?> <div class="form-container">
        <h2>Influencer Form</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category">Category of Influencer</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="tech">Tech</option>
                    <option value="fashion">Fashion</option>
                    <option value="lifestyle">Lifestyle</option>
                    <option value="fitness">Fitness</option>
                    <option value="food">Food</option>
                </select>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" required> Male</label>
                    <label><input type="radio" name="gender" value="female"> Female</label>
                  
                </div>
            </div>
            <div class="form-group">
                <label for="image">Upload Image of Influencer</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="insta_followers">Instagram Followers</label>
                <input type="text" id="insta_followers" name="insta_followers" placeholder="Enter the number of followers (e.g., 1k, 10M)" required>
            </div>

            <div class="form-group">
                <label for="insta_avg_views">Average Views on Instagram</label>
                <input type="text" id="insta_avg_views" name="insta_avg_views" placeholder="Enter the average views (e.g., 500k)" required>
            </div>

            <div class="form-group">
                <label for="youtube_subs">YouTube Subscribers</label>
                <input type="text" id="youtube_subs" name="youtube_subs" placeholder="Enter the number of subscribers (e.g., 2M)" required>
            </div>

            <div class="form-group">
                <label for="youtube_avg_views">Average Views on YouTube</label>
                <input type="text" id="youtube_avg_views" name="youtube_avg_views" placeholder="Enter the average views (e.g., 100k)" required>
            </div>

            <div class="form-submit">
                <button type="submit">Submit</button>
            </div>
        </form>
        <?php
// Add this at the top to handle deletion
if(isset($_POST['delete_id'])) {
    include 'db.php';
    
    // First get the image path
    $id = $_POST['delete_id'];
    $img_query = "SELECT image_path FROM influencers WHERE id = ?";
    $stmt = $conn->prepare($img_query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        // Delete the image file if it exists
        if(file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }
    
    // Delete the record
    $delete_query = "DELETE FROM influencers WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('i', $id);
    
    if($delete_stmt->execute()) {
        echo "<script>alert('Record deleted successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error deleting record!');</script>";
    }
}
?>
        <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Gender</th>
                    <th>Image</th>
                    <th>Instagram Followers</th>
                    <th>Instagram Avg. Views</th>
                    <th>YouTube Subscribers</th>
                    <th>YouTube Avg. Views</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic rows will be inserted here via PHP -->
                <?php
            include 'db.php';
            $query = "SELECT * FROM influencers ORDER BY created_at DESC";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($row['image_path']) . "' alt='Image' style='width: 50px; height: 50px;'></td>";
                    echo "<td>" . htmlspecialchars($row['insta_followers']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['insta_avg_views']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['youtube_subs']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['youtube_avg_views']) . "</td>";
                    echo "<td>
                        <form method='POST' style='margin: 0;' onsubmit='return confirm(\"Are you sure you want to delete this influencer?\");'>
                            <input type='hidden' name='delete_id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' style='background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;'>Delete</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No records found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    </div>
    
</body>

</html>