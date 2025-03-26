<?php
include '../includes/db.php';

header('Content-Type: application/json'); // Ensure JSON response

try {
    $sql = "SELECT category, product_name, units_in_stock FROM products ORDER BY category";
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
        $groupedData[$category]["stocks"][] = (int)$row['units_in_stock']; // Ensure numeric format
    }

    echo json_encode($groupedData);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
