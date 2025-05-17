<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle API calls
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid_tag = strtoupper($_POST['rfid_tag']); // Ensure uppercase
    $status = $_POST['status'];

    // Fetch user
    $stmt = $conn->prepare("SELECT id FROM users WHERE rfid_tag = ?");
    $stmt->bind_param("s", $rfid_tag);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Log access with user_id
        $stmt = $conn->prepare("INSERT INTO access_logs (rfid_tag, status, timestamp, user_id) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("ssi", $rfid_tag, $status, $user_id);
    } else {
        // Unknown user (optional handling)
        $stmt = $conn->prepare("INSERT INTO access_logs (rfid_tag, status, timestamp) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $rfid_tag, $status);
    }

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    exit; // End execution for API calls
}

// If not an API call, display the logs page
// Get logs with username
$query = "SELECT a.id, a.rfid_tag, a.status, a.timestamp, u.username 
          FROM access_logs a 
          LEFT JOIN users u ON a.user_id = u.id 
          ORDER BY a.timestamp DESC 
          LIMIT 100";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFID Access Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .badge-success {
            background-color: #28a745;
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
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
    <?php include("../partials/navbar.php"); ?>
    <?php include("../partials/sidebar.php"); ?>
    
    <div class="main-content">
        <div class="container-fluid mt-4">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">RFID Access Logs</h2>
                    <a href="index.php" class="btn btn-outline-light btn-sm" style="background-color: rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="logsTable" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>RFID Tag</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($log = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $log['id'] ?></td>
                                            <td><span class="rfid-tag"><?= htmlspecialchars($log['rfid_tag']) ?></span></td>
                                            <td><?= $log['username'] ? htmlspecialchars($log['username']) : '<span class="badge bg-warning">Unknown</span>' ?></td>
                                            <td>
                                                <?php if($log['status'] == 'granted'): ?>
                                                    <span class="badge bg-success">Access Granted</span>
                                                <?php elseif($log['status'] == 'denied'): ?>
                                                    <span class="badge bg-danger">Access Denied</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($log['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $log['timestamp'] ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No access logs found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Showing the last 100 log entries</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#logsTable').DataTable({
                "order": [[0, "desc"]], // Sort by ID descending (newest first)
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "language": {
                    "emptyTable": "No access logs found"
                }
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
