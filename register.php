<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $contact_number = $_POST['contact_number'];

    // Validate password with regex pattern
    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*:;<>,.?~`-]).{8,}$/', $_POST['password'])) {
        die("<script>alert('Password does not meet requirements'); window.history.back();</script>");
    }

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetchColumn();

    if ($result > 0) {
        // Show an alert message and go back to the form
        die("<script>alert('Username already exists! Please choose another.'); window.history.back();</script>");
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, contact_number) 
                                VALUES (:username, :email, :password, :full_name, :contact_number)");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':contact_number', $contact_number);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('Registration failed! Please try again.'); window.history.back();</script>";
        }
    }
}
?>
