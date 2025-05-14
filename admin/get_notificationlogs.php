<?php
include '../includes/db.php';
header('Content-Type: application/json');

// Simulate the last seen timestamp (in a real implementation, retrieve this from the database or session)
$lastSeenTimestamp = isset($_GET['last_seen']) ? $_GET['last_seen'] : 0;

try {
    $sql = "
        SELECT id, action, details, UNIX_TIMESTAMP(created_at) AS timestamp
        FROM activity_log
        WHERE UNIX_TIMESTAMP(created_at) > :last_seen
        ORDER BY created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':last_seen', $lastSeenTimestamp, PDO::PARAM_INT);
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($logs) {
        echo json_encode($logs);
    } else {
        echo json_encode(["message" => "No new notifications."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>