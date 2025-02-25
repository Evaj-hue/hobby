<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            echo "<script>alert('Login successful! Redirecting to Admin Dashboard.'); window.location.href='admin_dashboard.php';</script>";
        } else {
            echo "<script>alert('Login successful! Redirecting to User Dashboard.'); window.location.href='dashboard.php';</script>";
        }
    } else {
        // Show an alert for invalid username or password and go back
        echo "<script>alert('Invalid username or password! Please try again.'); window.history.back();</script>";
    }
}
?>
