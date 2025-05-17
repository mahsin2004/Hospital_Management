<?php
// Database connection settings
$host = 'localhost';
$dbname = 'hospital_db'; // Your database name
$username = 'root'; // Default MySQL username
$password = ''; // Default MySQL password for XAMPP is empty

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
