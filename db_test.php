<?php

include('db_connect.php');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>
