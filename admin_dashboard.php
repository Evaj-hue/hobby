<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Home" content="Everything you need to know about the system is located here: its functions, purpose, 
    and why ive been struggling to finish this project haha ">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <title>admin side</title>
</head>
<body>
<?php include("partials/sidebar.php"); ?> <!-- Sidebar included -->
<?php include("partials/navbar.php"); ?> <!-- Navbar included -->

           
    <main>
         

    </main>
    
</body>
</html>