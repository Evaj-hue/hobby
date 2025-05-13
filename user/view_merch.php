<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../index.html'); // Redirect to login page if not logged in
    exit();
}

// Fetch the logged-in user's information
$user = $_SESSION['user'];

// Ensure the user is a staff member
if ($user['role'] !== 'user') {
    header('Location: dashboard.php'); // Redirect to dashboard if not a staff user
    exit();
}

// Fetch merch products from the database
$sql = "SELECT * FROM merch_products";
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
    <title>View Merch</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
            background-color: #253529 !important;
            color: white !important;
        }
        .content {
            margin-left: 260px;
            padding: 80px 20px 20px;
        }
        table {
            background-color: #3d4f40 !important;
            color: white !important;
            border-radius: 10px;
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background-color: #50624e !important;
            font-size: 1rem;
            font-weight: bold;
        }
        table th, table td {
            color: white !important;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #7a8c74;
        }
        table tbody tr:hover {
            background-color: #5a6b58 !important;
        }
        .dataTables_wrapper input, .dataTables_wrapper select {
            background-color: #50624e !important;
            color: white !important;
            border: 1px solid #7a8c74 !important;
            padding: 5px;
        }
        
    </style>
</head>
<body>
<div class="content">
<?php include("../partials/user_navbar.php"); ?> <!-- Navbar included -->
<?php include("../partials/user_sidebar.php"); ?> <!-- Sidebar included -->
    <h2 class="text-center mb-4">List of Merch</h2>
    <table id="merchTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><img src="<?php echo htmlspecialchars($row['image']); ?>" width="50" alt="Merch Image"></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#merchTable').DataTable();
    });
</script>
</body>
</html>