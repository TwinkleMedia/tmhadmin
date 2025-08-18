<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reel Video Upload</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

    /* Reset and Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary-color: #3182ce;
      --secondary-color: #2c5282;
      --background-color: #f7fafc;
      --text-color: #2d3748;
      --white: #ffffff;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      line-height: 1.6;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Upload Form Styles */
    .form-container {
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin-bottom: 30px;
    }

    .form-container h2 {
      text-align: center;
      color: var(--secondary-color);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-container h2 i {
      margin-right: 15px;
      color: var(--primary-color);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--text-color);
    }

    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }

    .form-group input[type="file"] {
      border: 2px dashed #e2e8f0;
      padding: 15px;
    }

    .form-group button {
      width: 100%;
      padding: 15px;
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-group button:hover {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-group button i {
      margin-right: 10px;
    }

    /* Uploaded Reels Table Styles */
    .table-container {
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }

    .uploaded-reels-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .uploaded-reels-table thead {
      background-color: #f0f4f8;
    }

    .uploaded-reels-table th,
    .uploaded-reels-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    .uploaded-reels-table th {
      font-weight: 600;
      color: var(--secondary-color);
      text-transform: uppercase;
      font-size: 14px;
    }

    .uploaded-reels-table tr:last-child td {
      border-bottom: none;
    }

    .uploaded-reels-table .video-preview {
      max-width: 200px;
      border-radius: 8px;
    }

    .delete-btn {
      background-color: #e53e3e;
      color: var(--white);
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: background-color 0.3s ease;
    }

    .delete-btn:hover {
      background-color: #c53030;
    }

    .delete-btn i {
      margin-right: 5px;
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
      .container {
        padding: 10px;
        width: 100%;
      }

      .form-container,
      .table-container {
        border-radius: 0;
        box-shadow: none;
        padding: 15px;
        margin-bottom: 15px;
      }

      .form-group {
        margin-bottom: 15px;
      }

      .form-group label {
        font-size: 14px;
        margin-bottom: 5px;
      }

      .form-group input {
        font-size: 14px;
        padding: 10px;
      }

      .form-group button {
        font-size: 14px;
        padding: 12px;
      }

      .uploaded-reels-table {
        font-size: 12px;
        width: 100%;
      }

      .uploaded-reels-table thead {
        display: none;
      }

      .uploaded-reels-table tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
      }

      .uploaded-reels-table td {
        display: block;
        text-align: right;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px;
        position: relative;
      }

      .uploaded-reels-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 10px;
      }

      .uploaded-reels-table td:last-child {
        border-bottom: none;
      }

      .uploaded-reels-table .video-preview {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        display: block;
      }

      .delete-btn {
        width: 100%;
        justify-content: center;
      }
    }

    @media screen and (max-width: 480px) {
      body {
        padding: 10px;
      }

      .form-container h2 {
        font-size: 18px;
      }

      .form-container h2 i {
        margin-right: 10px;
      }

      .uploaded-reels-table td,
      .uploaded-reels-table td:before {
        font-size: 12px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Upload Form -->
    <div class="form-container">
      <h2><i class="fas fa-cloud-upload-alt"></i>Upload Reel Video</h2>
      <form>
        <div class="form-group">
          <label for="reel_title"><i class="fas fa-heading"></i>Reel Title</label>
          <input type="text" id="reel_title" placeholder="Enter reel title" required>
        </div>
        <div class="form-group">
          <label for="reel_video"><i class="fas fa-video"></i>Reel Video</label>
          <input type="file" id="reel_video" accept="video/*" required>
        </div>
        <div class="form-group">
          <button type="button" onclick="addReel()">
            <i class="fas fa-upload"></i>Upload
          </button>
        </div>
      </form>
    </div>

    <!-- Uploaded Reels Table -->
    <div class="table-container">
      <table class="uploaded-reels-table" id="reelsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Video Path</th>
            <th>Preview</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="5" style="text-align:center;padding:20px;">No reels uploaded yet.</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    let reelId = 1;

    function addReel() {
      const title = document.getElementById("reel_title").value;
      const videoInput = document.getElementById("reel_video");
      const file = videoInput.files[0];

      if (!title || !file) {
        alert("Please enter a title and select a video.");
        return;
      }

      const table = document.getElementById("reelsTable").querySelector("tbody");
      if (table.rows.length === 1 && table.rows[0].cells[0].colSpan === 5) {
        table.innerHTML = ""; // remove "no reels" row
      }

      const row = table.insertRow();
      row.innerHTML = `
        <td data-label="ID">${reelId++}</td>
        <td data-label="Title">${title}</td>
        <td data-label="Video Path">${file.name}</td>
        <td data-label="Preview">
          <video class="video-preview" controls>
            <source src="${URL.createObjectURL(file)}" type="${file.type}">
            Your browser does not support the video tag.
          </video>
        </td>
        <td data-label="Actions">
          <button class="delete-btn" onclick="deleteReel(this)">
            <i class="fas fa-trash"></i>Delete
          </button>
        </td>
      `;

      // reset form
      document.getElementById("reel_title").value = "";
      videoInput.value = "";
    }

    function deleteReel(btn) {
      const row = btn.closest("tr");
      row.remove();
      const table = document.getElementById("reelsTable").querySelector("tbody");
      if (table.rows.length === 0) {
        table.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:20px;">No reels uploaded yet.</td></tr>`;
      }
    }
  </script>
</body>
</html>
