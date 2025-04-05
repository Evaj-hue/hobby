<?php
session_start();
include '../includes/db.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !in_array($_SESSION['user']['role'], ['admin', 'moderator'])) {
    header('Location: ../index.html'); // Redirect to login if unauthorized
    exit();
}

// Fetch the username from the database
$userId = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['username']) : "Unknown User";

// Function to log activity
function logActivity($userId, $action, $details) {
    global $conn;
    $sql = "INSERT INTO activity_log (user_id, action, details) VALUES (:user_id, :action, :details)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':action', $action, PDO::PARAM_STR);
    $stmt->bindValue(':details', $details, PDO::PARAM_STR);
    $stmt->execute();
}

// Handle adding a new category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    // Insert the new category into the categories table
    $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':category_name', $category_name);
    $stmt->execute();

    // Get the ID of the newly inserted category
    $category_id = $conn->lastInsertId();

    // Check if products already have a category assigned
    // Optionally you can update only products without a category.
    $updateProductsSql = "UPDATE products SET category_id = :category_id WHERE category_id IS NULL";
    $updateStmt = $conn->prepare($updateProductsSql);
    $updateStmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $updateStmt->execute();

    // Check if the query executed correctly
    if ($updateStmt->rowCount() > 0) {
        // If rows are affected, log the success
        logActivity($userId, "added category", "Category: $category_name and updated related products");
    } else {
        // If no rows affected, log a warning or handle it
        logActivity($userId, "added category", "Category: $category_name, but no products updated");
    }

    $_SESSION['message'] = 'Category added successfully!';
    header('Location: manage_categories.php');
    exit();
}

// Handle removing a category
if (isset($_POST['remove_category'])) {
    $category_id = (int)$_POST['category_id'];

    $stmt = $conn->prepare("SELECT category_name FROM categories WHERE id = :category_id");
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = :category_id");
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        logActivity($userId, "removed category", "Category: " . $category['category_name']);
        $_SESSION['message'] = 'Category removed successfully!';
    } else {
        $_SESSION['message'] = 'Error removing category.';
    }

    header('Location: manage_categories.php');
    exit();
}

// Handle updating a category
if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    $stmt = $conn->prepare("UPDATE categories SET category_name = :category_name WHERE id = :category_id");
    $stmt->bindValue(':category_name', $category_name);
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();

    logActivity($userId, "updated category", "Category ID: $category_id, New Name: $category_name");
    $_SESSION['message'] = 'Category updated successfully!';
    header('Location: manage_categories.php');
    exit();
}

// Fetch categories from the database
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
    /* Modal Background */
    .modal-content {
    background-color: #253529 !important;
    color: white !important;
    border: 1px solid #50624e;
}

/* Modal Header */
.modal-header {
    background-color: #3d4f40 !important;
    border-bottom: 1px solid #50624e;
}

.modal-title {
    color: white !important;
}

/* Modal Close Button */
.btn-close {
    filter: invert(1);
}

/* Modal Body */
.modal-body {
    background-color: #253529 !important;
}

/* Form Labels */
.modal-body .form-label {
    color: white !important;
}

/* Form Inputs */
.modal-body .form-control, 
.modal-body .form-select {
    background-color: #3d4f40 !important;
    color: white !important;
    border: 1px solid #50624e;
}

.modal-body .form-control::placeholder {
    color: #a5b29e !important;
}

/* Modal Footer */
.modal-footer {
    background-color: #3d4f40 !important;
    border-top: 1px solid #50624e;
}

/* Buttons */
.btn-primary {
    background-color: #ED7117 !important;
    border-color: #7A3803 !important;
}

.btn-primary:hover {
    background-color: #7A3803 !important;
}

.btn-secondary {
    background-color: #50624e !important;
    border-color: #7a8c74 !important;
    color: white !important;
}

.btn-secondary:hover {
    background-color: #5a6b58 !important;
}
    </style>
</head>
<body>
<div class="content">
    <?php include("../partials/sidebar.php"); ?> 
    <?php include("../partials/navbar.php"); ?> 

    <h2 class="text-center mb-4">Manage Categories</h2>
    <span class="navbar-text me-3">Welcome, <?php echo $username; ?>!</span>
    
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>

    <table id="categoriesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td>
                    <!-- Edit Button -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                            data-id="<?php echo $row['id']; ?>" 
                            data-name="<?php echo $row['category_name']; ?>">Edit</button>
                    
                    <!-- Delete Button -->
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal"
                            data-id="<?php echo $row['id']; ?>"
                            data-name="<?php echo $row['category_name']; ?>">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="text" name="category_name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_category" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_category" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="deleteCategoryId">
                    <p>Are you sure you want to delete this category?</p>
                    <p id="deleteCategoryName" class="fw-bold"></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="remove_category" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables with search and pagination functionality
        $('#categoriesTable').DataTable({
            "paging": true,           // Enable pagination
            "searching": true,        // Enable search bar
            "ordering": true,         // Enable sorting
            "info": true,             // Show table info
            "lengthChange": true,     // Allow changing the number of entries
            "pageLength": 10,         // Default number of rows per page
        });

        // Edit Category Modal - Set values
        var editCategoryModal = document.getElementById('editCategoryModal');
        editCategoryModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var categoryId = button.getAttribute('data-id');
            var categoryName = button.getAttribute('data-name');
            
            var editCategoryId = editCategoryModal.querySelector('#editCategoryId');
            var editCategoryName = editCategoryModal.querySelector('#editCategoryName');
            
            editCategoryId.value = categoryId;
            editCategoryName.value = categoryName;
        });

        // Delete Category Modal - Set values
        var deleteCategoryModal = document.getElementById('deleteCategoryModal');
        deleteCategoryModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var categoryId = button.getAttribute('data-id');
            var categoryName = button.getAttribute('data-name');
            
            var deleteCategoryId = deleteCategoryModal.querySelector('#deleteCategoryId');
            var deleteCategoryName = deleteCategoryModal.querySelector('#deleteCategoryName');
            
            deleteCategoryId.value = categoryId;
            deleteCategoryName.textContent = categoryName;
        });
    });
</script>

</body>
</html>
