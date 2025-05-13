<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}

// Include database connection
include '../includes/db.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Home" content="Everything you need to know about the system is located here: its functions, purpose, and why I've been struggling to finish this project haha">
    <link rel="stylesheet" type="text/css" href="/idealcozydesign/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="/idealcozydesign/css/cards.css"/>

    <title>Admin Side</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #253529 !important; /* Your original dark theme */
            color: white;
        }

        /* Notification Card Styling */
        .notification-card {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 1000;
            transition: all 0.5s ease-in-out;
        }

        .card {
            border-radius: 10px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        
        .card:hover {
            transform: scale(1.05);
        }

        /* Widget Row Section */
        .widget-row {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-bottom: 40px;
        }

        .widget {
            flex: 1;
            margin: 10px;
        }

    </style>
</head>
<body>

<?php include("../partials/sidebar.php"); ?> 
<?php include("../partials/navbar.php"); ?>

<!-- DASHBOARD CONTAINER -->
<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- Widgets Section -->
        <div class="container my-4">
            <div class="row text-center">
                <!-- Widget 1: Total Activity Logs -->
                <div class="col-md-4">
                    <a href="activity_logs.php?type=total" class="text-decoration-none">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Activity Logs</h5>
                                <p class="card-text display-4"><?php echo $totalActivities; ?></p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Widget 2: Merch Activity Logs -->
                <div class="col-md-4">
                    <a href="activity_logs.php?type=merch" class="text-decoration-none">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Merch Activity Logs</h5>
                                <p class="card-text display-4"><?php echo $totalMerch; ?></p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Widget 3: Product Activity Logs -->
                <div class="col-md-4">
                    <a href="activity_logs.php?type=product" class="text-decoration-none">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Product Activity Logs</h5>
                                <p class="card-text display-4"><?php echo $totalProduct; ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider (Separates Activity Logs from Stock Levels) -->
        <div class="divider"></div>

        <!-- Product Stock Levels -->
        <h1>Product Stock Levels</h1>
        <div id="charts-container" class="charts-grid"></div>

        <!-- Another Divider (Below Stock Levels) -->
        <div class="divider"></div>
    </div>  
</div>

<!-- Scripts -->
<script src="../scripts/fetch_charts.js"></script>
<script src="../scripts/fetch_logs.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>