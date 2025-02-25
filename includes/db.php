<?php
$host = 'localhost';
$dbname = 'user_management';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password); // ✅ Fixed "mysql" typo
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Ensure that the session always contains the correct user info (role, etc.)
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];

    // Fetch the latest user data from the database to ensure the role is up to date
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :user_id");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user exists, update session role
    if ($user) {
        $_SESSION['user']['role'] = $user['role']; // Update the session with the latest role
    }
}

?>
