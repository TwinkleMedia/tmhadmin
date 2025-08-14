<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twinkleadmin";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Verify table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'creative_form_work'");
    if ($tableCheck->num_rows === 0) {
        throw new Exception("Table 'creative_form_work' does not exist");
    }

    $type = $_GET['type'] ?? '';
    $category = $_GET['category'] ?? '';

    if ($type === "categories") {
        $sql = "SELECT DISTINCT category FROM creative_form_work ORDER BY category ASC";
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
        
        echo json_encode([
            "success" => true,
            "data" => $categories,
            "count" => count($categories)
        ]);

    } elseif ($type === "images") {
        if ($category === "all") {
            $sql = "SELECT id, title, category, image_url FROM creative_form_work ORDER BY id DESC";
            $result = $conn->query($sql);
        } else {
            $sql = "SELECT id, title, category, image_url FROM creative_form_work WHERE category = ? ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        
        echo json_encode([
            "success" => true,
            "data" => $images,
            "count" => count($images)
        ]);

    } else {
        throw new Exception("Invalid request type");
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}