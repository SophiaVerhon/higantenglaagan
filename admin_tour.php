<?php
include('db_connect.php');
session_start();
$currency_symbol = "₱";

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Query for upcoming tours
$query = "SELECT tour_id, tour_name, description, start_date, end_date, price_per_person, location, image_path 
          FROM tour 
          WHERE start_date > NOW() 
          ORDER BY start_date ASC";

$result = $conn->query($query);

// Debugging: Check if the query works and how many results are fetched
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($result->num_rows === 0) {
    echo "<p>No upcoming tours available.</p>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tour List</title>
    <link rel="stylesheet" href="css/adtour2.css"> <!-- Link to the specific CSS file -->
</head>
<body>
<?php
if (isset($_GET['msg'])) {
    echo "<div class='message-box'>" . htmlspecialchars($_GET['msg']) . "</div>";
}
?>
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
    <h2>Available Tours</h2>
    <div class="tour-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="tour-item">
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Tour Image">
                    <?php else: ?>
                        <p>Image not available</p>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($row['tour_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Price per Person:</strong> <?php echo $currency_symbol . number_format($row['price_per_person'], 2); ?></p>
                    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>
                    <p><strong>End Date:</strong> <?php echo htmlspecialchars($row['end_date']); ?></p>
                    
                    <!-- Edit and Delete buttons -->
                    <a href="touredit.php?id=<?php echo $row['tour_id']; ?>" class="edit-button">Edit</a>
                    <a href="tour_delete.php?tour_id=<?php echo $row['tour_id']; ?>" 
                       class="delete-button" 
                       onclick="return confirm('Are you sure you want to delete this tour?');">Delete</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No upcoming tours available.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
