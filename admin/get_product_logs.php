<?php
include '../includes/db.php';

header('Content-Type: application/json');

try {
    $sql = "
        SELECT 
            p.category, 
            p.product_name, 
            p.units_in_stock AS volume
        FROM products p
        ORDER BY p.category
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $groupedData = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category = $row['category'];
        if (!isset($groupedData[$category])) {
            $groupedData[$category] = [
                "products" => [],
                "stocks" => []
            ];
        }
        $groupedData[$category]["products"][] = $row['product_name'];
        $groupedData[$category]["stocks"][] = (int)$row['volume'];
    }

    echo json_encode($groupedData);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>