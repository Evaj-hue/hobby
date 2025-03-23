<?php
session_start();
include '../includes/db.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !in_array($_SESSION['user']['role'], ['admin', 'moderator'])) {
    header('Location: ../index.html'); // Redirect to login if unauthorized
    exit();
}

// Fetch the username from the database based on the logged-in user's ID
$userId = $_SESSION['user']['id']; // Corrected session key

$stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $user ? htmlspecialchars($user['username']) : "Unknown User";

// Function to log activity
function logActivity($userId, $action, $details) {
    global $conn;

    // Get the username of the logged-in user
    $sql = "SELECT username FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $username = $user ? $user['username'] : '';

    // Insert the activity log with username
    $sql = "INSERT INTO activity_log (user_id, username, action, details) VALUES (:user_id, :username, :action, :details)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':username' => $username, ':action' => $action, ':details' => $details]);
}

// Handle adding a new product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $shelf = $_POST['shelf'];
    $units_in_stock = $_POST['units_in_stock'];

    $sql = "INSERT INTO products (product_name, category, shelf, units_in_stock) VALUES (:product_name, :category, :shelf, :units_in_stock)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':product_name', $product_name);
    $stmt->bindValue(':category', $category);
    $stmt->bindValue(':shelf', $shelf);
    $stmt->bindValue(':units_in_stock', $units_in_stock, PDO::PARAM_INT);
    $stmt->execute();

    logActivity($userId, "added product", "Product: $product_name, Category: $category, Shelf: $shelf, Stock: $units_in_stock");

    $_SESSION['message'] = 'Product added successfully!';
    header('Location: manage_products.php');
    exit();
}

// Handle removing a product
if (isset($_POST['remove_product'])) {
    $product_id = (int)$_POST['product_id'];

    $stmt = $conn->prepare("SELECT product_name FROM products WHERE id = :product_id");
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("DELETE FROM products WHERE id = :product_id");
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        logActivity($userId, "removed product", "Product: " . $product['product_name']);
        $_SESSION['message'] = 'Product removed successfully!';
    } else {
        $_SESSION['message'] = 'Error removing product.';
    }

    header('Location: manage_products.php');
    exit();
}

// Handle updating a product
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $shelf = $_POST['shelf'];
    $units_in_stock = $_POST['units_in_stock'];

    $stmt = $conn->prepare("UPDATE products SET product_name = :product_name, category = :category, shelf = :shelf, units_in_stock = :units_in_stock WHERE id = :product_id");
    $stmt->bindValue(':product_name', $product_name);
    $stmt->bindValue(':category', $category);
    $stmt->bindValue(':shelf', $shelf);
    $stmt->bindValue(':units_in_stock', $units_in_stock, PDO::PARAM_INT);
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    logActivity($userId, "updated product", "Product ID: $product_id, New Name: $product_name, New Category: $category, New Shelf: $shelf, New Stock: $units_in_stock");

    $_SESSION['message'] = 'Product updated successfully!';
    header('Location: manage_products.php');
    exit();
}

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->errorInfo()[2]);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> 
<style>
        /* Dark Background and White Text */
body {
    background-color: #253529 !important;
    color: white !important;
}

/* Sidebar & Content Spacing */
.content {
    margin-left: 260px;
    padding: 80px 20px 20px;
}

/* Table Styling */
table {
    background-color: #3d4f40 !important; /* Darker green-gray */
    color: white !important;
    border-radius: 10px;
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
}

/* Table Header */
thead {
    background-color: #50624e !important;
    font-size: 1rem;
    font-weight: bold;
}

/* Force White Text in Table */
table th, table td {
    color: white !important;
    padding: 15px; /* More padding for better spacing */
    text-align: center;
    vertical-align: middle;
    border: 1px solid #7a8c74;
}

/* Hover Effect */
table tbody tr:hover {
    background-color: #5a6b58 !important;
}

/* DataTables Input & Select */
.dataTables_wrapper input, .dataTables_wrapper select {
    background-color: #50624e !important;
    color: white !important;
    border: 1px solid #7a8c74 !important;
    padding: 5px;
}

/* Responsive Table */
@media (max-width: 768px) {
    .content {
        margin-left: 0;
        padding: 80px 10px;
    }

    table {
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>
       <!-- Toast Notification -->
       <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php if (isset($_SESSION['message'])) {
                        echo $_SESSION['message'];
                        unset($_SESSION['message']); // Clear message after displaying it
                    } ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<div class="content">
<?php include("../partials/sidebar.php"); ?> 
<?php include("../partials/navbar.php"); ?> 

       

        <h2 class="text-center mb-4">Manage Products</h2>
        <span class="navbar-text me-3">Welcome, <?php echo $username; ?>!</span>
        <!-- Add Product Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>

        <!-- Products Table -->
        <table id="productsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Shelf</th>
                    <th>Units in Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['shelf']); ?></td>
                        <td><?php echo htmlspecialchars($row['units_in_stock']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal"
                                    data-id="<?php echo $row['id']; ?>" 
                                    data-name="<?php echo $row['product_name']; ?>" 
                                    data-category="<?php echo $row['category']; ?>" 
                                    data-shelf="<?php echo $row['shelf']; ?>" 
                                    data-stock="<?php echo $row['units_in_stock']; ?>">Edit</button>

                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="remove_product" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php include '../modal/modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JS (for Toast functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable();

            var toastEl = document.getElementById('liveToast');
    if (toastEl && toastEl.querySelector('.toast-body').textContent.trim() !== '') {
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }


            // Populate the edit modal with product data
            $('#editProductModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var productId = button.data('id');
                var productName = button.data('name');
                var category = button.data('category');
                var shelf = button.data('shelf');
                var stock = button.data('stock');

                var modal = $(this);
                modal.find('#edit_product_id').val(productId);
                modal.find('#edit_product_name').val(productName);
                modal.find('#edit_category').val(category);
                modal.find('#edit_shelf').val(shelf);
                modal.find('#edit_units_in_stock').val(stock);
            });
           
        });
        $maxStockLimit = 50; // Define max stock limit

         if ($units_in_stock > $maxStockLimit) {
         $_SESSION['message'] = "Error: Maximum stock limit is $maxStockLimit units.";
         header('Location: manage_products.php');
         exit();
}
    </script>
</body>
</html>
