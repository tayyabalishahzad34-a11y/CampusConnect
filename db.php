<?php
$host = "localhost";
$user = "campususer";
$pass = "campus123"; 
$dbname = "campusconnect";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>