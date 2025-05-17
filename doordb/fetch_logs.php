<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$sql = "SELECT access_logs.id, access_logs.rfid_tag, access_logs.status, access_logs.timestamp,
               users.username, users.role
        FROM access_logs
        LEFT JOIN users ON access_logs.user_id = users.id
        ORDER BY access_logs.timestamp DESC";

$result = $conn->query($sql);
$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}
echo json_encode($logs);
$conn->close();
?>