<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle update
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "Role updated successfully!";
}

// Handle delete
if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "User deleted successfully!";
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage RFID Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .btn-primary {
            background-color: #253529;
            border-color: #253529;
        }
        
        .btn-primary:hover {
            background-color: #1a2720;
            border-color: #1a2720;
        }
        
        /* Custom styling to match screenshot */
        .rfid-tag {
            background-color: #00a8ff;
            color: white;
            border-radius: 4px;
            padding: 4px 8px;
            font-family: monospace;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }
        
        .table {
            background-color: white;
        }
        
        .table thead {
            background-color: #253529;
            color: white;
        }
        
        .table th {
            font-weight: 500;
        }
        
        .table tr td {
            vertical-align: middle;
        }
        
        /* Updated button styling to match screenshot */
        .back-btn {
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.2s;
        }
        
        .back-btn:hover {
            background-color: #0b5ed7;
            color: white;
        }
    </style>
</head>
<body>
    <?php include("../partials/navbar.php"); ?>
    <?php include("../partials/sidebar.php"); ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0">RFID Users</h2>
                <a href="index.php" class="back-btn">
                    Back to Dashboard
                </a>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>RFID Tag</th>
                            <th>Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td>
                                            <span class="rfid-tag">
                                                <?= htmlspecialchars($user['rfid_tag']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <select name="role" class="form-select form-select-sm">
                                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                <option value="barista" <?= $user['role'] == 'barista' ? 'selected' : '' ?>>Barista</option>
                                                <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                                                <option value="guest" <?= $user['role'] == 'guest' ? 'selected' : '' ?>>Guest</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" name="update" class="btn btn-sm btn-success">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete user <?= htmlspecialchars($user['username']) ?>?');">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 text-muted">
                <small>Total Users: <?= $result->num_rows ?></small>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                var alertElements = document.querySelectorAll('.alert');
                alertElements.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
