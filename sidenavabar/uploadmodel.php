<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Model Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
    }
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
    <form action="upload_model.php" method="POST" enctype="multipart/form-data">
      
      <label for="model_name">Model Name</label>
      <input type="text" id="model_name" name="model_name" required>

      <label for="gender">Gender</label>
      <select id="gender" name="gender" required>
        <option value="">--Select Gender--</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>

      <label for="age">Age</label>
      <input type="number" id="age" name="age" min="1" required>

      <label for="images">Upload Images (1–10)</label>
      <input type="file" id="images" name="images[]" accept="image/*" multiple required>
      <p class="note">You can upload up to 10 images.</p>

      <label for="videos">Upload Videos (1–10)</label>
      <input type="file" id="videos" name="videos[]" accept="video/*" multiple>
      <p class="note">You can upload up to 10 videos.</p>

      <label for="pdf">Upload PDF</label>
      <input type="file" id="pdf" name="pdf" accept="application/pdf">

      <button type="submit">Upload</button>
    </form>
  </div>
</body>
</html>
