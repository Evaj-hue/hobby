<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid_tag = strtoupper(trim($_POST["rfid_tag"]));
    $username = trim($_POST["username"]);
    $role = trim($_POST["role"]);

    // Check if RFID already exists
    $check = $conn->prepare("SELECT * FROM users WHERE rfid_tag = ?");
    $check->bind_param("s", $rfid_tag);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "RFID tag is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (rfid_tag, username, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $rfid_tag, $username, $role);

        if ($stmt->execute()) {
            $message = "User registered successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register New RFID User</title>
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
        
        /* RFID display style */
        .rfid-input {
            font-family: monospace;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <?php include("../partials/navbar.php"); ?>
    <?php include("../partials/sidebar.php"); ?>
    
    <div class="main-content">
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">Register RFID User</h2>
                            <a href="index.php" class="btn btn-outline-light btn-sm" style="background-color: rgba(255, 255, 255, 0.2);">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert <?= strpos($message, "successfully") !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                                    <?= $message ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="rfid_tag" class="form-label">RFID Tag (Hex)</label>
                                    <input type="text" id="rfid_tag" name="rfid_tag" class="form-control rfid-input" required 
                                           placeholder="e.g. 0XC4 0X66 0X27 0XDB">
                                    <div class="form-text">Enter the RFID tag value in 0X format with spaces.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" id="username" name="username" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select id="role" name="role" class="form-select" required>
                                        <option value="admin">Admin</option>
                                        <option value="barista">Barista</option>
                                        <option value="staff">Staff</option>
                                        <option value="guest">Guest</option>
                                    </select>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Register User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Remove automatic formatting for RFID input to prevent issues
        document.getElementById('rfid_tag').addEventListener('input', function(e) {
            // Only convert to uppercase, no other formatting
            e.target.value = e.target.value.toUpperCase();
        });
        
        // Initialize any Bootstrap components
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
