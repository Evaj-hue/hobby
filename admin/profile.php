<?php
session_start();
include('../includes/db.php'); // Ensure the correct path

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !in_array($_SESSION['user']['role'], ['admin', 'moderator'])) {
    header('Location: ../index.html'); // Redirect to login if unauthorized
    exit();
}

// Get the logged-in user's ID from the session
$userId = $_SESSION['user']['id'];

// Fetch the user's details from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If the user is not found, show an error and redirect
if (!$user) {
    $_SESSION['message'] = "User not found!";
    $_SESSION['msg_type'] = 'danger';
    header("Location: admin_dashboard.php"); // Redirect if user not found
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Home" content="Everything you need to know about the system is located here: its functions, purpose, and why I've been struggling to finish this project haha">
    <link rel="stylesheet" type="text/css" href="/idealcozydesign/css/profile.css"/>
    <title>Profile</title>
</head>
<body>

<?php include("../partials/sidebar.php"); ?> 
<?php include("../partials/navbar.php"); ?> 
<div class="profile-container">

        <!-- Display success or error messages -->
        <?php
        if (isset($_SESSION['message'])) {
            $msg_type = $_SESSION['msg_type'] ?? 'info';
            echo "<div class='alert alert-$msg_type'>{$_SESSION['message']}</div>";
            unset($_SESSION['message'], $_SESSION['msg_type']);
        }
        ?>

<div class="form-container">
        <h1>Edit Profile</h1>
        <form action="update_profile.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">

            <!-- Full Name (editable) -->
            <label for="full_name">Full Name:</label>
            <div class="inputbox">
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <!-- Email (editable) -->
            <label for="email">Email:</label>
            <div class="inputbox">
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <!-- Contact Number (editable) -->
            <label for="contact_number">Contact Number:</label>
            <div class="inputbox">
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>">
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</div>
</body>
</html>