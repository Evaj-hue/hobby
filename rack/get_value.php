<?php
include "config.php";

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

$maxItems = 20;
$minDetectableWeight = $itemWeight * 0.01; // 1% of item weight as minimum threshold

$sql = "SELECT weight FROM weight ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $currentWeight = floatval($row['weight']);

    $recognized = false;
    $matchedItems = 0;

    if ($currentWeight <= $minDetectableWeight) {
        // Weight too small, probably empty
        $recognized = true;
        $matchedItems = 0;
    } else {
        // Try to find the best matching count of items
        for ($i = 1; $i <= $maxItems; $i++) {
            $expected = $i * $itemWeight;
            $tolerance = ($expected * $tolerancePercent / 100); // Calculate tolerance as percentage
            
            if (abs($currentWeight - $expected) <= $tolerance) {
                $recognized = true;
                $matchedItems = $i;
                break;
            }
        }
        
        // If no match found, calculate the closest item count
        if (!$recognized) {
            $matchedItems = round($currentWeight / $itemWeight);
        }
    }

    echo json_encode([
        'weight' => $currentWeight,
        'item_count' => $matchedItems,
        'unrecognized' => (!$recognized && $currentWeight > $minDetectableWeight) ? 1 : 0
    ]);
} else {
    echo json_encode([
        'weight' => 0,
        'item_count' => 0,
        'unrecognized' => 0
    ]);
}

mysqli_close($conn);
?>