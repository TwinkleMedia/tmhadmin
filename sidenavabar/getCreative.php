<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twinkleadmin";

$response = [];
$statusCode = 200;

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error, 500);
    }

    // Validate required parameters
    if (!isset($_GET['type'])) {
        throw new Exception("Missing required parameter: type", 400);
    }

    $type = $_GET['type'];
    $category = $_GET['category'] ?? 'all'; // Default to 'all' if not specified

    if ($type === "categories") {
        $sql = "SELECT DISTINCT category FROM creative_form_work ORDER BY category ASC";
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error, 500);
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
        
        $response = [
            "success" => true,
            "data" => $categories,
            "count" => count($categories)
        ];

    } elseif ($type === "images") {
        if ($category === 'all') {
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
            throw new Exception("Query failed: " . $conn->error, 500);
        }

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        
        $response = [
            "success" => true,
            "data" => $images,
            "count" => count($images)
        ];

    } else {
        throw new Exception("Invalid request type. Valid types are: 'categories' or 'images'", 400);
    }

} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    $response = [
        "success" => false,
        "error" => $e->getMessage(),
        "type" => $type ?? 'not provided',
        "category" => $category ?? 'not provided'
    ];
} finally {
    http_response_code($statusCode);
    echo json_encode($response);
    if (isset($conn)) {
        $conn->close();
    }
}