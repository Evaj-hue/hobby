<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";

echo "<h1>Database Debug Information</h1>";

// Check 'weight' table - most recent entries
echo "<h2>Most Recent Weight Records</h2>";
$weightQuery = "SELECT id, weight, status, time, date, created_at FROM weight ORDER BY created_at DESC LIMIT 10";
$weightResult = mysqli_query($conn, $weightQuery);

if ($weightResult && mysqli_num_rows($weightResult) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Weight</th><th>Status</th><th>Time</th><th>Date</th><th>Created At</th></tr>";
    
    while ($row = mysqli_fetch_assoc($weightResult)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['weight']} kg</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['time']}</td>";
        echo "<td>{$row['date']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:red'>No weight records found or query error: " . mysqli_error($conn) . "</p>";
}

// Check 'config' table
echo "<h2>Configuration Settings</h2>";
$configQuery = "SELECT id, item_weight, tolerance, updated_at FROM config ORDER BY id DESC LIMIT 5";
$configResult = mysqli_query($conn, $configQuery);

if ($configResult && mysqli_num_rows($configResult) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Item Weight</th><th>Tolerance (%)</th><th>Updated At</th></tr>";
    
    while ($row = mysqli_fetch_assoc($configResult)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['item_weight']} kg</td>";
        echo "<td>{$row['tolerance']}%</td>";
        echo "<td>{$row['updated_at']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:red'>No configuration records found or query error: " . mysqli_error($conn) . "</p>";
}

// Check specified weight items calculation
echo "<h2>Specified Weight Items Calculation Test</h2>";
if ($weightResult && mysqli_num_rows($weightResult) > 0) {
    mysqli_data_seek($weightResult, 0);
    $row = mysqli_fetch_assoc($weightResult);
    $weight = floatval($row['weight']);
    
    if ($configResult && mysqli_num_rows($configResult) > 0) {
        mysqli_data_seek($configResult, 0);
        $config = mysqli_fetch_assoc($configResult);
        $itemWeight = floatval($config['item_weight']);
        $tolerancePercent = floatval($config['tolerance']);
        
        // Test calculation
        $recognized = false;
        $matchedItems = 0;
        
        // Check each potential item count
        for ($i = 1; $i <= 20; $i++) {
            $expectedWeight = $i * $itemWeight;
            $tolerance = ($expectedWeight * $tolerancePercent / 100);
            
            if (abs($weight - $expectedWeight) <= $tolerance) {
                $matchedItems = $i;
                $recognized = true;
                break;
            }
        }
        
        // Calculate specified items (maximum items that can be extracted)
        $specifiedItems = 0;
        $remainingWeight = $weight;
        $tolerance = $itemWeight * $tolerancePercent / 100;
        
        // Extract as many whole items as possible
        echo "<h3>Step-by-step Specified Items Calculation:</h3>";
        echo "<pre>Starting with weight: $weight kg\nItem weight: $itemWeight kg\nTolerance: $tolerancePercent% ($tolerance kg)</pre>";
        
        while ($remainingWeight >= ($itemWeight - $tolerance)) {
            // First check if the remaining weight itself is within tolerance of an item
            if (abs($remainingWeight - $itemWeight) <= $tolerance) {
                $specifiedItems++;
                echo "<pre>  Found final item: remaining $remainingWeight kg ≈ $itemWeight kg (within tolerance)\n  Extracted item #$specifiedItems, remaining weight = 0 kg</pre>";
                $remainingWeight = 0;
                break; // Perfect match for the last item
            }
            
            // Otherwise, if we have at least one item's worth of weight
            if ($remainingWeight >= $itemWeight) {
                $specifiedItems++;
                $remainingWeight -= $itemWeight;
                echo "<pre>  Extracted item #$specifiedItems, remaining weight = $remainingWeight kg</pre>";
            } else {
                echo "<pre>  Remaining weight $remainingWeight kg is less than item weight $itemWeight kg\n  Cannot extract more items</pre>";
                break; // Not enough weight for another item
            }
        }
        
        echo "<p>Current weight: $weight kg</p>";
        echo "<p>Item weight: $itemWeight kg</p>";
        echo "<p>Tolerance: $tolerancePercent% ($tolerance kg)</p>";
        echo "<p>Recognized: " . ($recognized ? "Yes" : "No") . "</p>";
        echo "<p>Matched items: $matchedItems</p>";
        echo "<p>Specified items: $specifiedItems</p>";
    }
}

// Check GetValue API Response
echo "<h2>GetValue API Response</h2>";
echo "<pre>";
$response = file_get_contents('getvalue.php');
echo htmlspecialchars($response);
echo "</pre>";

mysqli_close($conn);
?>
