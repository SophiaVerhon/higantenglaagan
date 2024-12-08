<?php
include('db_connect.php'); // Database connection

// Ensure session is started only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Get the tour_id from URL
$tour_id = $_GET['tour_id'];

// Fetch the tour name for the given tour_id
$tour_query = "SELECT tour_name FROM tour WHERE tour_id = ?";
$tour_name = "Unknown Tour"; // Default in case the query fails

if ($stmt = $conn->prepare($tour_query)) {
    $stmt->bind_param('i', $tour_id); // Bind the tour_id
    $stmt->execute();
    $stmt->bind_result($fetched_tour_name);
    if ($stmt->fetch()) {
        $tour_name = $fetched_tour_name;
    }
    $stmt->close();
}

// Prepare the query to get bookings for the specific tour
$query = "
    SELECT
        customer.customer_id,
        customer.name AS customer_name,
        booking.booking_id,
        booking.booking_date,
        customer.valid_id_path
    FROM booking
    JOIN customer ON booking.customer_id = customer.customer_id
    WHERE booking.tour_id = ?
";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $tour_id); // Bind the tour_id
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link rel="stylesheet" href="css/view_bookings.css">
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <div class="back-button">
            <a href="upcoming_tours.php" class="back-link">⟵ Back to Upcoming Tours</a>
        </div>

        <!-- Page Header -->
        <h1 class="header">Bookings for Tour: <?php echo htmlspecialchars($tour_name); ?></h1>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <div class="table-container">
                <div class="table-header">
                    <div class="table-column">Customer Name</div>
                    <div class="table-column">Booking Date</div>
                    <div class="table-column">Valid ID</div>
                </div>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="table-row">
                        <div class="table-column">
                            <?php echo htmlspecialchars($row['customer_name']); ?>
                        </div>
                        <div class="table-column">
                            <?php echo htmlspecialchars($row['booking_date']); ?>
                        </div>
                        <div class="table-column">
                            <?php if (!empty($row['valid_id_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['valid_id_path']); ?>" alt="Valid ID" class="valid-id-img">
                            <?php else: ?>
                                <span>No ID Provided</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-bookings">No bookings for this tour.</p>
        <?php endif; ?>

        <!-- Close the database connection -->
        <?php $conn->close(); ?>
    </div>
</body>
</html>
