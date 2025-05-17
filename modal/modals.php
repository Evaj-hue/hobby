<!-- modals.php -->
<!--MODAL STYLE-->
<style>
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

/* Style for threshold dropdown items */
.dropdown-menu-end .dropdown-item {
    color: #333;
    cursor: pointer;
}
    
.dropdown-menu-end .dropdown-item:hover {
    background-color: #4caf50;
    color: white;
}
    
/* Style for input groups */
.input-group .btn-outline-secondary {
    color: #fff;
    border-color: #7a8c74;
    background-color: #50624e;
}
    
.input-group .btn-outline-secondary:hover {
    background-color: #3d4f40;
}
    
/* Style for the modals in dark theme */
.modal-content {
    background-color: #3d4f40;
    color: white;
}
    
.modal-header {
    border-bottom: 1px solid #50624e;
}
    
.modal-footer {
    border-top: 1px solid #50624e;
}
    
/* Style for form inputs in modals */
.modal-content .form-control {
    background-color: #253529;
    color: white;
    border: 1px solid #7a8c74;
}
    
.modal-content .form-control:focus {
    background-color: #253529;
    color: white;
    border-color: #4caf50;
    box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
}
</style>
<!-- Add Product Modal -->
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select" required onchange="updateShelves('category', 'shelf')">
                            <option value="" selected disabled>Select Category</option>
                            <option value="coffee">Coffee</option>
                            <option value="tea">Tea</option>
                            <option value="pastries">Pastries</option>
                            <option value="sandwiches">Sandwiches</option>
                            <option value="beverages">Beverages</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="shelf" class="form-label">Shelf</label>
                        <select name="shelf" id="shelf" class="form-select" required>
                            <option value="" selected disabled>Select a shelf</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="units_in_stock" class="form-label">Units in Stock</label>
                        <input type="number" name="units_in_stock" id="units_in_stock" class="form-control" required min="1" max="50">
                    </div>

                    <div class="mb-3">
                        <label for="max_stock" class="form-label">Maximum Stock</label>
                        <input type="number" name="max_stock" id="max_stock" class="form-control" required onchange="calculateThreshold('max_stock', 'stock_threshold')">
                    </div>

                    <div class="mb-3">
                        <label for="stock_threshold" class="form-label">Stock Threshold (Low Stock Alert)</label>
                        <div class="input-group">
                            <input type="number" name="stock_threshold" id="stock_threshold" class="form-control" required>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Auto Set</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('max_stock', 'stock_threshold', 10); return false;">10% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('max_stock', 'stock_threshold', 20); return false;">20% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('max_stock', 'stock_threshold', 30); return false;">30% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('max_stock', 'stock_threshold', 50); return false;">50% of Max</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="edit_product_id">

                    <div class="mb-3">
                        <label for="edit_product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="edit_product_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <select name="category" id="edit_category" class="form-select" required onchange="updateShelves('edit_category', 'edit_shelf')">
                            <option value="" selected disabled>Select Category</option>
                            <option value="coffee">Coffee</option>
                            <option value="tea">Tea</option>
                            <option value="pastries">Pastries</option>
                            <option value="sandwiches">Sandwiches</option>
                            <option value="beverages">Beverages</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_shelf" class="form-label">Shelf</label>
                        <select name="shelf" id="edit_shelf" class="form-select" required>
                            <option value="" selected disabled>Select a shelf</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_units_in_stock" class="form-label">Units in Stock</label>
                        <input type="number" name="units_in_stock" id="edit_units_in_stock" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_max_stock" class="form-label">Maximum Stock</label>
                        <input type="number" name="max_stock" id="edit_max_stock" class="form-control" required onchange="calculateThreshold('edit_max_stock', 'edit_stock_threshold')">
                    </div>

                    <div class="mb-3">
                        <label for="edit_stock_threshold" class="form-label">Stock Threshold (Low Stock Alert)</label>
                        <div class="input-group">
                            <input type="number" name="stock_threshold" id="edit_stock_threshold" class="form-control" required>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Auto Set</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_max_stock', 'edit_stock_threshold', 10); return false;">10% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_max_stock', 'edit_stock_threshold', 20); return false;">20% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_max_stock', 'edit_stock_threshold', 30); return false;">30% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_max_stock', 'edit_stock_threshold', 50); return false;">50% of Max</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Product Image</label>
                        <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Leave blank to keep the current image.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript to Update Shelf Dropdown Dynamically -->
