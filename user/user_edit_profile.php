<?php
session_start();
include('../includes/db.php'); // Ensure the correct path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_POST['user_id'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];

        // Prepare the SQL query to update only editable fields (excluding username and role)
        $sql = "UPDATE users SET full_name = :full_name, email = :email, contact_number = :contact_number WHERE id = :user_id";
        $stmt = $conn->prepare($sql);
        
        // Execute the statement with the provided data
        if ($stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':contact_number' => $contact_number,
            ':user_id' => $user_id
        ])) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update profile.";
            $_SESSION['msg_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
}

// Redirect back to the profile page
header("Location: edit_profile.php");
exit();
