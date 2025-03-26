<?php
session_start();
include '../includes/db.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !in_array($_SESSION['user']['role'], ['admin', 'moderator'])) {
    header('Location: ../index.html');
    exit();
}

// Fetch the username from the database based on the logged-in user's ID
$userId = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['username']) : "Unknown User";

// Fetch activity log from the database
$sql = "SELECT id, username, action, details, created_at FROM activity_log ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Log</title>
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

    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php if (isset($_SESSION['message'])) {
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                    } ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="content">
        <?php include("../partials/sidebar.php"); ?> 
        <?php include("../partials/navbar.php"); ?> 

        <h2 class="text-center mb-4">Activity Reports</h2>
        <span class="navbar-text me-3">Welcome, <?php echo $username; ?>!</span>

        <!-- Reports Table -->
        <table id="activityLogTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JS (for Toast functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
       $(document).ready(function() {
        // Initialize DataTables with search and pagination functionality
        $('#activityLogTable').DataTable({
            "paging": true,           // Enable pagination
            "searching": true,        // Enable search bar
            "ordering": true,         // Enable sorting
            "info": true,             // Show table info
            "lengthChange": true,     // Allow changing the number of entries
            "pageLength": 10,         // Default number of rows per page
        });
 });
    </script>

</body>
</html>
