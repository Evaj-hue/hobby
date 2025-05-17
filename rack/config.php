<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final"; // update to match your actual database name

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>