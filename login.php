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

    if (!$user) {
        // User not found in database
        echo "<script>alert('User not found! Please check your username and try again.'); window.history.back();</script>";
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'admin') {
            echo "<script>alert('Login successful! Redirecting to Admin Dashboard.'); window.location.href='admin/admin_dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Login successful! Redirecting to User Dashboard.'); window.location.href='user/dashboard.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Incorrect password! Please try again.'); window.history.back();</script>";
        exit;
    }
}
?>
