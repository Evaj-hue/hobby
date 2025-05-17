<?php
include "config.php";

// Calculate net item count: added - removed
// Only count recognized changes (where unrecognized = 0)
$sql = "SELECT 
          SUM(CASE WHEN operation = 'added' AND unrecognized = 0 THEN item_count ELSE 0 END) -
          SUM(CASE WHEN operation = 'removed' AND unrecognized = 0 THEN item_count ELSE 0 END) 
          AS total_items 
        FROM weight_changes";

$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    // Get total_items, defaulting to 0 if NULL (empty database)
    $totalItems = $row['total_items'] !== NULL ? max(0, $row['total_items']) : 0;
    
    // Format the response
    echo "Total Item Count: " . $totalItems;
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>