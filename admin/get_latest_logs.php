<?php
include '../includes/db.php';
header('Content-Type: application/json');

$sql = "SELECT id, username, action FROM activity_log ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$log = $stmt->fetch(PDO::FETCH_ASSOC);

if ($log) {
    echo json_encode($log);
} else {
    echo json_encode(["error" => "No logs found."]);
}

?>
