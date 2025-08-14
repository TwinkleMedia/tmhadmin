<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");


// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "twinkleadmin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$sql = "SELECT id, logo_url AS image FROM client_logos ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

$logos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logos[] = $row;
    }
}

echo json_encode($logos);
$conn->close();
?>
