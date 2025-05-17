<?php
session_start();
include "../includes/db.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Get products with stock below threshold
    $sql = "SELECT id, product_name, units_in_stock, stock_threshold, max_stock, 'product' as type 
            FROM products 
            WHERE units_in_stock <= stock_threshold 
            AND stock_threshold IS NOT NULL";
    $stmt = $conn->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get merch products with stock below threshold
    $sql = "SELECT product_id as id, product_name, stock_quantity as units_in_stock, stock_threshold, max_stock, 'merch' as type 
            FROM merch_products 
            WHERE stock_quantity <= stock_threshold 
            AND stock_threshold IS NOT NULL";
    $stmt = $conn->query($sql);
    $merch = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine both results
    $lowStockItems = array_merge($products, $merch);

    // Calculate reorder quantities and add to the data
    foreach ($lowStockItems as &$item) {
        // Calculate optimal reorder amount to reach max stock
        if (!empty($item['max_stock'])) {
            $item['reorder_amount'] = max(0, $item['max_stock'] - $item['units_in_stock']);
        } else {
            // If max_stock is not set, suggest ordering up to double the threshold
            $item['reorder_amount'] = max(0, $item['stock_threshold'] * 2 - $item['units_in_stock']);
        }
    }

    // Sort by stock level (ascending)
    usort($lowStockItems, function($a, $b) {
        return $a['units_in_stock'] - $b['units_in_stock'];
    });

    if (count($lowStockItems) === 0) {
        echo json_encode(['message' => 'No low stock items found.']);
    } else {
        echo json_encode($lowStockItems);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
