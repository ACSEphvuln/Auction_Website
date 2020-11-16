<?php 
// File used to connect to the database
$host = 'mysql';
$user = 'root';
$pass = 'rootpassword';
$db_name = 'myDb';
$conn = new mysqli($host, $user, $pass, $db_name);

 // Notice: for debugging reasons, this will be always printed before shipped to production
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


  ?>
