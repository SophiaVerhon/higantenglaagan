<?php
include('db_connect.php'); 
include('get_upcomingtours.php'); 

$upcoming_tours = get_upcoming_tours($conn); 
$currency_symbol = "₱";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <title>All Upcoming Tours</title>
    <link rel="stylesheet" href="css/admindashb.css">
    <style>
        .tour-details {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            display: block;
        }

        .tour-details h3 {
            margin: 0 0 10px;
        }

        .tour-details p {
            margin: 5px 0;
        }

        .tour-details .tour-left, .tour-details .tour-right {
            margin-bottom: 10px;
            display: block; 
        }

        .view-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .view-btn:hover {
            background-color: #45a049;
        }

        .view-booking-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .view-booking-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="admin">
    <div class="main-container">
        <header class="main-header">
            <div class="header-logo-text">
                <img src="image/logo.png" alt="Logo" class="logo-image">
                <span class="header-text">Higanteng Laagan Travel & Tours</span>
            </div>
            <nav class="header-navHP">
                <a href="admin_tour.php" class="nav-linkHP">TOURS</a>
                <a href="tour_add.php" class="nav-linkHP">+ADD NEW TOURS</a>
                <a href="admin_about.php" class="nav-linkHP">ABOUT US</a>
                <a href="review.php" class="nav-linkHP">REVIEW</a>
                <a href="admin_notifications.php" class="nav-linkHP">NOTIFICATION</a>

                <a href="admin_dashboard.php" class="nav-linkHP">DASHBOARD</a>
                <a href="logout.php" class="logout-button">LOGOUT</a>
                <!-- Notifications Badge in Navbar -->
            </nav>
        </header>

    <div class="container">
        <h2>Upcoming Tours</h2>

        <?php
        if ($upcoming_tours && $upcoming_tours->num_rows > 0) {
            while ($tour = $upcoming_tours->fetch_assoc()) {
                echo "<div class='tour-details'>";
                echo "<div class='tour-left'>";
                echo "<h3>" . htmlspecialchars($tour['tour_name']) . "</h3>";
                echo "<p><strong>Start Date:</strong> " . htmlspecialchars($tour['start_date']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($tour['description']) . "</p>";
                echo "<p><strong>Price per Person:</strong> " . $currency_symbol . number_format($tour['price_per_person'], 2) . "</p>";
                echo "</div>";

                echo "<div class='tour-right'>";
                echo "<p><strong>People Booked:</strong> " . $tour['total_booked'] . " people</p>";

                echo "<a href='tour_details.php?tour_id=" . $tour['tour_id'] . "' class='view-btn'>View Details</a>";

                echo "<a href='view_bookings.php?tour_id=" . $tour['tour_id'] . "' class='view-booking-btn'>View Bookings</a>";

                echo "</div>"; 
                
                echo "</div>"; 
            }
        } else {
            echo "<p>No upcoming tours available at the moment.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
