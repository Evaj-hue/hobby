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
            gap: 16px;
            justify-content: flex-start;
        }

        .product-card {
            background-color: #362532;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 15px;
            text-align: center;
            flex: 1 1 calc(20% - 16px); /* Better fit for 5 cards per row */
            max-width: calc(20% - 16px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
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
            margin-top: 30px;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .category-divider {
            height: 2px;
            background-color: #ED7117;
            margin: 20px 0;
            border-radius: 1px;
        }

        /* Popup Styling */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup {
            background: white;
            width: 300px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup h2 {
            font-size: 1.5rem;
            color: #ED7117;
            margin-bottom: 10px;
        }

        .popup p {
            font-size: 1rem;
            color: #333;
            margin-bottom: 20px;
        }

        .popup button {
            background: #ED7117;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .popup button:hover {
            background: #c9560e;
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
                        <div class="category-divider"></div>
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

    <!-- Popup HTML -->
    <div class="popup-overlay" id="popup">
        <div class="popup">
            <h2>Notice</h2>
            <p>The products displayed are available in-store only.</p>
            <button onclick="closePopup()">Okay</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show popup on page load
        document.addEventListener('DOMContentLoaded', function () {
            const popup = document.getElementById('popup');
            popup.style.display = 'flex'; // Show popup
        });

        // Close popup function
        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none'; // Hide popup
        }
    </script>
</body>
</html>