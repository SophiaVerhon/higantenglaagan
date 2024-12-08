<?php
include('db_connect.php'); // Include database connection

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Mark all notifications as read
if (isset($_GET['mark_all_read'])) {
    $update_query = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
    $conn->query($update_query);
    header("Location: admin_notifications.php"); // Redirect back to the notifications page
    exit();
}

// Fetch notifications from the database, joining with the tour table to get the tour name
$notifications_query = "
    SELECT 
        notifications.notification_id, 
        notifications.message, 
        notifications.created_at, 
        notifications.is_read, 
        IFNULL(tour.tour_name, 'No tour name') AS tour_name
    FROM 
        notifications 
    LEFT JOIN 
        tour 
    ON 
        notifications.tour_id = tour.tour_id 
    ORDER BY 
        notifications.created_at DESC";
$result = $conn->query($notifications_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="css/admindb.css">
</head>
<body>
<div class="navbar">
    <a href="admin_home.php">Home</a>
    <a href="admin_tour.php">Tours</a>
    <a href="admin_about.php">About Us</a>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

<div class="container">
    <h2>Notifications</h2>
    <a href="admin_notifications.php?mark_all_read=true" class="mark-read-btn">Mark All as Read</a>
    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
            <?php while ($notification = $result->fetch_assoc()): ?>
                <li class="<?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                    <?php 
                        // Replace {tour_name} in the message with the actual tour name
                        $message = $notification['message'];
                        if (strpos($message, '{tour_name}') !== false && !empty($notification['tour_name'])) {
                            $message = str_replace('{tour_name}', $notification['tour_name'], $message);
                        }
                        echo htmlspecialchars($message); 
                    ?>
                    <span class="timestamp"><?php echo $notification['created_at']; ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No notifications at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>
