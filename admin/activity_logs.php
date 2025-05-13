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

// Fetch total activity logs
$totalActivitiesSql = "SELECT COUNT(*) as total FROM activity_log";
$totalActivitiesResult = $conn->query($totalActivitiesSql);
$totalActivities = $totalActivitiesResult->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch total merch activity logs
$totalMerchSql = "SELECT COUNT(*) as total FROM activity_log WHERE action LIKE '%merch%'";
$totalMerchResult = $conn->query($totalMerchSql);
$totalMerch = $totalMerchResult->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch total product activity logs
$totalProductSql = "SELECT COUNT(*) as total FROM activity_log WHERE action LIKE '%product%'";
$totalProductResult = $conn->query($totalProductSql);
$totalProduct = $totalProductResult->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch activity log from the database
$sql = "SELECT id, username, action, details, created_at FROM activity_log ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch results for Merch and Product Activity Logs
$merchSql = "SELECT id, action, details, created_at FROM activity_log WHERE action LIKE '%merch%' ORDER BY created_at DESC";
$productSql = "SELECT id, action, details, created_at FROM activity_log WHERE action LIKE '%product%' ORDER BY created_at DESC";

$merchResult = $conn->query($merchSql);
$productResult = $conn->query($productSql);
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
            padding: 20px;
        }

        /* Widgets Styling */
        .card {
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .table {
            background-color: #3d4f40 !important;
            color: white !important;
            border-radius: 10px;
            border-collapse: collapse;
        }

        .table th, .table td {
            color: white !important;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #7a8c74;
        }

        .table tbody tr:hover {
            background-color: #5a6b58 !important;
        }

        /* Scrollable Icons */
        .scroll-icons {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .scroll-icons a {
            color: white;
            text-decoration: none;
            background-color: #50624e;
            padding: 10px 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .scroll-icons a:hover {
            background-color: #5a6b58;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 80px 10px;
            }

            .scroll-icons {
                flex-direction: column;
                gap: 10px;
            }

            table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <?php include("../partials/navbar.php"); ?>
    <?php include("../partials/sidebar.php"); ?>

    <div class="content">
        <!-- Widgets Section -->
        <div class="container my-4">
            <div class="row text-center">
                <!-- Widget 1: Total Activity Logs -->
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Users Activity Logs</h5>
                            <p class="card-text display-4"><?php echo $totalActivities; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Widget 2: Merch Activity Logs -->
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Merch Activity Logs</h5>
                            <p class="card-text display-4"><?php echo $totalMerch; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Widget 3: Product Activity Logs -->
                <div class="col-md-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5 class="card-title">Product Activity Logs</h5>
                            <p class="card-text display-4"><?php echo $totalProduct; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clickable Icons Section -->
        <div class="scroll-icons">
            <a href="#userActivityTable" title="Go to User Activity">👤 User Activity</a>
            <a href="#merchActivityLogTable" title="Go to Merch Activity">🛒 Merch</a>
            <a href="#productActivityLogTable" title="Go to Product Activity">📦 Product</a>
        </div>

        <!-- User Activity Logs Table -->
        <h2 class="text-center mb-4" id="userActivityTable">User Activity Reports</h2>
        <span class="navbar-text me-3">Welcome, <?php echo $username; ?>!</span>
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

        <hr class="my-5">

        <!-- Merch Activity Logs Table -->
        <h3 class="text-center mb-4" id="merchActivityLogTable">Merch Activity Logs</h3>
        <table id="merchActivityTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $merchResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <hr class="my-5">

        <!-- Product Activity Logs Table -->
        <h3 class="text-center mb-4" id="productActivityLogTable">Product Activity Logs</h3>
        <table id="productActivityTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $productResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables for specific tables
            $('#activityLogTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthChange": true,
                "pageLength": 10,
            });

            $('#merchActivityTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthChange": true,
                "pageLength": 10,
            });

            $('#productActivityTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthChange": true,
                "pageLength": 10,
            });
        });
    </script>

</body>
</html>