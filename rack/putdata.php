<?php
include "config.php";

// Get input
$weight = isset($_GET['weight']) ? floatval($_GET['weight']) : 0.0;
$status = isset($_GET['status']) ? $_GET['status'] : 'unknown';

date_default_timezone_set("Asia/Manila");
$date = date("Y-m-d");
$time = date("H:i:s");

// Fetch dynamic configuration
$configQuery = "SELECT item_weight, tolerance FROM config ORDER BY id DESC LIMIT 1";
$configResult = mysqli_query($conn, $configQuery);
if (!$configResult || mysqli_num_rows($configResult) == 0) {
    $itemWeight = 0.5;
    $tolerancePercent = 2.0; // Default 2% tolerance
} else {
    $config = mysqli_fetch_assoc($configResult);
    $itemWeight = floatval($config['item_weight']);
    $tolerancePercent = floatval($config['tolerance']); // Tolerance stored as percentage
}

// Get last recorded weight
$lastWeightSql = "SELECT weight FROM weight ORDER BY created_at DESC LIMIT 1";
$lastResult = mysqli_query($conn, $lastWeightSql);
$lastWeight = 0.0;
if ($lastResult && mysqli_num_rows($lastResult) > 0) {
    $lastWeight = floatval(mysqli_fetch_assoc($lastResult)['weight']);
}

$weightDiff = $weight - $lastWeight;
$minDetectableWeight = $itemWeight * 0.01; // 1% of item weight as minimum threshold

if (abs($weightDiff) >= $minDetectableWeight) {
    // Calculate item count change
    $itemCountChange = round(abs($weightDiff) / $itemWeight);
    $operation = ($weightDiff > 0) ? "added" : "removed";

    // Check if the change is recognized
    $unrecognized = 1; // Default to unrecognized
    
    // Multiple possible item counts to check
    for ($i = 1; $i <= $itemCountChange + 1; $i++) {
        $expectedChange = $i * $itemWeight;
        $tolerance = ($expectedChange * $tolerancePercent / 100); // Percentage-based tolerance
        
        if (abs(abs($weightDiff) - $expectedChange) <= $tolerance) {
            // This number of items matches the weight change within tolerance
            $itemCountChange = $i;
            $unrecognized = 0;
            break;
        }
    }

    // Insert into weight_changes (history)
    $insertHistory = "INSERT INTO weight_changes (
        weight, status, time, date, item_count, operation, unrecognized
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Check if prepared statement supports the expected parameters
    if ($stmt = $conn->prepare($insertHistory)) {
        $stmt->bind_param("dsssisi", $weight, $status, $time, $date, $itemCountChange, $operation, $unrecognized);
        $stmt->execute();
    } else {
        // Fallback in case the unrecognized column doesn't exist
        $insertHistoryFallback = "INSERT INTO weight_changes (
            weight, status, time, date, item_count, operation
        ) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertHistoryFallback);
        $stmt->bind_param("dsssis", $weight, $status, $time, $date, $itemCountChange, $operation);
        $stmt->execute();
    }

    // Insert new current weight into weight table
    $insertWeight = "INSERT INTO weight (weight, status, time, date)
                     VALUES (?, ?, ?, ?)";
    $stmt2 = $conn->prepare($insertWeight);
    $stmt2->bind_param("dsss", $weight, $status, $time, $date);
    $stmt2->execute();

    // If unrecognized, log a warning
    if ($unrecognized) {
        // Changed: Show total weight instead of just the difference
        $msg = "Unrecognized weight detected: " . number_format($weight, 3) . "kg";
        
        // Add additional context about expected weights
        $closestMatch = round($weight / $itemWeight);
        $expectedWeight = $closestMatch * $itemWeight;
        $diff = $weight - $expectedWeight;
        
        if ($closestMatch > 0) {
            $msg .= " (closest to " . $closestMatch . " item(s): " . number_format($expectedWeight, 3) . "kg, Δ: " . 
                    number_format($diff, 3) . "kg)";
        }
        
        $insertWarning = "INSERT INTO weight_warnings (weight, message, time, date)
                          VALUES (?, ?, ?, ?)";
        $stmt3 = $conn->prepare($insertWarning);
        $stmt3->bind_param("dsss", $weight, $msg, $time, $date);
        $stmt3->execute();
        echo "Unrecognized weight detected: " . number_format($weight, 3) . "kg";
    } else {
        echo "Change recorded: $itemCountChange item(s) $operation";
    }
} else {
    echo "No significant weight change detected.";
}

mysqli_close($conn);
?>