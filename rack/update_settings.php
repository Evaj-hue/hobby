<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemWeight = isset($_POST['itemWeight']) ? floatval($_POST['itemWeight']) : 0.5;
    $tolerance = isset($_POST['tolerance']) ? floatval($_POST['tolerance']) : 2.0; // Now a percentage

    // Validate inputs
    if ($itemWeight <= 0) {
        echo json_encode(['success' => false, 'message' => 'Item weight must be greater than 0']);
        exit;
    }
    
    if ($tolerance < 0.1 || $tolerance > 10) {
        echo json_encode(['success' => false, 'message' => 'Tolerance must be between 0.1% and 10%']);
        exit;
    }

    // Update the latest row in config (assuming only one row)
    $sql = "UPDATE config SET item_weight=?, tolerance=?, updated_at=NOW() ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dd", $itemWeight, $tolerance);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update settings: ' . $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>