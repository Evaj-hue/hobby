<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Get last seen timestamp from request
$lastSeen = isset($_GET['last_seen']) ? intval($_GET['last_seen']) : 0;

// Check if table exists
$tableCheckQuery = "SHOW TABLES LIKE 'activity_logs'";
$tableCheckResult = $conn->query($tableCheckQuery);

if ($tableCheckResult->num_rows == 0) {
    echo json_encode(['message' => 'No notifications available']);
    exit;
}

// Query to get unread notifications
$query = "SELECT id, username, action, details, link, 
          UNIX_TIMESTAMP(timestamp) as timestamp 
          FROM activity_logs 
          WHERE UNIX_TIMESTAMP(timestamp) > ? 
          ORDER BY timestamp DESC 
          LIMIT 10";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lastSeen);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['message' => 'No new notifications']);
} else {
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    echo json_encode($notifications);
}

$stmt->close();
$conn->close();
?>