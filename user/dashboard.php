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
$sql = "SELECT * FROM products";
$productResult = $conn->query($sql);
while ($row = $productResult->fetch(PDO::FETCH_ASSOC)) {
    $source = $row['image'];
    $destination = $row['image']; // Overwrite the original image
    resizeImage($source, $destination, 200, 200); // Resize to 200x200
}

// Resize images for all merch products
$sql = "SELECT * FROM merch_products";
$merchResult = $conn->query($sql);
while ($row = $merchResult->fetch(PDO::FETCH_ASSOC)) {
    $source = $row['image'];
    $destination = $row['image']; // Overwrite the original image
    resizeImage($source, $destination, 200, 200); // Resize to 200x200
}

// Fetch products grouped by category
$sql = "SELECT * FROM products ORDER BY category, product_name";
$productResult = $conn->query($sql);

$productsByCategory = [];
while ($row = $productResult->fetch(PDO::FETCH_ASSOC)) {
    $productsByCategory[$row['category']][] = $row;
}

// Fetch merch products grouped by category
$sql = "SELECT * FROM merch_products ORDER BY category, product_name";
$merchResult = $conn->query($sql);

$merchByCategory = [];
while ($row = $merchResult->fetch(PDO::FETCH_ASSOC)) {
    $merchByCategory[$row['category']][] = $row;
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
            margin-top: 10px; /* Added margin for consistency */
        }

        .product-card, .merch-card {
            background-color: #362532;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 15px;
            text-align: center;
            flex: 1 1 calc(20% - 20px); /* Ensure cards fit evenly in rows */
            max-width: calc(20% - 20px);
            min-height: 300px; /* Set a consistent minimum height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card img, .merch-card img {
            width: 100%;
            height: 150px; /* Set fixed height for images */
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .product-card h3, .merch-card h3 {
            font-size: 1.2rem;
            margin: 10px 0;
            color: #ED7117;
        }

        .product-card p, .merch-card p {
            font-size: 0.9rem;
            margin: 5px 0;
        }

        .product-card:hover, .merch-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        .category-header.products {
          color: #ED7117; /* Orange for Products */
            }

        .category-header.merch {
               color: #3AB54A; /* Green for Merch */
            }
        .category-header {
            color: #ED7117;
            font-size: 1.8rem;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .toggle-icon {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ED7117;
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
            background: rgba(0, 0, 0, 0.5);
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
        <h1 class="text-center">Dashboard</h1>

        <!-- Toggle All Products Section -->
        <h2 class="category-header products">
            All Products
            <span class="toggle-icon" onclick="toggleSection('all-products')">▼</span>
        </h2>
        <div class="category-divider main-divider"></div>
        <div class="all-products" style="display: none;"> <!-- Hidden by default -->
            <?php if (!empty($productsByCategory)): ?>
                <?php foreach ($productsByCategory as $category => $products): ?>
                    <h2 class="category-header">
                        <?= htmlspecialchars($category); ?>
                        <span class="toggle-icon" onclick="toggleSection('product-<?= htmlspecialchars($category); ?>')">▼</span>
                    </h2>
                    <div class="category-divider"></div>
                    <div class="cards-container product-<?= htmlspecialchars($category); ?>" style="display: none;"> <!-- Hidden by default -->
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>">
                                <h3><?= htmlspecialchars($product['product_name']); ?></h3>
                                <p>Shelf: <?= htmlspecialchars($product['shelf']); ?></p>
                                <p>Stock: <?= htmlspecialchars($product['units_in_stock']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No products available.</p>
            <?php endif; ?>
        </div>

        <!-- Toggle All Merch Section -->
        <h2 class="category-header merch">
            All Merch
            <span class="toggle-icon" onclick="toggleSection('all-merch')">▼</span>
        </h2>
        <div class="category-divider main-divider"></div>
        <div class="all-merch" style="display: none;"> <!-- Hidden by default -->
            <?php if (!empty($merchByCategory)): ?>
                <?php foreach ($merchByCategory as $category => $merch): ?>
                    <h2 class="category-header">
                        <?= htmlspecialchars($category); ?>
                        <span class="toggle-icon" onclick="toggleSection('merch-<?= htmlspecialchars($category); ?>')">▼</span>
                    </h2>
                    <div class="category-divider"></div>
                    <div class="cards-container merch-<?= htmlspecialchars($category); ?>" style="display: none;"> <!-- Hidden by default -->
                        <?php foreach ($merch as $item): ?>
                            <div class="merch-card">
                                <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>">
                                <h3><?= htmlspecialchars($item['product_name']); ?></h3>
                                <p><?= htmlspecialchars($item['description']); ?></p>
                                <p>Price: $<?= htmlspecialchars($item['price']); ?></p>
                                <p>Stock: <?= htmlspecialchars($item['stock_quantity']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No merch products available.</p>
            <?php endif; ?>
        </div>
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

    <script>
        function toggleSection(sectionClass) {
        const section = document.querySelector(`.${sectionClass}`);
        const icon = document.querySelector(`.toggle-icon[onclick="toggleSection('${sectionClass}')"]`);

        // Check if it's a "Show All" action
        if (sectionClass === 'all-products' || sectionClass === 'all-merch') {
            const subSections = document.querySelectorAll(`.${sectionClass} .cards-container`);
            
            if (section.style.display === "none" || !section.style.display) {
                section.style.display = "block"; // Show the main section
                subSections.forEach(subSection => subSection.style.display = "flex"); // Show all subsections
                icon.textContent = "▼"; // Update icon to down arrow
            } else {
                section.style.display = "none"; // Hide the main section
                subSections.forEach(subSection => subSection.style.display = "none"); // Hide all subsections
                icon.textContent = "▲"; // Update icon to up arrow
            }
            return;
        }

        // Individual toggle for subsections
        if (section.style.display === "none" || !section.style.display) {
            section.style.display = "flex"; // Show the section
            icon.textContent = "▼"; // Update icon to down arrow
        } else {
            section.style.display = "none"; // Hide the section
            icon.textContent = "▲"; // Update icon to up arrow
        }
    }
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>
</html>