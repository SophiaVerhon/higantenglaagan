<?php
include('../db_connect.php'); // Database connection
session_start();
$currency_symbol = "₱"; // Currency symbol

// Query to get all tours from the database
$query = "SELECT tour_id, tour_name, description, location, price_per_person, start_date, end_date, image_path FROM tour";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours - User Page</title>
    <link rel="stylesheet" href="usertour.css"> <!-- Link to user tour CSS -->
</head>
<body>
<div class="navbar">
    <a href="home.php">Home</a>
    <a href="user_tour.php">Tour</a>
    <a href="about.php">About Us</a>
    <a href="reviews.php">Reviews</a>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

<div class="container">
    <h2>Explore Our Tours</h2>
    <div class="tour-list">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="tour-item">
                <!-- Displaying tour image if available -->
                <?php 
                $image_path = $row['image_path'];
                if (!empty($image_path)) {
                    $image_src = "../" . $image_path; // Ensure the path points to the uploads folder
                } else {
                    $image_src = "../uploads/default.jpg"; // Default image if none exists
                }
                ?>
                <img src="<?php echo $image_src; ?>" alt="Tour Image" class="tour-image">

                <!-- Displaying tour details -->
                <h3><?php echo htmlspecialchars($row['tour_name']); ?></h3>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Price:</strong> <?php echo $currency_symbol . number_format($row['price_per_person'], 2); ?></p>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($row['end_date']); ?></p>
                <p><strong>Note: </strong><?php echo htmlspecialchars($row['description']); ?></p>

                <!-- Book Now button (optional) -->
                <a href="../customer_form.php?tour_id=<?php echo $row['tour_id']; ?>" class="book-now-button">Book Now</a>
             
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
