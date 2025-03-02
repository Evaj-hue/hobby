<?php
session_start();
include("../includes/db.php"); // Ensure the correct path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $contact_number = $_POST['contact_number'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        $sql = "UPDATE users SET username = :username, email = :email, full_name = :full_name, contact_number = :contact_number, role = :role, status = :status WHERE id = :user_id";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':full_name' => $full_name,
            ':contact_number' => $contact_number,
            ':role' => $role,
            ':status' => $status,
            ':user_id' => $user_id
        ])) {
            $_SESSION['message'] = "User updated successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update user.";
            $_SESSION['msg_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
}

// Redirect back to manage_roles.php
header("Location: manage_roles.php");
exit();
