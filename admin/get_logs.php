<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, COALESCE(username, 'Unknown') AS username, action, details, created_at 
            AS timestamp FROM activity_log ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If logs are empty, return a message
    if (empty($logs)) {
        echo json_encode(["message" => "No logs found."]);
        exit();
    }

    echo json_encode($logs);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database query failed: " . $e->getMessage()]);
    exit();
}
?>
