<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <title>User Dashboard</title>
</head>
<body>
    <?php include("partials/user_navbar.php"); ?> <!-- Sidebar included -->
    <?php include("partials/user_sidebar.php"); ?> <!-- Sidebar included -->
</body>
</html>