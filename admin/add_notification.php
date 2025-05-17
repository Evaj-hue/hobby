<?php
/**
 * Add Notification API
 * 
 * This file receives notification data and adds it to the activity_logs table
 * for display in the navbar notification system.
 */

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$database = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => "Connection failed: " . $conn->connect_error]));
}

// Get notification data
$type = isset($_POST['type']) ? $_POST['type'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';
$link = isset($_POST['link']) ? $_POST['link'] : '';

// Validate data
if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message is required']);
    exit;
}

// Format type for activity logs
$action = 'Rack Alert';
if (strpos($type, 'rack_warning') !== false) {
    $action = 'Rack Warning';
} else if (strpos($type, 'rack_alert') !== false) {
    $action = 'Rack Alert';
} else if (strpos($type, 'rack_info') !== false) {
    $action = 'Rack Info';
} else if (strpos($type, 'rack_low_stock') !== false) {
    $action = 'Rack Low Stock';
}

// Insert into activity_logs to integrate with navbar notifications
$sql = "INSERT INTO activity_logs (username, action, details, link) 
        VALUES ('System', ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("sss", $action, $message, $link);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Notification added successfully']);
} else {
    echo json_encode(['success' => false, 'error' => "Execute failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
