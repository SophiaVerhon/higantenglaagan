<?php
include('db_connect.php');

session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
   
    header("Location: admin_log.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="css/admin_dashboard_style.css"> 
</head>
<body>

    
    <div class="navbar">
        <a href="admin_home.php">Home</a>
        <a href="admin_tour.php">Tours</a>
        <a href="admin_about.php">About Us</a>
        <a href="review.php">Review</a>
        <a href="tour_add.php">+ Add New Tour</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php" class="logout-button">Logout</a>
        
    </div>

  
    <div class="container">
        <h2>Welcome to Admin Homepage</h2>
        <p></p>
        
    </div>

</body>
</html>
