<?php include 'uploadCreation.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creative Work Upload</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f8ff; padding: 20px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 15px rgba(0,0,0,0.1); max-width: 400px; margin: auto; margin-bottom: 30px; }
        h2 { text-align: center; color: #0044cc; }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input[type="text"], input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 10px; }
        input[type="submit"] { background-color: #0044cc; color: white; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; width: 100%; }
        input[type="submit"]:hover { background-color: #003399; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0px 4px 15px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #0044cc; color: white; }
        .delete-btn { background: red; color: white; padding: 6px 10px; text-decoration: none; border-radius: 4px; }
        .delete-btn:hover { background: darkred; }
        .message { background: lightgreen; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; color: green; font-weight: bold; }
    </style>
</head>
<body>
  <?php 
    include './sidenavbar.php'
    ?>
<div class="form-container">
    <h2>Upload Creative Work</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
        <script>
            setTimeout(() => {
                window.location.href = "creative.php";
            }, 1500);
        </script>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Work Title</label>
        <input type="text" name="title" id="title" required>

        <label for="category">Category Name</label>
        <input type="text" name="category" id="category" required>

        <label for="image">Work Image</label>
        <input type="file" name="image" id="image" accept="image/*" required>
        <small>Max size: 10MB</small>

        <input type="submit" value="Upload">
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Category</th>
        <th>Image</th>
        <th>Action</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><img src="<?= $row['image_url'] ?>" alt="Work" width="100"></td>
                <td><a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this work?')">Delete</a></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No creative works found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
