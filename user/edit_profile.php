<?php
session_start();
include('../includes/db.php'); // Ensure the correct path

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !in_array($_SESSION['user']['role'], ['user'])) {
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
    header("Location: dashboard.php"); // Redirect if user not found
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Home" content="USER PROFILE EDIT PART ">
    <link rel="stylesheet" type="text/css" href="/idealcozydesign/css/profile.css"/>
    <title> Profile</title>
</head>
<body>

<?php include("../partials/user_navbar.php"); ?> <!-- Sidebar included -->
<?php include("../partials/user_sidebar.php"); ?> <!-- Sidebar included -->
<div class="profile-container">

<?php
if (isset($_SESSION['message'])) {
    $msg_type = $_SESSION['msg_type'] ?? 'info';
    $message = $_SESSION['message'];

    // Unset the session variables to prevent repeated alerts
    unset($_SESSION['message'], $_SESSION['msg_type']);

    echo "<script>
            alert('$message');
          </script>";
}
?>


<div class="form-container">
        <h1>Edit Profile</h1>
        <form action="user_edit_profile.php" method="POST">
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