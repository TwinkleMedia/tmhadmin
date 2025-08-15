<?php
// Database configuration
include './db.php'; 

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete staff entry if 'delete' is triggered
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM staff WHERE id = $id";

    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Staff deleted successfully!'); window.location.href='uploadstaff.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch staff information from the database
$sql = "SELECT * FROM staff";
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>



<?php
// Close the connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .form-container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input[type="submit"] {
            background-color: #1a3965;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .form-group input[type="submit"]:hover {
            background-color: #1452a5;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 15px;
            }

            .form-group input[type="text"],
            .form-group input[type="file"] {
                font-size: 0.9rem;
            }

            .form-group input[type="submit"] {
                font-size: 0.9rem;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }


        table th {
            background-color: #1a3965;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .delete-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-button:hover {
            background-color: #ff1a1a;
        }
    </style>
</head>

<body>
    <?php
    include './sidenavbar.php';
    ?>
    <div class="form-container">
        <h2>Staff Details Form</h2>
        <form action="./staffupload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="staff-name">Name of Staff:</label>
                <input type="text" id="staff-name" name="staff_name" placeholder="Enter staff name" required>
            </div>

            <div class="form-group">
                <label for="staff-designation">Designation:</label>
                <input type="text" id="staff-designation" name="staff_designation" placeholder="Enter staff designation" required>
            </div>
            <div class="form-group">
                <label for="staff-description">Description:</label>
                <textarea id="staff-description" name="staff_description" placeholder="Enter staff description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="staff-image">Image of Staff:</label>
                <input type="file" id="staff-image" name="staff_image" accept="image/*" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
        <h2>Staff Information</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['designation']}</td>
                        <td>{$row['description']}</td>
                        <td><img src='{$row['image']}' alt='Staff Image' width='50' height='50'></td>
                        <td>
                            <a href='uploadstaff.php?delete={$row['id']}'>
                                <button class='delete-button'>Delete</button>
                            </a>
                        </td>
                      </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>


    </div>
</body>

</html>