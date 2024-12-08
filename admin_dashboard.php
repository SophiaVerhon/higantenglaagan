<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

$total_customers_query = "SELECT COUNT(*) AS total_customers FROM customer";
$total_customers_result = $conn->query($total_customers_query);
$total_customers = $total_customers_result->fetch_assoc()['total_customers'];

$booking_list_query = "SELECT COUNT(*) AS total_bookings FROM booking";
$booking_list_result = $conn->query($booking_list_query);
$total_bookings = $booking_list_result->fetch_assoc()['total_bookings'];

$upcoming_tours_query = "SELECT COUNT(*) AS upcoming_tours FROM tour WHERE start_date > CURDATE()";
$upcoming_tours_result = $conn->query($upcoming_tours_query);
$upcoming_tours = $upcoming_tours_result->fetch_assoc()['upcoming_tours'];

$query = "SELECT COUNT(*) AS total_tours FROM tour";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_tours = $row['total_tours'];

// Query to get unread notifications count
$unread_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE is_read = 0";
$unread_result = $conn->query($unread_query);
$unread_count = $unread_result->fetch_assoc()['unread_count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admindb.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home.php">Home</a>
        <a href="admin_tour.php">Tours</a>
        <a href="admin_about.php">About Us</a>
        <a href="review.php">Review</a>
        <a href="tour_add.php">+ Add New Tour</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php" class="logout-button">Logout</a>

        <!-- Notifications Badge in Navbar -->
        <a href="admin_notifications.php" class="notification-badge">
            <?php if ($unread_count > 0): ?>
                <span class="badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Dashboard Statistics -->
    <div class="container">
        <h2>Welcome to the Admin Dashboard</h2>

        <!-- Notification Badge above the cards -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Total Customers</h3>
                <p><?php echo $total_customers; ?></p>
                <a href="admin_customer_list.php" class="view-customers-btn">View Customer List</a>
            </div>
            <div class="dashboard-card">
                <h3>Booking List</h3>
                <p><?php echo $total_bookings; ?></p>
                <a href="booking_list.php" class="view-customers-btn">View Booking List</a>
            </div>
            <div class="dashboard-card">
                <h3>Upcoming Tours</h3>
                <p><?php echo $upcoming_tours; ?></p>
                <!-- Notification Badge for Unread Notifications in Upcoming Tours -->
                <?php if ($unread_count > 0): ?>
                    <div class="notification-badge-upcoming">
                        <span class="badge"><?php echo $unread_count; ?> Unread Notifications</span>
                    </div>
                <?php endif; ?>
                <a href="upcoming_tours.php" class="view-customers-btn">View Upcoming Tours</a>
            </div>
            <div class="dashboard-card">
                <h3>Total Tours</h3>
                <p><?php echo $total_tours; ?></p>
            </div>
        </div>
    </div>

</body>
</html>
