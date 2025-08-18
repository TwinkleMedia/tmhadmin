<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'u694280384_twinkleadmin');
define('DB_PASSWORD', 'Deep@0118');
define('DB_NAME', 'u694280384_twinkleadmin');

// Cloudinary configuration
define('CLOUDINARY_CLOUD_NAME', 'dh9dpvul4');
define('CLOUDINARY_API_KEY', '913163688842134');
define('CLOUDINARY_API_SECRET', 'FR5RjEj7it70xfBMnT53mgW-uds');
// Database connection
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Create reels table if it doesn't exist
function createReelsTable() {
    $conn = getDbConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS reels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        video_url VARCHAR(500) NOT NULL,
        cloudinary_public_id VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->query($sql);
    $conn->close();
}

// Initialize database
createReelsTable();
?>