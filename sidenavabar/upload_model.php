<?php
require './uploadmodel.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 40px;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #34495e;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input[type="file"] {
            padding: 8px 12px;
            border: 2px dashed #e0e6ed;
            background: #f8fafc;
            cursor: pointer;
        }

        input[type="file"]:hover {
            border-color: #667eea;
            background: #f1f5f9;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            padding: 8px 16px;
            font-size: 12px;
            width: auto;
            min-width: 70px;
            text-decoration: none;
            color: white;
        }

        .btn-danger:hover {
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            min-width: 800px;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e0e6ed;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8fafc;
        }

        .media-grid {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            max-width: 200px;
        }

        .media-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .media-item img:hover {
            transform: scale(1.1);
        }

        .video-link, .pdf-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            padding: 4px 12px;
            border: 1px solid #667eea;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 2px;
        }

        .video-link:hover, .pdf-link:hover {
            background: #667eea;
            color: white;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            font-size: 14px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
            font-size: 16px;
        }

        .small-text {
            color: #6c757d;
            font-size: 12px;
            margin-top: 4px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .id-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .model-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .stats-row {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .card {
                padding: 20px;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 2rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px 8px;
            }
        }

        .refresh-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            margin-bottom: 20px;
            width: auto;
            padding: 10px 20px;
            font-size: 14px;
        }

        .refresh-btn:hover {
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎭 Model Management System</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-number"><?php echo count($models); ?></div>
                <div class="stat-label">Total Models</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($models, function($m) { return $m['gender'] === 'Male'; })); ?></div>
                <div class="stat-label">Male</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($models, function($m) { return $m['gender'] === 'Female'; })); ?></div>
                <div class="stat-label">Female</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count(array_filter($models, function($m) { return $m['gender'] === 'Other'; })); ?></div>
                <div class="stat-label">Other</div>
            </div>
        </div>
        
        <div class="grid">
            <div class="card">
                <h2>📤 Upload New Model</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="model_name">Model Name *</label>
                        <input type="text" id="model_name" name="model_name" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">--Select Gender--</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="age">Age *</label>
                        <input type="number" id="age" name="age" min="1" max="100" required>
                    </div>

                    <div class="form-group">
                        <label for="images">Upload Images (Max 10)</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple>
                        <div class="small-text">JPG, PNG, GIF formats accepted</div>
                    </div>

                    <div class="form-group">
                        <label for="videos">Upload Videos (Max 10)</label>
                        <input type="file" id="videos" name="videos[]" accept="video/*" multiple>
                        <div class="small-text">MP4, AVI, MOV formats accepted</div>
                    </div>

                    <div class="form-group">
                        <label for="pdf">Upload PDF</label>
                        <input type="file" id="pdf" name="pdf" accept="application/pdf">
                        <div class="small-text">PDF format only</div>
                    </div>

                    <button type="submit" class="btn">
                        📤 Upload Model
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>📋 Uploaded Models</h2>
                <a href="index.php" class="btn refresh-btn">🔄 Refresh</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Model Details</th>
                            <th>Demographics</th>
                            <th>Images</th>
                            <th>Videos</th>
                            <th>PDF</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($models)): ?>
                            <tr>
                                <td colspan="7" class="no-data">
                                    🎭 No models uploaded yet. Add your first model above!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($models as $model): ?>
                                <tr>
                                    <td>
                                        <span class="id-badge"><?php echo $model['id']; ?></span>
                                    </td>
                                    <td>
                                        <div class="model-name"><?php echo htmlspecialchars($model['model_name']); ?></div>
                                        <div class="small-text">Added: <?php echo date('M d, Y', strtotime($model['created_at'] ?? 'now')); ?></div>
                                    </td>
                                    <td>
                                        <div><strong><?php echo htmlspecialchars($model['gender']); ?></strong></div>
                                        <div class="small-text"><?php echo $model['age']; ?> years old</div>
                                    </td>
                                    <td>
                                        <div class="media-grid">
                                            <?php if (!empty($model['images'])): ?>
                                                <?php foreach ($model['images'] as $img): ?>
                                                    <div class="media-item">
                                                        <a href="<?php echo htmlspecialchars($img); ?>" target="_blank">
                                                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Model Image">
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="small-text">No images</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($model['videos'])): ?>
                                            <?php foreach ($model['videos'] as $video): ?>
                                                <a href="<?php echo htmlspecialchars($video); ?>" target="_blank" class="video-link">
                                                    📹 Video
                                                </a>
                                                <br>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="small-text">No videos</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($model['pdf_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($model['pdf_url']); ?>" target="_blank" class="pdf-link">
                                                📄 View PDF
                                            </a>
                                        <?php else: ?>
                                            <span class="small-text">No PDF</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?delete=<?php echo $model['id']; ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this model? This action cannot be undone.')">
                                            🗑️ Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>