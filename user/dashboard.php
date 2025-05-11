<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit();
}

include '../includes/db.php';

// Fetch merch products grouped by category
$sql = "SELECT * FROM merch_products ORDER BY category, product_name";
$result = $conn->query($sql);

$productsByCategory = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $productsByCategory[$row['category']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/idealcozydesign/css/style.css"/>
    <link rel="stylesheet" href="/idealcozydesign/css/cards.css"/>
    <title>User Dashboard</title>
    <style>
        .product-card {
            background-color: #362532;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 15px;
            margin: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .product-card img {
            max-width: 100%;
            border-radius: 5px;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .category-header {
            color: #ED7117;
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include("../partials/user_navbar.php"); ?>
    <?php include("../partials/user_sidebar.php"); ?>

    <div class="dashboard-container">
        <div class="dashboard-content">
            <h1 class="text-center">Merch Products</h1>
            <?php if (!empty($productsByCategory)): ?>
                <?php foreach ($productsByCategory as $category => $products): ?>
                    <div>
                        <h2 class="category-header"><?= htmlspecialchars($category); ?></h2>
                        <div class="d-flex flex-wrap justify-content-center">
                            <?php foreach ($products as $product): ?>
                                <div class="product-card">
                                    <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>">
                                    <h3><?= htmlspecialchars($product['product_name']); ?></h3>
                                    <p><?= htmlspecialchars($product['description']); ?></p>
                                    <p>Price: $<?= htmlspecialchars($product['price']); ?></p>
                                    <p>Stock: <?= htmlspecialchars($product['stock_quantity']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>