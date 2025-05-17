<?php
include "config.php";

// Improved query to ensure we get the most recent warnings first
$sql = "SELECT id, weight, message, time, date, created_at FROM weight_warnings 
        WHERE 1=1 
        ORDER BY created_at DESC 
        LIMIT 50";

$res = mysqli_query($conn, $sql);

// Debug output
if (!$res) {
    echo "<tr><td colspan='5'>Error: " . mysqli_error($conn) . "</td></tr>";
    exit;
}

if (mysqli_num_rows($res) == 0) {
    echo "<tr><td colspan='5' class='text-center'>No unrecognized weight warnings found</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($res)) {
        // Highlight the weight value in the message
        $message = $row['message'];
        
        // If the message contains "Unrecognized weight detected:", highlight that part
        if (strpos($message, "Unrecognized weight detected:") !== false) {
            // Extract the weight value from the message
            preg_match('/Unrecognized weight detected: ([0-9.]+)kg/', $message, $matches);
            if (isset($matches[1])) {
                $extractedWeight = $matches[1];
                // Replace with highlighted version
                $message = str_replace(
                    "Unrecognized weight detected: {$extractedWeight}kg",
                    "Unrecognized weight detected: <strong class='text-danger'>{$extractedWeight}kg</strong>",
                    $message
                );
            }
        }
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td class='font-weight-bold'>" . number_format($row['weight'], 3) . "</td>";
        echo "<td>" . $message . "</td>";
        echo "<td>" . htmlspecialchars($row['time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "</tr>";
    }
}
?>