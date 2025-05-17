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
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Get POST data
$username = isset($_POST['username']) ? $_POST['username'] : 'System';
$action = isset($_POST['action']) ? $_POST['action'] : 'Info';
$details = isset($_POST['details']) ? $_POST['details'] : '';
$link = isset($_POST['link']) ? $_POST['link'] : '';

// Validate required fields
if (empty($details)) {
    die(json_encode(['success' => false, 'message' => 'Details are required']));
}

// Check if activity_logs table exists
$tableCheckQuery = "SHOW TABLES LIKE 'activity_logs'";
$tableCheckResult = $conn->query($tableCheckQuery);

if ($tableCheckResult->num_rows == 0) {
    // Create the table if it doesn't exist
    $createTableQuery = "CREATE TABLE activity_logs (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL DEFAULT 'System',
        action VARCHAR(255) NOT NULL,
        details TEXT NOT NULL,
        link VARCHAR(255) DEFAULT '',
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTableQuery)) {
        die(json_encode(['success' => false, 'message' => 'Failed to create activity_logs table: ' . $conn->error]));
    }
}

// Check for duplicates in last 5 minutes to prevent spam
$checkDuplicateQuery = "SELECT id FROM activity_logs 
                         WHERE action = ? 
                         AND details = ? 
                         AND timestamp > NOW() - INTERVAL 5 MINUTE";
$checkStmt = $conn->prepare($checkDuplicateQuery);
$checkStmt->bind_param("ss", $action, $details);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Duplicate found, don't insert
    $checkStmt->close();
    echo json_encode(['success' => true, 'message' => 'Duplicate notification skipped']);
    exit;
}
$checkStmt->close();

// Insert into activity_logs
$insertQuery = "INSERT INTO activity_logs (username, action, details, link) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("ssss", $username, $action, $details, $link);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Activity log added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add activity log: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
