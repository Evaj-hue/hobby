<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit();
}

include '../includes/db.php';

// Function to resize images
function resizeImage($source, $destination, $width, $height) {
    $imageInfo = getimagesize($source);
    $mime = $imageInfo['mime'];

    // Create an image resource based on mime type
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            throw new Exception('Unsupported image type.');
    }

    // Create a blank canvas for the resized image
    $resizedImage = imagecreatetruecolor($width, $height);

    // Preserve transparency for PNG and GIF
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefilledrectangle($resizedImage, 0, 0, $width, $height, $transparent);
    }

    // Resize the image
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

    // Save the resized image
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($resizedImage, $destination, 90);
            break;
        case 'image/png':
            imagepng($resizedImage, $destination);
            break;
        case 'image/gif':
            imagegif($resizedImage, $destination);
            break;
    }

    // Free memory
    imagedestroy($image);
    imagedestroy($resizedImage);
}

// Resize images for all products
$sql = "SELECT * FROM merch_products";
$result = $conn->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $source = $row['image'];
    $destination = $row['image']; // Overwrite the original image
    resizeImage($source, $destination, 200, 200); // Resize to 200x200
}

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
    .dashboard-container {
        padding: 20px;
    }

    .cards-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-start;
    }

    .product-card {
        background-color: #362532;
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 10px;
        text-align: center;
        flex: 1 1 calc(20% - 16px); /* Adjust to fit 5 cards in a row */
        max-width: calc(20% - 16px); /* Same as flex-basis */
        margin: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
    }

    .product-card h3 {
        font-size: 1.2rem;
        margin: 10px 0;
        color: #ED7117;
    }

    .product-card p {
        font-size: 0.9rem;
        margin: 5px 0;
    }

    .product-card:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .category-header {
        color: #ED7117;
        font-size: 1.8rem;
        font-weight: bold;
        margin: 20px 0 10px;
        text-align: center;
        text-transform: uppercase;
    }

    /* Responsive Adjustments */
    @media screen and (max-width: 1200px) {
        .product-card {
            flex: 1 1 calc(25% - 16px); /* Adjust to fit 4 cards in a row */
            max-width: calc(25% - 16px);
        }
    }

    @media screen and (max-width: 768px) {
        .product-card {
            flex: 1 1 calc(50% - 16px); /* Adjust to fit 2 cards in a row */
            max-width: calc(50% - 16px);
        }

        .category-header {
            font-size: 1.3rem;
        }
    }

    @media screen and (max-width: 480px) {
        .product-card {
            flex: 1 1 100%; /* Adjust to fit 1 card per row */
            max-width: 100%;
        }

        .category-header {
            font-size: 1.2rem;
        }
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
                        <div class="cards-container">
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