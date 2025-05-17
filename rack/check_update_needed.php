<?php
include "config.php";

// Get current weight from Arduino
$currentWeight = isset($_GET['weight']) ? floatval($_GET['weight']) : 0;

// Get last recorded weight from database
$sql = "SELECT weight FROM weight ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

$lastWeight = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $lastWeight = floatval($row['weight']);
}

// Get configuration for sensitivity
$configQuery = "SELECT item_weight, tolerance FROM config ORDER BY id DESC LIMIT 1";
$configResult = mysqli_query($conn, $configQuery);
$itemWeight = 0.5;  // Default
$tolerancePercent = 2.0;  // Default

if ($configResult && mysqli_num_rows($configResult) > 0) {
    $config = mysqli_fetch_assoc($configResult);
    $itemWeight = floatval($config['item_weight']);
    $tolerancePercent = floatval($config['tolerance']);
}

// Calculate minimum significant change (5% of item weight or at least 0.01kg)
$minSignificantChange = max(0.01, $itemWeight * 0.05);

// Determine if update is needed
$weightDifference = abs($currentWeight - $lastWeight);
$updateNeeded = ($weightDifference >= $minSignificantChange);

// Return response
$response = [
    'update_needed' => $updateNeeded ? 1 : 0,
    'last_weight' => $lastWeight,
    'weight_difference' => $weightDifference,
    'min_change_threshold' => $minSignificantChange
];

header('Content-Type: application/json');
echo json_encode($response);
mysqli_close($conn);
?>
