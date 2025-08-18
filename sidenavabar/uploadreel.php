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
      --success-color: #38a169;
      --error-color: #e53e3e;
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

    .form-group button:hover:not(:disabled) {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-group button:disabled {
      background-color: #a0aec0;
      cursor: not-allowed;
      transform: none;
    }

    .form-group button i {
      margin-right: 10px;
    }

    /* Alert styles */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      display: none;
      position: relative;
    }

    .alert-success {
      background-color: #c6f6d5;
      color: #2f855a;
      border: 1px solid #9ae6b4;
    }

    .alert-error {
      background-color: #fed7d7;
      color: #c53030;
      border: 1px solid #feb2b2;
    }

    /* Loading spinner */
    .loading {
      display: none;
      text-align: center;
      padding: 20px;
      color: var(--primary-color);
    }

    .loading i {
      font-size: 2em;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Uploaded Reels Table Styles */
    .table-container {
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }

    .table-header {
      padding: 20px;
      border-bottom: 1px solid #e2e8f0;
    }

    .table-header h3 {
      color: var(--secondary-color);
      display: flex;
      align-items: center;
    }

    .table-header h3 i {
      margin-right: 10px;
      color: var(--primary-color);
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
      max-height: 150px;
      border-radius: 8px;
    }

    .delete-btn {
      background-color: var(--error-color);
      color: var(--white);
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .delete-btn:hover:not(:disabled) {
      background-color: #c53030;
      transform: translateY(-1px);
    }

    .delete-btn:disabled {
      background-color: #a0aec0;
      cursor: not-allowed;
      transform: none;
    }

    .delete-btn i {
      margin-right: 5px;
    }

    .no-data {
      text-align: center;
      padding: 40px;
      color: #718096;
    }

    .no-data i {
      font-size: 3em;
      margin-bottom: 15px;
      opacity: 0.5;
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

      .uploaded-reels-table thead {
        display: none;
      }

      .uploaded-reels-table tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background: var(--white);
      }

      .uploaded-reels-table td {
        display: block;
        text-align: right;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 15px;
        position: relative;
        min-height: 40px;
      }

      .uploaded-reels-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        top: 10px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
        color: var(--secondary-color);
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
  </style>
</head>
<body>
  <?php
    // Uncomment the line below if you have a side navigation
    // include './sidenavbar.php';
  ?>
  
  <div class="container">
    <!-- Upload Form -->
    <div class="form-container">
      <h2><i class="fas fa-cloud-upload-alt"></i>Upload Reel Video</h2>
      
      <div id="alert" class="alert"></div>
      
      <form id="uploadForm" enctype="multipart/form-data">
        <div class="form-group">
          <label for="reel_title"><i class="fas fa-heading"></i> Reel Title</label>
          <input type="text" id="reel_title" name="reel_title" placeholder="Enter reel title" required>
        </div>
        <div class="form-group">
          <label for="reel_video"><i class="fas fa-video"></i> Reel Video</label>
          <input type="file" id="reel_video" name="reel_video" accept="video/*" required>
        </div>
        <div class="form-group">
          <button type="submit" id="uploadBtn">
            <i class="fas fa-upload"></i>Upload
          </button>
        </div>
      </form>
    </div>

    <!-- Uploaded Reels Table -->
    <div class="table-container">
      <div class="table-header">
        <h3><i class="fas fa-film"></i>Uploaded Reels</h3>
      </div>
      
      <div id="loadingReels" class="loading">
        <i class="fas fa-spinner"></i>
        <p>Loading reels...</p>
      </div>
      
      <table class="uploaded-reels-table" id="reelsTable" style="display: none;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Preview</th>
            <th>Upload Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      
      <div id="noReelsMessage" class="no-data" style="display: none;">
        <i class="fas fa-film"></i>
        <p>No reels uploaded yet. Upload your first reel above!</p>
      </div>
    </div>
  </div>

  <script>
    // Load reels on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadReels();
    });

    // Upload form handler
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      const title = document.getElementById('reel_title').value.trim();
      const videoFile = document.getElementById('reel_video').files[0];
      
      if (!title || !videoFile) {
        showAlert('Please enter a title and select a video.', 'error');
        return;
      }
      
      // Check file size (limit to 100MB)
      if (videoFile.size > 100 * 1024 * 1024) {
        showAlert('Video file is too large. Maximum size is 100MB.', 'error');
        return;
      }
      
      formData.append('reel_title', title);
      formData.append('reel_video', videoFile);
      
      // Disable submit button
      const uploadBtn = document.getElementById('uploadBtn');
      uploadBtn.disabled = true;
      uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Uploading...';
      
      fetch('reelupload.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert(data.message, 'success');
          document.getElementById('uploadForm').reset();
          loadReels(); // Reload the reels table
        } else {
          showAlert(data.message, 'error');
        }
      })
      .catch(error => {
        showAlert('Upload failed: ' + error.message, 'error');
      })
      .finally(() => {
        // Re-enable submit button
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i>Upload';
      });
    });

    // Load reels from database
    function loadReels() {
      const loadingDiv = document.getElementById('loadingReels');
      const tableDiv = document.getElementById('reelsTable');
      const noReelsDiv = document.getElementById('noReelsMessage');
      
      loadingDiv.style.display = 'block';
      tableDiv.style.display = 'none';
      noReelsDiv.style.display = 'none';
      
      fetch('fetch_reels.php')
        .then(response => response.json())
        .then(data => {
          loadingDiv.style.display = 'none';
          
          if (data.success && data.reels.length > 0) {
            populateReelsTable(data.reels);
            tableDiv.style.display = 'table';
          } else {
            noReelsDiv.style.display = 'block';
          }
        })
        .catch(error => {
          loadingDiv.style.display = 'none';
          showAlert('Failed to load reels: ' + error.message, 'error');
          noReelsDiv.style.display = 'block';
        });
    }

    // Populate reels table
    function populateReelsTable(reels) {
      const tbody = document.querySelector('#reelsTable tbody');
      tbody.innerHTML = '';
      
      reels.forEach(reel => {
        const row = tbody.insertRow();
        const uploadDate = new Date(reel.created_at).toLocaleDateString();
        
        row.innerHTML = `
          <td data-label="ID">${reel.id}</td>
          <td data-label="Title">${escapeHtml(reel.title)}</td>
          <td data-label="Preview">
            <video class="video-preview" controls preload="metadata">
              <source src="${reel.video_url}" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </td>
          <td data-label="Upload Date">${uploadDate}</td>
          <td data-label="Actions">
            <button class="delete-btn" onclick="deleteReel(${reel.id}, this)">
              <i class="fas fa-trash"></i>Delete
            </button>
          </td>
        `;
      });
    }

    // Delete reel function
    function deleteReel(reelId, btnElement) {
      if (!confirm('Are you sure you want to delete this reel? This action cannot be undone.')) {
        return;
      }
      
      // Disable button
      btnElement.disabled = true;
      btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Deleting...';
      
      fetch('delete_reel.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: reelId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert(data.message, 'success');
          loadReels(); // Reload the table
        } else {
          showAlert(data.message, 'error');
          // Re-enable button on error
          btnElement.disabled = false;
          btnElement.innerHTML = '<i class="fas fa-trash"></i>Delete';
        }
      })
      .catch(error => {
        showAlert('Delete failed: ' + error.message, 'error');
        // Re-enable button on error
        btnElement.disabled = false;
        btnElement.innerHTML = '<i class="fas fa-trash"></i>Delete';
      });
    }

    // Show alert function
    function showAlert(message, type) {
      const alert = document.getElementById('alert');
      alert.className = `alert alert-${type}`;
      alert.textContent = message;
      alert.style.display = 'block';
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        alert.style.display = 'none';
      }, 5000);
      
      // Scroll to alert
      alert.scrollIntoView({ behavior: 'smooth' });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
  </script>
</body>
</html>