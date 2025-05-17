<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Select all tags from users, no WHERE clause
$sql = "SELECT rfid_tag FROM users";
$result = $conn->query($sql);

$tags = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tags[] = $row['rfid_tag'];
    }
}

echo json_encode($tags);

$conn->close();
?>
