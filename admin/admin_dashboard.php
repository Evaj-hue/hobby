<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Home" content="Everything you need to know about the system is located here: its functions, purpose, and why I've been struggling to finish this project haha">
    <link rel="stylesheet" type="text/css" href="/idealcozydesign/css/style.css"/>
    <title>Admin Side</title>
</head>
<body>

<?php include("../partials/sidebar.php"); ?> 
<?php include("../partials/navbar.php"); ?>


<main>

</main>

</body>
</html>
