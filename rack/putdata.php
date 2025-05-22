<?php
// filepath: c:\xampp\htdocs\idealcozydesign\rack\putdata.php

include "../includes/db.php";

// Get input parameters
$weight = isset($_GET['weight']) ? floatval($_GET['weight']) : 0.0;
$itemCount = isset($_GET['item_count']) ? intval($_GET['item_count']) : 0;
$rackId = isset($_GET['rack_id']) ? intval($_GET['rack_id']) : 0;
$productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

// For backward compatibility, migrate the old data to weight_history
date_default_timezone_set("Asia/Manila");
$timestamp = date('Y-m-d H:i:s');
$time = date('H:i:s');
$date = date('Y-m-d');

// Record to the old tables for backwards compatibility
try {
    // First check if these tables exist
    $checkOldTableSql = "SHOW TABLES LIKE 'weight_changes'";
    $oldTableResult = $conn->query($checkOldTableSql);
    $hasOldTable = $oldTableResult && $oldTableResult->rowCount() > 0;
    
    if ($hasOldTable) {
        // Check if unrecognized column exists in weight_changes
        $checkColumnSql = "SHOW COLUMNS FROM weight_changes LIKE 'unrecognized'";
        $columnResult = $conn->query($checkColumnSql);
        $hasUnrecognizedColumn = $columnResult && $columnResult->rowCount() > 0;
        
        // Insert into weight_changes with appropriate columns
        if ($hasUnrecognizedColumn) {
            $oldInsertSql = "INSERT INTO weight_changes (weight, time, date, item_count, operation, unrecognized) 
                             VALUES (:weight, :time, :date, :item_count, 'measurement', 0)";
        } else {
            $oldInsertSql = "INSERT INTO weight_changes (weight, time, date, item_count, operation) 
                             VALUES (:weight, :time, :date, :item_count, 'measurement')";
        }
        
        $oldStmt = $conn->prepare($oldInsertSql);
        $oldStmt->bindParam(':weight', $weight, PDO::PARAM_STR);
        $oldStmt->bindParam(':time', $time, PDO::PARAM_STR);
        $oldStmt->bindParam(':date', $date, PDO::PARAM_STR);
        $oldStmt->bindParam(':item_count', $itemCount, PDO::PARAM_INT);
        $oldStmt->execute();
    }
} catch (Exception $e) {
    // Log the error but continue - don't stop execution for backwards compatibility issues
    error_log("Error with legacy tables: " . $e->getMessage());
}

// Record the weight measurement in our new system
try {
    // Insert into weight_history
    $insertWeight = "INSERT INTO weight_history (rack_id, product_id, weight, item_count, created_at) 
                    VALUES (:rack_id, :product_id, :weight, :item_count, :timestamp)";
    $insertStmt = $conn->prepare($insertWeight);
    $insertStmt->bindParam(':rack_id', $rackId, PDO::PARAM_INT);
    $insertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $insertStmt->bindParam(':weight', $weight, PDO::PARAM_STR);
    $insertStmt->bindParam(':item_count', $itemCount, PDO::PARAM_INT);
    $insertStmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
    $insertStmt->execute();

    // Check if we need to create alerts based on product information
    if ($productId > 0) {
        // Get product info
        $productSql = "SELECT p.*, rp.item_weight 
                      FROM products p 
                      JOIN rack_products rp ON p.id = rp.product_id 
                      WHERE p.id = :product_id AND rp.rack_id = :rack_id AND rp.active = 1";
        $productStmt = $conn->prepare($productSql);
        $productStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $productStmt->bindParam(':rack_id', $rackId, PDO::PARAM_INT);
        $productStmt->execute();
        
        if ($productInfo = $productStmt->fetch(PDO::FETCH_ASSOC)) {
            $maxStock = $productInfo['max_stock'];
            $stockThreshold = $productInfo['stock_threshold'];
            $productName = $productInfo['product_name'];
            
            // Check for stock level alerts
            $alertType = null;
            $alertMessage = "";
            
            if ($itemCount <= 0) {
                $alertType = 'out_of_stock';
                $alertMessage = "Product is out of stock! Please restock {$productName} in Rack #{$rackId}.";
            } elseif ($itemCount <= $stockThreshold) {
                $alertType = 'low_stock';
                $alertMessage = "Low stock alert! Only {$itemCount} of {$productName} remaining in Rack #{$rackId}.";
            } elseif ($maxStock > 0 && $itemCount > $maxStock) {
                $alertType = 'overstocked';
                $alertMessage = "Overstocked! {$itemCount} of {$productName} detected (max: {$maxStock}) in Rack #{$rackId}.";
            }
            
            // Create alert if needed
            if ($alertType) {
                // Check if a similar unresolved alert exists
                $checkAlertQuery = "SELECT id FROM inventory_alerts 
                                  WHERE rack_id = :rack_id AND product_id = :product_id 
                                  AND alert_type = :alert_type AND status != 'resolved'";
                $checkAlertStmt = $conn->prepare($checkAlertQuery);
                $checkAlertStmt->bindParam(':rack_id', $rackId, PDO::PARAM_INT);
                $checkAlertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $checkAlertStmt->bindParam(':alert_type', $alertType, PDO::PARAM_STR);
                $checkAlertStmt->execute();
                
                // Only create a new alert if one doesn't exist
                if ($checkAlertStmt->rowCount() == 0) {
                    $createAlertQuery = "INSERT INTO inventory_alerts 
                                      (rack_id, product_id, alert_type, message, status, created_at) 
                                      VALUES (:rack_id, :product_id, :alert_type, :message, 'new', :timestamp)";
                    $createAlertStmt = $conn->prepare($createAlertQuery);
                    $createAlertStmt->bindParam(':rack_id', $rackId, PDO::PARAM_INT);
                    $createAlertStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                    $createAlertStmt->bindParam(':alert_type', $alertType, PDO::PARAM_STR);
                    $createAlertStmt->bindParam(':message', $alertMessage, PDO::PARAM_STR);
                    $createAlertStmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
                    $createAlertStmt->execute();
                }
            }
        }
    }

    // Success response
    $response = [
        'success' => true,
        'weight' => $weight,
        'item_count' => $itemCount,
        'timestamp' => $timestamp
    ];
} catch (Exception $e) {
    // Error response
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

// Output response
header('Content-Type: application/json');
echo json_encode($response);
?>