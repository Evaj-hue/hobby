<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get total users count
$userCountResult = $conn->query("SELECT COUNT(*) as count FROM users");
$userCount = $userCountResult->fetch_assoc()['count'];

// Get access log summary - improved query to count correctly
$accessStatsQuery = "SELECT 
                    COUNT(*) as total_logs,
                    COUNT(DISTINCT CASE WHEN status = 'granted' THEN rfid_tag END) as unique_granted_users,
                    SUM(CASE WHEN status = 'granted' THEN 1 ELSE 0 END) as granted_count,
                    SUM(CASE WHEN status = 'denied' THEN 1 ELSE 0 END) as denied_count,
                    COUNT(DISTINCT rfid_tag) as unique_tags
                FROM access_logs";
$accessStats = $conn->query($accessStatsQuery)->fetch_assoc();

// Get recent logs
$recentLogsQuery = "SELECT a.id, a.rfid_tag, a.status, a.timestamp, u.username
                    FROM access_logs a
                    LEFT JOIN users u ON a.user_id = u.id
                    ORDER BY a.timestamp DESC
                    LIMIT 5";
$recentLogs = $conn->query($recentLogsQuery);

// Get recent users
$recentUsersQuery = "SELECT id, username, rfid_tag, role
                     FROM users
                     ORDER BY id DESC
                     LIMIT 5";
$recentUsers = $conn->query($recentUsersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFID Access Control Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
            margin-left: 200px; /* space for sidebar */
            padding-top: 60px; /* Space for fixed navbar */
        }
        
        .main-content {
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        @media (max-width: 768px) {
            body {
                margin-left: 0;
            }
        }
        
        .card-header {
            background-color: #253529;
            color: white;
        }
        
        .dashboard-title {
            color: #253529;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stat-card {
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .rfid-tag {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php include("../partials/user_navbar.php"); ?>
    <?php include("../partials/sidebar.php"); ?>
    
    <div class="main-content">
        <h1 class="dashboard-title">RFID Access Control Dashboard</h1>
        
        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <a href="register_user.php" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-user-plus"></i> Register New User
                </a>
                <a href="manage_users.php" class="btn btn-success btn-lg me-2">
                    <i class="fas fa-users-cog"></i> Manage Users
                </a>
                <a href="log_rfid.php" class="btn btn-info btn-lg text-white">
                    <i class="fas fa-list-alt"></i> View Access Logs
                </a>
            </div>
        </div>
        
        <!-- Recent Data -->
        <div class="row">
            <!-- Recent Logs -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Access Logs</h5>
                        <a href="log_rfid.php" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recentLogs && $recentLogs->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>RFID Tag</th>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($log = $recentLogs->fetch_assoc()): ?>
                                            <tr>
                                                <td><span class="rfid-tag"><?= htmlspecialchars($log['rfid_tag']) ?></span></td>
                                                <td><?= $log['username'] ? htmlspecialchars($log['username']) : '<span class="badge bg-warning text-dark">Unknown</span>' ?></td>
                                                <td>
                                                    <?php if($log['status'] == 'granted'): ?>
                                                        <span class="badge bg-success">Granted</span>
                                                    <?php elseif($log['status'] == 'denied'): ?>
                                                        <span class="badge bg-danger">Denied</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?= htmlspecialchars($log['status']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('M d, H:i', strtotime($log['timestamp'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No recent access logs found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recently Registered Users</h5>
                        <a href="manage_users.php" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recentUsers && $recentUsers->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>RFID Tag</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($user = $recentUsers->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['username']) ?></td>
                                                <td><span class="rfid-tag"><?= htmlspecialchars($user['rfid_tag']) ?></span></td>
                                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($user['role']) ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No recent users found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
