<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}
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
        .notification-card.show {
            display: block;
        }

        /* Compact Activity Logs Card */
        .log-card {
            max-width: 18rem;
            margin-bottom: 20px;
        }

        /* Divider */
        .divider {
            border-bottom: 2px solid #7A3803;
            margin: 20px 0;
        }
        
    </style>
</head>
<body>

<?php include("../partials/sidebar.php"); ?> 
<?php include("../partials/navbar.php"); ?>
<!-- DASHBOARD CONTAINER -->
<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- Logs Section -->
        <h2>Recent Activity</h2>
        <div id="logs-container"></div>

        <!-- Divider (Separates Activity Logs from Stock Levels) -->
        <div class="divider"></div>

        <!-- Product Stock Levels -->
        <h1>Product Stock Levels</h1>
        <div id="charts-container" class="charts-grid"></div>

        <!-- Another Divider (Below Stock Levels) -->
        <div class="divider"></div>

        <!-- Notification Card -->
        <div id="notification-card" class="notification-card">
        <strong><i class="fas fa-bell"></i> New Activity:</strong>
            <p id="notification-text"></p>
        </div>
    </div>  
</div>
<!-- Scripts -->
<script src="../scripts/fetch_charts.js"></script>
<script src="../scripts/fetch_logs.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fetch Log Count for Activity Logs Card
function fetchLogCount() {
    fetch("get_logs.php")
        .then(response => response.json())
        .then(logs => {
            if (!logs || logs.length === 0 || logs.message) {
                document.getElementById("log-count").textContent = "0 Logs";
                return;
            }
            document.getElementById("log-count").textContent = `${logs.length} Logs`;
        })
        .catch(error => console.error("Error fetching logs:", error));
}

// Fetch log count on page load and update every 10 seconds
fetchLogCount();
setInterval(fetchLogCount, 10000);

</script>
</body>
</html>
