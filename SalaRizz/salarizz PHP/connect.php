<?php

$servername = "localhost"; // Change this to your MySQL server's hostname if it's different
$username = "root"; // Replace 'your_username' with your actual MySQL username
$password = ""; // Replace 'your_password' with your actual MySQL password
$database = "salarizz"; // Replace 'salarizz' with your actual database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage(); // This will be printed if there's an error connecting to the database
}
?>
