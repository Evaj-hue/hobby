<?php
// Database connection
include "config.php";

// Start HTML output for debugging
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Structure Update</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        .code { font-family: monospace; background: #f4f4f4; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Database Structure Update</h1>";

// Check if unrecognized column exists in weight_changes table
$checkColumnSql = "SHOW COLUMNS FROM weight_changes LIKE 'unrecognized'";
$columnResult = mysqli_query($conn, $checkColumnSql);

if (!$columnResult) {
    echo "<p class='error'>Error checking for column: " . mysqli_error($conn) . "</p>";
    exit;
}

if (mysqli_num_rows($columnResult) > 0) {
    echo "<p class='success'>The 'unrecognized' column already exists in the weight_changes table.</p>";
} else {
    echo "<p class='warning'>The 'unrecognized' column does not exist in the weight_changes table. Attempting to add it...</p>";
    
    // Add the column
    $addColumnSql = "ALTER TABLE weight_changes ADD COLUMN unrecognized TINYINT(1) NOT NULL DEFAULT 0";
    
    if (mysqli_query($conn, $addColumnSql)) {
        echo "<p class='success'>Successfully added 'unrecognized' column to weight_changes table.</p>";
        
        // Now update the existing records based on some logic
        echo "<p>Updating existing records...</p>";
        
        // Logic: If the weight does not match any of the standard weight multiples (within tolerance),
        // mark it as unrecognized
        $updateSql = "
            UPDATE weight_changes SET unrecognized = 1 
            WHERE id IN (
                SELECT * FROM (
                    SELECT wc.id 
                    FROM weight_changes wc, config c
                    WHERE (
                        -- Check if weight is not a multiple of item_weight within tolerance
                        (wc.weight % c.item_weight) > (c.item_weight * c.tolerance / 100)
                        AND
                        (c.item_weight - (wc.weight % c.item_weight)) > (c.item_weight * c.tolerance / 100)
                    )
                    ORDER BY wc.id DESC
                ) AS temp
            )
        ";
        
        // For safety, we'll just update recent records
        $simplifiedUpdateSql = "
            UPDATE weight_changes wc
            JOIN (
                SELECT id FROM weight_changes 
                ORDER BY id DESC LIMIT 1000
            ) recent ON wc.id = recent.id
            SET wc.unrecognized = 1
            WHERE wc.id IN (
                SELECT id FROM weight_warnings
            )
        ";
        
        if (mysqli_query($conn, $simplifiedUpdateSql)) {
            echo "<p class='success'>Successfully updated existing records based on warnings.</p>";
            
            // Count updated records
            $countSql = "SELECT COUNT(*) as count FROM weight_changes WHERE unrecognized = 1";
            $countResult = mysqli_query($conn, $countSql);
            if ($countResult) {
                $countRow = mysqli_fetch_assoc($countResult);
                echo "<p>Total records marked as unrecognized: " . $countRow['count'] . "</p>";
            }
        } else {
            echo "<p class='error'>Error updating existing records: " . mysqli_error($conn) . "</p>";
            echo "<p class='code'>" . $simplifiedUpdateSql . "</p>";
        }
    } else {
        echo "<p class='error'>Error adding column: " . mysqli_error($conn) . "</p>";
    }
}

// Check if created_at column exists
$checkCreatedAtSql = "SHOW COLUMNS FROM weight_changes LIKE 'created_at'";
$createdAtResult = mysqli_query($conn, $checkCreatedAtSql);

if (mysqli_num_rows($createdAtResult) == 0) {
    echo "<p class='warning'>The 'created_at' column does not exist in the weight_changes table. Attempting to add it...</p>";
    
    // Add the column
    $addCreatedAtSql = "ALTER TABLE weight_changes ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    
    if (mysqli_query($conn, $addCreatedAtSql)) {
        echo "<p class='success'>Successfully added 'created_at' column to weight_changes table.</p>";
    } else {
        echo "<p class='error'>Error adding created_at column: " . mysqli_error($conn) . "</p>";
    }
}

// Check if weight_warnings table has created_at column
$checkWarningsCreatedAtSql = "SHOW COLUMNS FROM weight_warnings LIKE 'created_at'";
$warningsCreatedAtResult = mysqli_query($conn, $checkWarningsCreatedAtSql);

if (mysqli_num_rows($warningsCreatedAtResult) == 0) {
    echo "<p class='warning'>The 'created_at' column does not exist in the weight_warnings table. Attempting to add it...</p>";
    
    // Add the column
    $addWarningsCreatedAtSql = "ALTER TABLE weight_warnings ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    
    if (mysqli_query($conn, $addWarningsCreatedAtSql)) {
        echo "<p class='success'>Successfully added 'created_at' column to weight_warnings table.</p>";
    } else {
        echo "<p class='error'>Error adding created_at column to warnings: " . mysqli_error($conn) . "</p>";
    }
}

echo "<p><a href='../admin/activity_logs.php'>Return to Activity Logs</a></p>";

echo "</body></html>";

mysqli_close($conn);
?>
