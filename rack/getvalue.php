<?php
// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Static cache duration
$cacheTime = 2; // seconds between database reads
$cachePath = __DIR__ . '/cache/value_cache.json';
$cacheDir = __DIR__ . '/cache';

// Create cache directory if it doesn't exist
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Check if cache exists and is fresh
$useCachedData = false;
$cachedData = null;

if (file_exists($cachePath)) {
    $fileAge = time() - filemtime($cachePath);
    if ($fileAge < $cacheTime) {
        $cachedContent = file_get_contents($cachePath);
        $cachedData = json_decode($cachedContent, true);
        if ($cachedData !== null) {
            $useCachedData = true;
        }
    }
}

if ($useCachedData) {
    // Use cached data
    $response = $cachedData;
    
    // Add debug info to response
    $response['debug']['from_cache'] = true;
    $response['debug']['cache_age'] = $fileAge;
} else {
    // Database access - only when necessary
    include "config.php";
    
    $response = [
        'weight' => 0,
        'item_count' => 0,
        'specified_items' => 0,
        'unrecognized' => 0,
        'timestamp' => date('Y-m-d H:i:s'),
        'success' => true
    ];
    
    try {
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
        
        // Get current weight
        $sql = "SELECT weight, created_at FROM weight ORDER BY created_at DESC LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $currentWeight = floatval($row['weight']);
            $response['weight'] = $currentWeight;
            $response['timestamp'] = $row['created_at'];
            
            // Continue with calculation only for meaningful weight
            if ($currentWeight > 0.001) {
                // Is the weight recognized as a valid multiple of item weight?
                $recognized = false;
                $matchedItems = 0;
                
                // Try to find a match from 1 to 20 items
                for ($i = 1; $i <= 20; $i++) {
                    $expectedWeight = $i * $itemWeight;
                    $tolerance = ($expectedWeight * $tolerancePercent / 100);
                    
                    if (abs($currentWeight - $expectedWeight) <= $tolerance) {
                        $matchedItems = $i;
                        $recognized = true;
                        break;
                    }
                }
                
                // If not recognized, estimate the count
                if (!$recognized) {
                    $matchedItems = round($currentWeight / $itemWeight);
                    
                    // Add extra information about the unrecognized weight
                    $response['unrecognized_details'] = [
                        'actual_weight' => $currentWeight,
                        'closest_match_items' => $matchedItems,
                        'expected_weight' => $matchedItems * $itemWeight,
                        'difference' => $currentWeight - ($matchedItems * $itemWeight)
                    ];
                }
                
                // Set response values for item count and recognition
                $response['item_count'] = $matchedItems;
                $response['unrecognized'] = (!$recognized && $currentWeight > 0.01) ? 1 : 0;
                
                // Calculate how many whole items can be extracted
                $specifiedItems = 0;
                $remainingWeight = $currentWeight;
                $tolerance = $itemWeight * $tolerancePercent / 100;
                
                // Try to extract complete items from the weight
                while ($remainingWeight >= ($itemWeight - $tolerance)) {
                    if (abs($remainingWeight - $itemWeight) <= $tolerance) {
                        // Last bit matches an item within tolerance
                        $specifiedItems++;
                        break;
                    } elseif ($remainingWeight >= $itemWeight) {
                        // Extract one item
                        $specifiedItems++;
                        $remainingWeight -= $itemWeight;
                    } else {
                        break;
                    }
                }
                
                $response['specified_items'] = $specifiedItems;
            }
        }
        
        // Add debug info
        $response['debug'] = [
            'item_weight' => $itemWeight,
            'tolerance_percent' => $tolerancePercent,
            'server_time' => date('Y-m-d H:i:s'),
            'from_cache' => false
        ];
        
        mysqli_close($conn);
        
        // Cache the result
        file_put_contents($cachePath, json_encode($response));
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
    }
}

// Output JSON with proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
echo json_encode($response);
?>
