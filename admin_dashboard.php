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
    <link rel="stylesheet" href="css/admindashb.css">
</head>
<body class="admin">
    <div class="main-container">
        <header class="main-header">
            <div class="header-logo-text">
                <img src="image/logo.png" alt="Logo" class="logo-image">
                <span class="header-text">Higanteng Laagan Travel & Tours</span>
            </div>
            <nav class="header-navHP">
                <a href="admin_home.php" class="nav-linkHP">HOME </a>
                <a href="admin_tour.php" class="nav-linkHP">TOURS</a>
                <a href="tour_add.php" class="nav-linkHP">+ADD NEW TOURS</a>
                <a href="admin_about.php" class="nav-linkHP">ABOUT US</a>
                <a href="review.php" class="nav-linkHP">REVIEW</a>
                <a href="admin_dashboard.php" class="nav-linkHP">DASHBOARD</a>
                <a href="logout.php" class="logout-button">LOGOUT</a>
                <a href="admin_notifications.php" class="notification-badge">
                    <?php if ($unread_count > 0): ?>
                        <span class="badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </header>

        <div class="container">
            <h2>Welcome to the Admin Dashboard</h2>

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

                <div class="dashboard-card">
                    <h3>Payment List</h3>
                    <?php if ($payment_result->num_rows > 0): ?>
                        <div class="payment-list">
                            <ul>
                                <?php while ($payment = $payment_result->fetch_assoc()): ?>
                                    <li>
                                        <p>Payment ID: <?php echo $payment['payment_id']; ?> | Customer ID: <?php echo $payment['customer_id']; ?> | Amount: $<?php echo number_format($payment['amount'], 2); ?> | Date: <?php echo $payment['payment_date']; ?> | Status: <?php echo ucfirst($payment['status']); ?></p>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <p>No payments found.</p>
                    <?php endif; ?>
                    <a href="admin_payments.php" class="view-customers-btn">View All Payments</a>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
