<?php
session_start();
include("../includes/db.php"); // Ensure the correct path

// Function to log activity
function logActivity($userId, $action, $details) {
    global $conn;

    // Get the username of the logged-in user
    $sql = "SELECT username FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $username = $user ? $user['username'] : '';

    // Insert the activity log with username
    $sql = "INSERT INTO activity_log (user_id, username, action, details) VALUES (:user_id, :username, :action, :details)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':username' => $username, ':action' => $action, ':details' => $details]);
}

// Check if the user is logged in and has a valid role
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'moderator'])) {
    header('Location: index.html');
    exit();
}

try {
    // Logic for adding a new user
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $full_name = $_POST['full_name'];
        $contact_number = $_POST['contact_number'];

        // Check for existing username or email
        $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Username or email already exists!'); window.location.href='manage_roles.php';</script>";
            exit();
        }

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, email, password, role, full_name, contact_number, status) 
                VALUES (:username, :email, :password, :role, :full_name, :contact_number, 'active')";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([':username' => $username, ':email' => $email, ':password' => $password, ':role' => $role, ':full_name' => $full_name, ':contact_number' => $contact_number])) {
            logActivity($_SESSION['user']['id'], "Add User", "Added user: $username");
            echo "<script>alert('New user added successfully!'); window.location.href='manage_roles.php';</script>";
        } else {
            echo "<script>alert('Error adding new user.'); window.location.href='manage_roles.php';</script>";
        }
        exit();
    }

    // Logic for changing roles
    if (isset($_POST['change_role'])) {
        $user_id = $_POST['user_id'];
        $new_role = $_POST['role'];
        
        // Update the user's role
        $sql = "UPDATE users SET role = :role WHERE id = :user_id";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([':role' => $new_role, ':user_id' => $user_id])) {
            logActivity($_SESSION['user']['id'], "Change Role", "Updated user ID $user_id to role $new_role");
            echo "<script>alert('User role updated successfully!'); window.location.href='manage_roles.php';</script>";
        } else {
            echo "<script>alert('Error updating user role.'); window.location.href='manage_roles.php';</script>";
        }
        exit();
    }

    // Logic for removing/deactivating/reactivating users
    if (isset($_GET['action']) && isset($_GET['user_id'])) {
        $action = $_GET['action'];
        $user_id = $_GET['user_id'];
        $new_status = '';
        
        if ($action === 'remove') {
            $sql = "DELETE FROM users WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            logActivity($_SESSION['user']['id'], "Remove User", "Removed user ID $user_id");
        } elseif ($action === 'deactivate') {
            $new_status = 'inactive';
            $sql = "UPDATE users SET status = :status WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':status' => $new_status, ':user_id' => $user_id]);
            logActivity($_SESSION['user']['id'], "Deactivate User", "Deactivated user ID $user_id");
        } elseif ($action === 'reactivate') {
            $new_status = 'active';
            $sql = "UPDATE users SET status = :status WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':status' => $new_status, ':user_id' => $user_id]);
            logActivity($_SESSION['user']['id'], "Reactivate User", "Reactivated user ID $user_id");
        }
        echo "<script>alert('User status updated successfully!'); window.location.href='manage_roles.php';</script>";
        exit();
    }

    // Fetch all users from the database
    $sql = "SELECT id, username, email, contact_number AS mobile, role, status, created_at, full_name FROM users";
    $stmt = $conn->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Database error: " . $e->getMessage() . "'); window.location.href='manage_roles.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
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
    </style>
</head>
<body>
<div class="content">
<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/navbar.php'; ?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-bg-<?php echo $_SESSION['msg_type']; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']); // Clear message after displaying
                        unset($_SESSION['msg_type']); 
                    ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var toastEl = document.getElementById('liveToast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    </script>
<?php endif; ?>

<h2 class="text-center mb-4">Manage Users</h2>
        <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</span>
        <!-- Add Product Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        <table id="rolesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                data-user-id="<?php echo $row['id']; ?>"
                                data-username="<?php echo $row['username']; ?>"
                                data-email="<?php echo $row['email']; ?>"
                                data-full-name="<?php echo $row['full_name']; ?>"
                                data-contact-number="<?php echo $row['mobile']; ?>"
                                data-role="<?php echo $row['role']; ?>"
                                data-status="<?php echo $row['status']; ?>">Edit</button>
                        <a href="manage_roles.php?action=remove&user_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                        <?php if ($row['status'] !== 'inactive'): ?>
                            <a href="manage_roles.php?action=deactivate&user_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Deactivate</a>
                        <?php endif; ?>
                        <?php if ($row['status'] === 'inactive'): ?>
                            <a href="manage_roles.php?action=reactivate&user_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Reactivate</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../modal/modals.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    
 
<!-- Bootstrap JS (for Toast functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
    <script src="../scripts/manage_roles.js"></script>
    <script>
    $(document).ready(function() {
        $('#rolesTable').DataTable({
            "paging": true,         // Enable pagination
            "searching": true,      // Enable search functionality
            "ordering": true,       // Enable sorting by columns
            "lengthChange": true,   // Enable length change option
            "pageLength": 10,       // Set default number of entries to show per page
            "lengthMenu": [10, 25, 50, 100],  // Define the number of entries to show (options in the dropdown)
            "info": true,           // Show information about number of entries and pages
            "autoWidth": false,     // Disable auto width for columns
        });
    });
</script>

    
</body>

</html>
