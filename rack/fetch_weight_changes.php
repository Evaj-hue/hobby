<?php
include "config.php";

// Fetch the latest weight changes, newest first (adjust LIMIT as needed)
$sql = "SELECT id, weight, time, date, item_count, operation, unrecognized 
        FROM weight_changes 
        ORDER BY created_at DESC 
        LIMIT 50";
$res = mysqli_query($conn, $sql);

$counter = 1;

while ($row = mysqli_fetch_assoc($res)) {
    $rowClass = ($row['unrecognized']) ? ' class="table-danger"' : '';
    echo "<tr$rowClass>";
    echo "<td>" . $counter++ . "</td>";
    echo "<td>" . number_format($row['weight'], 3) . "</td>";
    echo "<td>" . htmlspecialchars($row['time']) . "</td>";
    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
    echo "<td>" . htmlspecialchars($row['item_count']) . "</td>";
    echo "<td>" . htmlspecialchars($row['operation']) . "</td>";
    
    // Add status column for unrecognized
    echo "<td>";
    if (isset($row['unrecognized']) && $row['unrecognized'] == 1) {
        echo "<span class='badge bg-danger'>Unrecognized</span>";
    } else {
        echo "<span class='badge bg-success'>Recognized</span>";
    }
    echo "</td>";
    
    echo "</tr>";
}

// If no results, show empty message
if (mysqli_num_rows($res) == 0) {
    echo "<tr><td colspan='7' class='text-center'>No weight changes recorded</td></tr>";
}
?>