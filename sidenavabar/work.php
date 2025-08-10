<!DOCTYPE html>
<html>
<head>
    <title>Upload Video to Cloudinary</title>
</head>
<body>
<h2>Upload Video</h2>
<form action="upload_video.php" method="POST" enctype="multipart/form-data">
    <label>Video Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Thumbnail (Image):</label><br>
    <input type="file" name="thumbnail" accept="image/*" required><br><br>

    <label>Video File:</label><br>
    <input type="file" name="video" accept="video/mp4" required><br><br>

    <button type="submit">Upload</button>
</form>
</body>
</html>
