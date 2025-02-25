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

</style>
<!-- Add Product Modal -->
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_products.php" method="POST">
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
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select" required onchange="updateShelves('category', 'shelf')">
                            <option value="coffee">Coffee</option>
                            <option value="tea">Tea</option>
                            <option value="pastries">Pastries</option>
                            <option value="sandwiches">Sandwiches</option>
                            <option value="beverages">Beverages</option>
                        </select>
                    </div>

                    <!-- Updated Shelf: Now a Dropdown Instead of Text Input -->
                    <div class="mb-3">
                        <label for="shelf" class="form-label">Shelf</label>
                        <select name="shelf" id="shelf" class="form-select" required>
                            <option value="">Select a shelf</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="units_in_stock" class="form-label">Units in Stock</label>
                        <input type="number" name="units_in_stock" id="units_in_stock" class="form-control" required>
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
            <form action="manage_products.php" method="POST">
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
                        <label for="edit_category" class="form-label">Category</label>
                        <select name="category" id="edit_category" class="form-select" required onchange="updateShelves('edit_category', 'edit_shelf')">
                            <option value="coffee">Coffee</option>
                            <option value="tea">Tea</option>
                            <option value="pastries">Pastries</option>
                            <option value="sandwiches">Sandwiches</option>
                            <option value="beverages">Beverages</option>
                        </select>
                    </div>

                    <!-- Updated Shelf: Now a Dropdown Instead of Text Input -->
                    <div class="mb-3">
                        <label for="edit_shelf" class="form-label">Shelf</label>
                        <select name="shelf" id="edit_shelf" class="form-select" required>
                            <option value="">Select a shelf</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_units_in_stock" class="form-label">Units in Stock</label>
                        <input type="number" name="units_in_stock" id="edit_units_in_stock" class="form-control" required>
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
        shelfDropdown.innerHTML = "<option value=''>Select a shelf</option>";

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
</script>

