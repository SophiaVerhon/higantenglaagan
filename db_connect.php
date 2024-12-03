<?php
// db_connect.php

// Database credentials
$servername = "localhost";  // Typically localhost if running locally
$username = "root";         // Database username (default for XAMPP)
$password = "";             // Database password (default is empty for XAMPP)
$dbname = "higante_db";     // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to utf8 for proper encoding
$conn->set_charset("utf8");

// Optionally, print a message to confirm connection success
// echo "Connected successfully";

?>
