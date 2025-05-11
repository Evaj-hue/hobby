<?php
session_start();
include('../includes/db.php'); // Ensure the correct path

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'user') {
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/idealcozydesign/css/profile.css">
    <link rel="stylesheet" href="/idealcozydesign/css/modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #253529; /* Consistent background color */
            color: white;
            font-family: Arial, sans-serif;
        }

        .content {
            margin-left: 260px;
            padding: 80px 20px 20px;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #354f52; /* Profile card dark green tone */
            padding: 20px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .profile-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-container p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .profile-container button {
            display: block;
            margin: 20px auto 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include("../partials/user_sidebar.php"); ?>
    <?php include("../partials/user_navbar.php"); ?>

    <div class="content">
        <div class="profile-container">
            <h1>Display Profile</h1>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number'] ?? 'N/A'); ?></p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #354f52; color: white;">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #253529; color: white;">
                    <form action="user_edit_profile.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <!-- Contact Number -->
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <div class="input-group">
                                <select class="form-select" name="country_code" required>
                                    <option value="+1" <?php echo str_starts_with($user['contact_number'], '+1') ? 'selected' : ''; ?>>+1 (USA)</option>
                                    <option value="+44" <?php echo str_starts_with($user['contact_number'], '+44') ? 'selected' : ''; ?>>+44 (UK)</option>
                                    <option value="+91" <?php echo str_starts_with($user['contact_number'], '+91') ? 'selected' : ''; ?>>+91 (India)</option>
                                    <option value="+61" <?php echo str_starts_with($user['contact_number'], '+61') ? 'selected' : ''; ?>>+61 (Australia)</option>
                                    <option value="+81" <?php echo str_starts_with($user['contact_number'], '+81') ? 'selected' : ''; ?>>+81 (Japan)</option>
                                    <option value="+63" <?php echo str_starts_with($user['contact_number'], '+63') ? 'selected' : ''; ?>>+63 (PH)</option>
                                </select>
                                <input type="text" class="form-control" id="contact_number" name="contact_number"
                                       placeholder="Enter your number (e.g., 9123456789)"
                                       value="<?php echo htmlspecialchars(str_replace(['+1', '+44', '+91', '+61', '+81', '+63'], '', $user['contact_number'])); ?>"
                                       pattern="\d+" title="Only numbers are allowed" maxlength="15" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>