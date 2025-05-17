<?php
include "config.php";

// Get configuration
$configQuery = "SELECT item_weight, tolerance FROM config ORDER BY id DESC LIMIT 1";
$configResult = mysqli_query($conn, $configQuery);

$itemWeight = 0.5;  // Default
$tolerancePercent = 2.0;  // Default

if ($configResult && mysqli_num_rows($configResult) > 0) {
    $config = mysqli_fetch_assoc($configResult);
    $itemWeight = floatval($config['item_weight']);
    $tolerancePercent = floatval($config['tolerance']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weight Debug Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .recognized { background-color: #d1e7dd; }
        .unrecognized { background-color: #f8d7da; }
        .weight-value { font-weight: bold; }
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .info-box { 
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Weight Debug Information</h1>
        
        <div class="info-box">
            <h3>Current Configuration</h3>
            <p><strong>Item Weight:</strong> <?php echo $itemWeight; ?> kg</p>
            <p><strong>Tolerance:</strong> <?php echo $tolerancePercent; ?>%</p>
            <p><strong>Valid Weight Range for 1 item:</strong> 
               <?php 
               $tolerance = $itemWeight * $tolerancePercent / 100;
               echo ($itemWeight - $tolerance) . " - " . ($itemWeight + $tolerance) . " kg"; 
               ?>
            </p>
        </div>
        
        <h2>All Recorded Weights</h2>
        <p>This table shows all recorded weights and whether they match a recognized item count.</p>
        
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Weight (kg)</th>
                    <th>Status</th>
                    <th>Closest Item Count</th>
                    <th>Expected Weight</th>
                    <th>Difference</th>
                    <th>Within Tolerance?</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT weight, created_at FROM weight ORDER BY created_at DESC LIMIT 100";
                $result = mysqli_query($conn, $sql);
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $currentWeight = floatval($row['weight']);
                    $recognized = false;
                    $matchedItems = 0;
                    
                    // Try to find a match
                    for ($i = 1; $i <= 20; $i++) {
                        $expectedWeight = $i * $itemWeight;
                        $tolerance = $expectedWeight * $tolerancePercent / 100;
                        
                        if (abs($currentWeight - $expectedWeight) <= $tolerance) {
                            $matchedItems = $i;
                            $recognized = true;
                            break;
                        }
                    }
                    
                    // If not recognized, find closest match
                    if (!$recognized) {
                        $matchedItems = round($currentWeight / $itemWeight);
                    }
                    
                    $expectedWeight = $matchedItems * $itemWeight;
                    $difference = $currentWeight - $expectedWeight;
                    $withinTolerance = abs($difference) <= ($expectedWeight * $tolerancePercent / 100);
                    
                    $rowClass = $recognized ? 'recognized' : 'unrecognized';
                    
                    echo "<tr class='$rowClass'>";
                    echo "<td class='weight-value'>" . number_format($currentWeight, 3) . "</td>";
                    echo "<td>" . ($recognized ? "Recognized" : "Unrecognized") . "</td>";
                    echo "<td>" . $matchedItems . " items</td>";
                    echo "<td>" . number_format($expectedWeight, 3) . " kg</td>";
                    echo "<td>" . number_format($difference, 3) . " kg</td>";
                    echo "<td>" . ($withinTolerance ? "Yes" : "No") . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
