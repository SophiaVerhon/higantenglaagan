<?php
include('db_connect.php');
session_start();
$currency_symbol = "₱";

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Query for upcoming tours, including the count of bookings for each tour and associated tour guides
$query = "
    SELECT t.tour_id, t.tour_name, t.description, t.start_date, t.end_date, t.price_per_person, t.location, t.image_path, t.max_bookings,
           (SELECT COUNT(*) FROM booking b WHERE b.tour_id = t.tour_id) AS current_bookings,
           GROUP_CONCAT(tg.name ORDER BY tg.name ASC) AS guide_names, 
           GROUP_CONCAT(tg.contact_no ORDER BY tg.name ASC) AS guide_contact_nos
    FROM tour t
    LEFT JOIN tour_guide_assignment tga ON tga.tour_id = t.tour_id
    LEFT JOIN tourguide tg ON tg.guide_id = tga.guide_id
    WHERE t.start_date > NOW()
    GROUP BY t.tour_id
    ORDER BY t.start_date ASC
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
$query_exclusive = "
SELECT t.tour_id, t.tour_name, t.description, t.start_date, t.end_date, t.price_per_person, t.location, t.image_path, t.max_bookings, t.min_bookings, t.is_exclusive,
       (SELECT COUNT(*) FROM booking b WHERE b.tour_id = t.tour_id) AS current_bookings,
       GROUP_CONCAT(tg.name ORDER BY tg.name ASC) AS guide_names, 
       GROUP_CONCAT(tg.contact_no ORDER BY tg.name ASC) AS guide_contact_nos
FROM tour t
LEFT JOIN tour_guide_assignment tga ON tga.tour_id = t.tour_id
LEFT JOIN tourguide tg ON tg.guide_id = tga.guide_id
WHERE t.is_exclusive = 1
GROUP BY t.tour_id
ORDER BY t.start_date ASC
";

$result_exclusive = $conn->query($query_exclusive);

// Check if the query was successful
if ($result_exclusive === false) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tour List</title>
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/addtours_2_style.css"> <!-- Link to the specific CSS file -->
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
                <!-- Notifications Badge in Navbar -->
            </nav>
        </header>

<div class="container">
    <h2>Exclusive Tours</h2>
    <div class="tour-list">
    <?php
    if ($result_exclusive->num_rows > 0) {
        while ($row = $result_exclusive->fetch_assoc()) {
            // Fetch the tour details
            $tour_name = $row['tour_name'];
            $description = $row['description'];
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
            $price_per_person = $row['price_per_person'];
            $location = $row['location'];
    ?>
   <div class="tour-item">
    <?php if (!empty($row['image_path'])): ?>
        <?php 
            // Convert BLOB data to base64
            $imageData = base64_encode($row['image_path']);
            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Tour Image" />';
        ?>
    <?php else: ?>
        <p>Image not available</p>
    <?php endif; ?>

    <h3><?php echo htmlspecialchars($row['tour_name']); ?></h3>
    <p><?php echo htmlspecialchars($row['description']); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
    <p><strong>Price per Person:</strong> <?php echo $currency_symbol . number_format($row['price_per_person'], 2); ?></p>
    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>
    <p><strong>End Date:</strong> <?php echo htmlspecialchars($row['end_date']); ?></p>

    <!-- Display the minimum bookings for exclusive tours -->
    <?php if ($row['is_exclusive'] == 1 && !empty($row['min_bookings'])): ?>
        <p><strong>Minimum Bookings:</strong> <?php echo htmlspecialchars($row['min_bookings']); ?></p>
    <?php endif; ?>
    
    <!-- Display guide names -->
    <p><strong>Tour Guides:</strong> 
        <?php 
            if (!empty($row['guide_names'])) {
                echo htmlspecialchars($row['guide_names']); 
            } else {
                echo "No guides assigned yet";
            }
        ?>
    </p>

    <!-- Display guide contact numbers -->
    <p><strong>Guide Contact Numbers:</strong> 
        <?php 
            if (!empty($row['guide_contact_nos'])) {
                echo htmlspecialchars($row['guide_contact_nos']); 
            } else {
                echo "No contact information available";
            }
        ?>
    </p>

    <!-- Admin-specific features: Edit, Delete -->
    <a href="touredit.php?id=<?php echo $row['tour_id']; ?>" class="edit-button">Edit</a>
    <button class="delete-button" data-id="<?php echo $row['tour_id']; ?>">Delete</button>
</div>
    <?php
        }
    } else {
        echo "<p>No exclusive tours available.</p>";
    }
    ?>
    </div>
</div>
<div class="container">
    <h2>Available Tours</h2>
    <div class="tour-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="tour-item">
                    <?php if (!empty($row['image_path'])): ?>
                        <?php 
                            // Convert BLOB data to base64
                            $imageData = base64_encode($row['image_path']);
                            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Tour Image" />';
                        ?>
                    <?php else: ?>
                        <p>Image not available</p>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($row['tour_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Price per Person:</strong> <?php echo $currency_symbol . number_format($row['price_per_person'], 2); ?></p>
                    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>
                    <p><strong>End Date:</strong> <?php echo htmlspecialchars($row['end_date']); ?></p>
                    
                    <!-- Display guide names -->
                    <p><strong>Tour Guides:</strong> 
                        <?php 
                            if (!empty($row['guide_names'])) {
                                echo htmlspecialchars($row['guide_names']); 
                            } else {
                                echo "No guides assigned yet";
                            }
                        ?>
                    </p>

                    <!-- Display guide contact numbers -->
                    <p><strong>Guide Contact Numbers:</strong> 
                        <?php 
                            if (!empty($row['guide_contact_nos'])) {
                                echo htmlspecialchars($row['guide_contact_nos']); 
                            } else {
                                echo "No contact information available";
                            }
                        ?>
                    </p>

                    <!-- Display booking status based on admin/user -->
                    <?php if ($_SESSION['admin_logged_in'] === true): ?>
                        <!-- Admin sees available slots -->
                        <?php 
                            if ($row['max_bookings'] > 0) {
                                $availableSlots = $row['max_bookings'] - $row['current_bookings'];
                                if ($row['current_bookings'] >= $row['max_bookings']) {
                                    echo "<p><strong>Status:</strong> Fully Booked</p>";
                                } else {
                                    echo "<p><strong>Status:</strong> $availableSlots slots available</p>";
                                }
                            } else {
                                echo "<p><strong>Status:</strong> No Booking Limit Set</p>";
                            }
                        ?>
                    <?php else: ?>
                        <!-- User sees booking status -->
                        <?php 
                            if ($row['max_bookings'] > 0 && $row['current_bookings'] >= $row['max_bookings']) {
                                echo "<p><strong>Status:</strong> Fully Booked</p>";
                            } elseif ($row['max_bookings'] == 0) {
                                echo "<p><strong>Status:</strong> No Booking Limit Set</p>";
                            } else {
                                echo "<p><strong>Status:</strong> Available</p>";
                            }
                        ?>
                    <?php endif; ?>

                    <a href="touredit.php?id=<?php echo $row['tour_id']; ?>" class="edit-button">Edit</a>
                    <button class="delete-button" data-id="<?php echo $row['tour_id']; ?>">Delete</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No upcoming tours available.</p>
        <?php endif; ?>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete this tour?</p>
        <button id="cancelButton" class="cancel-btn">Cancel</button>
        <button id="confirmButton" class="confirm-btn">Delete</button>
    </div>
</div>

<script src="js/delete_modal.js"></script>
</body>
</html>

<?php $conn->close(); ?>