<script>
    function updateShelves(categoryId, shelfId) {
        let category = document.getElementById(categoryId).value;
        let shelfDropdown = document.getElementById(shelfId);
        
        // Shelf options based on category
        let shelves = {
            "coffee": ["A1 - Espresso", "A2 - Latte", "A3 - Cappuccino"],
            "tea": ["B1 - Green Tea", "B2 - Black Tea", "B3 - Herbal Tea"],
            "pastries": ["C1 - Croissants", "C2 - Muffins", "C3 - Danish"],
            "sandwiches": ["D1 - Club Sandwich", "D2 - BLT", "D3 - Grilled Cheese"],
            "beverages": ["E1 - Soda", "E2 - Juice", "E3 - Energy Drinks"]
        };

        // Clear previous options
        shelfDropdown.innerHTML = "<option value='' disabled selected>Select a shelf</option>";

        // Add new options based on selected category
        if (shelves[category]) {
            shelves[category].forEach(function (shelf) {
                let option = document.createElement("option");
                option.value = shelf;
                option.textContent = shelf;
                shelfDropdown.appendChild(option);
            });
        }
    }

    // Function to update shelves when editing
    function loadEditProduct(product) {
        document.getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_product_name').value = product.name;
        document.getElementById('edit_description').value = product.description;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_category').value = product.category;
        updateShelves('edit_category', 'edit_shelf'); // Ensure the shelves are updated
        setTimeout(() => {
            document.getElementById('edit_shelf').value = product.shelf;
        }, 100); // Add slight delay to ensure options are populated before selection
        document.getElementById('edit_units_in_stock').value = product.units_in_stock;
    }
</script>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="manage_roles.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderator</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_user.php" method="POST">
            <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username">
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>

                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name">
                    </div>

                    <div class="mb-3">
                        <label for="edit_contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="edit_contact_number" name="contact_number">
                    </div>

                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select class="form-control" id="edit_role" name="role">
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderator</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-control" id="edit_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Activate/Reactivate Modal -->
<div class="modal fade" id="activateUserModal" tabindex="-1" aria-labelledby="activateUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activateUserModalLabel">Activate / Reactivate User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="manage_roles.php" method="GET">
                <div class="modal-body">
                    <input type="hidden" id="activate_user_id" name="user_id">
                    <p>Are you sure you want to activate/reactivate this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="action" value="reactivate">Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="manage_roles.php" method="GET">
                <div class="modal-body">
                    <input type="hidden" id="delete_user_id" name="user_id">
                    <p>Are you sure you want to delete this user? This action is irreversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="action" value="remove">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Merch Modal -->
<div class="modal fade" id="addMerchModal" tabindex="-1" aria-labelledby="addMerchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_merch.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMerchModalLabel">Add New Merch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="max_stock" class="form-label">Maximum Stock</label>
                        <input type="number" name="max_stock" id="merch_max_stock" class="form-control" required onchange="calculateThreshold('merch_max_stock', 'merch_stock_threshold')">
                    </div>
                    <div class="mb-3">
                        <label for="stock_threshold" class="form-label">Stock Threshold (Low Stock Alert)</label>
                        <div class="input-group">
                            <input type="number" name="stock_threshold" id="merch_stock_threshold" class="form-control" required>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Auto Set</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('merch_max_stock', 'merch_stock_threshold', 10); return false;">10% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('merch_max_stock', 'merch_stock_threshold', 20); return false;">20% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('merch_max_stock', 'merch_stock_threshold', 30); return false;">30% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('merch_max_stock', 'merch_stock_threshold', 50); return false;">50% of Max</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_merch" class="btn btn-primary">Add Merch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Merch Modal -->
<div class="modal fade" id="editMerchModal" tabindex="-1" aria-labelledby="editMerchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_merch.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMerchModalLabel">Edit Merch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="mb-3">
                        <label for="edit_product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="edit_product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stock_quantity" class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="edit_stock_quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_max_stock" class="form-label">Maximum Stock</label>
                        <input type="number" name="max_stock" id="edit_merch_max_stock" class="form-control" required onchange="calculateThreshold('edit_merch_max_stock', 'edit_merch_stock_threshold')">
                    </div>
                    <div class="mb-3">
                        <label for="edit_stock_threshold" class="form-label">Stock Threshold (Low Stock Alert)</label>
                        <div class="input-group">
                            <input type="number" name="stock_threshold" id="edit_merch_stock_threshold" class="form-control" required>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Auto Set</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_merch_max_stock', 'edit_merch_stock_threshold', 10); return false;">10% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_merch_max_stock', 'edit_merch_stock_threshold', 20); return false;">20% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_merch_max_stock', 'edit_merch_stock_threshold', 30); return false;">30% of Max</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setThresholdPercentage('edit_merch_max_stock', 'edit_merch_stock_threshold', 50); return false;">50% of Max</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Product Image</label>
                        <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Leave blank to keep the current image.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="edit_merch" class="btn btn-primary">Update Merch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add JavaScript for threshold calculation -->
<script>
    function setThresholdPercentage(maxStockId, thresholdId, percentage) {
        const maxStockElement = document.getElementById(maxStockId);
        const thresholdElement = document.getElementById(thresholdId);
        
        if (maxStockElement && thresholdElement) {
            const maxStock = parseInt(maxStockElement.value);
            if (!isNaN(maxStock) && maxStock > 0) {
                // Calculate percentage of max stock and round to nearest integer
                const threshold = Math.round((percentage / 100) * maxStock);
                thresholdElement.value = threshold;
            } else {
                alert("Please enter a valid maximum stock value first.");
            }
        }
    }

    function calculateThreshold(maxStockId, thresholdId) {
        // Default to 20% of max stock
        setThresholdPercentage(maxStockId, thresholdId, 20);
    }
</script>




