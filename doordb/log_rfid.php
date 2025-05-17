<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid_tag = strtoupper($_POST['rfid_tag']); // Ensure uppercase
    $status = $_POST['status'];

    // Fetch user
    $stmt = $conn->prepare("SELECT id FROM users WHERE rfid_tag = ?");
    $stmt->bind_param("s", $rfid_tag);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Log access with user_id
        $stmt = $conn->prepare("INSERT INTO access_logs (rfid_tag, status, timestamp, user_id) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("ssi", $rfid_tag, $status, $user_id);
    } else {
        // Unknown user (optional handling)
        $stmt = $conn->prepare("INSERT INTO access_logs (rfid_tag, status, timestamp) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $rfid_tag, $status);
    }

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
