<?php
session_start();
include('../db_connect.php'); 
$currency_symbol = "₱";

$sql = "
    SELECT t.tour_id, t.tour_name, t.image_path, t.price_per_person, t.max_bookings, COUNT(b.booking_id) AS booking_count
    FROM tour t
    LEFT JOIN booking b ON t.tour_id = b.tour_id
    WHERE t.start_date > NOW()  -- Only upcoming tours
    GROUP BY t.tour_id
    ORDER BY booking_count DESC
    LIMIT 4;
";
$result = $conn->query($sql);

$popular_tours = [];
if ($result && $result->num_rows > 0) {
    $popular_tours = $result->fetch_all(MYSQLI_ASSOC);
}

$upcoming_query = "
    SELECT t.tour_id, t.tour_name, t.description, t.start_date, t.end_date, t.price_per_person, t.location, t.image_path, t.max_bookings, COUNT(b.booking_id) AS booking_count 
    FROM tour t
    LEFT JOIN booking b ON t.tour_id = b.tour_id
    WHERE t.start_date > NOW()  -- Only upcoming tours
    GROUP BY t.tour_id
    ORDER BY t.start_date ASC
    LIMIT 4"; 
$upcoming_result = $conn->query($upcoming_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>USERhomepage</title>
</head>
<body class="userhomepage">
    <div class="main-container">
        <header class="main-header">
            <div class="header-logo-text">
                <img src="image/logo.png" alt="Logo" class="logo-image">
                <span class="header-text">Higanteng Laagan Travel & Tours</span>
            </div>
            <nav class="header-navHP">
                <a href="#" class="nav-linkHP">MOST POPULAR</a>
                <a href="#tours" class="nav-linkHP">ALL TOURS</a>
                <a href="#tours" class="nav-linkHP">SEARCH</a>
                <a href="aboutus.php" class="nav-linkHP">ABOUT US</a>
                <div class="dropdown">
                    <span class="nav-linkHP dropdown-toggle" onclick="toggleDropdown('profile-dropdown')">MY PROFILE</span>
                    <div id="profile-dropdown" class="dropdown-menu">
                        <a href="profile.php" class="dropdown-item">My Account</a>
                        <a href="bkstatus.php" class="dropdown-item">Booking Status</a>
                        <a href="logout.php" class="dropdown-item">Log Out</a>
                    </div>
                </div>
            </nav>
        </header>

        <div class="gaptop">
            <p><br></p>
        </div>

        <section id="popular" class="tours-section2">
            <div class="section-header">
                <div class="left-header">
                    <p class="section-title">MOST POPULAR TOURS</p>
                </div>
            </div>
            <div class="scroll-container">
                <?php if (!empty($popular_tours)): ?>
                    <?php foreach ($popular_tours as $tour): ?>
                        <?php
                        if ($tour['max_bookings'] == 0 || is_null($tour['max_bookings'])) {
                            $available_slots = 'Open Slots';  // Display "Open Slots" if no booking limit is set
                            $is_bookable = true;
                        } else {
                            $available_slots = $tour['max_bookings'] - $tour['booking_count'];
                            $is_bookable = ($available_slots > 0);
                            $available_slots = ($available_slots > 0) ? $available_slots : 'No available slots';
                        }
                        ?>
                        <div class="rectangle" onclick="window.location.href='../tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
                            <div class="image-container">
                                <?php
                                // Output image from BLOB
                                if (!empty($tour['image_path'])):
                                    echo '<img src="data:image/jpeg;base64,' . base64_encode($tour['image_path']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="tour-image">';
                                else:
                                    echo "<p>Image not available</p>";
                                endif;
                                ?>
                            </div>
                            <div class="tour-details">
                                <p class="tour-title2"><?php echo htmlspecialchars($tour['tour_name']); ?></p>
                                <p><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?> per person</p>
                                <p><strong>Available Slots: <?php echo $available_slots; ?></strong></p>
                            </div>
                            <?php if ($is_bookable): ?>
                                <a href="../customer_form.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button book-now-button">Book Now</a>
                            <?php else: ?>
                                <a href="#" class="tour-button book-now-button" disabled>Fully Booked</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No popular tours available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
        <section id="upcoming" class="tours-section2">
            <div class="section-header">
                <div class="left-header">
                    <p class="section-title">UPCOMING TOURS</p>
                </div>
            </div>
            <div class="scroll-container">
                <?php if ($upcoming_result->num_rows > 0): ?>
                    <?php while ($tour = $upcoming_result->fetch_assoc()): ?>
                        <?php
                        if ($tour['max_bookings'] == 0 || is_null($tour['max_bookings'])) {
                            $available_slots = 'Open Slots';  // Display "Open Slots" if no booking limit is set
                        } else {
                            $available_slots = $tour['max_bookings'] - $tour['booking_count'];
                            $available_slots = ($available_slots > 0) ? $available_slots : 'No available slots';
                        }
                        ?>
                        <div class="rectangle" onclick="window.location.href='../tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
                            <div class="image-container">
                                <?php
                                // Output image from BLOB
                                if (!empty($tour['image_path'])):
                                    echo '<img src="data:image/jpeg;base64,' . base64_encode($tour['image_path']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="tour-image">';
                                else:
                                    echo "<p>Image not available</p>";
                                endif;
                                ?>
                            </div>
                            <div class="tour-details">
                                <p class="tour-title2"><?php echo htmlspecialchars($tour['tour_name']); ?></p>
                                <p><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?> per person</p>
                                <p><strong>Available Slots: <?php echo $available_slots; ?></strong></p>
                            </div>
                            
                            <?php if ($is_bookable): ?>
                                <a href="../customer_form.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button book-now-button">Book Now</a>
                            <?php else: ?>
                                <a href="#" class="tour-button book-now-button" disabled>Fully Booked</a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No upcoming tours available at the moment.</p>
                <?php endif; ?>
            </div>
            <section id="exclusive" class="tours-section2">
    <div class="section-header">
        <div class="left-header">
            <p class="section-title">EXCLUSIVE TOURS</p>
        </div>
    </div>
    <div class="scroll-container">
        <?php
      
        $exclusive_query = "
            SELECT t.tour_id, t.tour_name, t.description, t.start_date, t.end_date, 
                   t.price_per_person, t.location, t.image_path, t.max_bookings, 
                   COUNT(b.booking_id) AS booking_count 
            FROM tour t
            LEFT JOIN booking b ON t.tour_id = b.tour_id
            WHERE t.is_exclusive = 1  -- Fetch only exclusive tours
            GROUP BY t.tour_id
            ORDER BY t.start_date ASC";

        $exclusive_result = $conn->query($exclusive_query);
        $exclusive_tours = [];

        if ($exclusive_result && $exclusive_result->num_rows > 0) {
            while ($tour = $exclusive_result->fetch_assoc()) {
                $exclusive_tours[] = $tour;
            }
        }
        ?>

        <?php if (!empty($exclusive_tours)): ?>
            <?php foreach ($exclusive_tours as $tour): ?>
                <div class="rectangle" onclick="window.location.href='../tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
                    <div class="image-container">
                        <?php
                        if (!empty($tour['image_path'])):
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($tour['image_path']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="tour-image">';
                        else:
                            echo "<p>Image not available</p>";
                        endif;
                        ?>
                    </div>
                    <div class="tour-details">
                        <p class="tour-title2"><?php echo htmlspecialchars($tour['tour_name']); ?></p>
                        <p><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?> per person</p>
                    </div>
                    <a href="../customer_form.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button">Book Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No exclusive tours available at the moment.</p>
        <?php endif; ?>
    </div>
</section>


        <section id="tours" class="tours-section2">
            <div class="section-header">
                <div class="left-header">
                    <p class="section-title">ALL TOURS</p>
                </div>
                <div class="search-wrapper">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search...">
                        <button class="search-button">
                            <img src="image/searchicon.png" alt="Search" class="search-icon">
                        </button>
                        <div class="filter-buttons">
                            <button class="filter-button" data-category="all">All Tours</button>
                            <button class="filter-button" data-category="minor-hike">Minor Dayhike</button>
                            <button class="filter-button" data-category="major-hike">Major Dayhike</button>
                            <button class="filter-button" data-category="trekking">Trekking</button>
                            <button class="filter-button" data-category="overnight">Overnight</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="scroll-container">
                <?php
                $allToursQuery = "
                    SELECT t.tour_id, t.tour_name, t.image_path, t.price_per_person, t.max_bookings, COUNT(b.booking_id) AS booking_count
                        FROM tour t
                         LEFT JOIN booking b ON t.tour_id = b.tour_id
                            WHERE t.start_date > NOW()  -- Only upcoming tours
                             GROUP BY t.tour_id
                                ORDER BY t.start_date ASC";
                $allToursResult = $conn->query($allToursQuery);

                if ($allToursResult->num_rows > 0):
                    while ($tour = $allToursResult->fetch_assoc()):
                        // Check if max_booking is NULL or 0
                        if ($tour['max_bookings'] == 0 || is_null($tour['max_bookings'])) {
                            $available_slots = 'Open Slots';  // Display "Open Slots" if no booking limit is set
                        } else {
                            $available_slots = $tour['max_bookings'] - $tour['booking_count'];
                            $available_slots = ($available_slots > 0) ? $available_slots : 'No available slots';
                        }
                        ?>
                        <div class="rectangle" onclick="window.location.href='../tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
                            <div class="image-container">
                                <?php
                                // Output image from BLOB
                                if (!empty($tour['image_path'])):
                                    echo '<img src="data:image/jpeg;base64,' . base64_encode($tour['image_path']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="tour-image">';
                                else:
                                    echo "<p>Image not available</p>";
                                endif;
                                ?>
                            </div>
                            <div class="tour-details">
                                <p class="tour-title2"><?php echo htmlspecialchars($tour['tour_name']); ?></p>
                                <p><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?> per person</p>
                                <p><strong>Available Slots: <?php echo $available_slots; ?></strong></p>
                            </div>
                            <a href="../customer_form.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button">Book Now</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No tours available.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

        <footer id="about-us-footer">
            <div class="footerContainer">
                <div class="socialIcons">
                    <a href=""><i class="fa-brands fa-facebook"></i></a>
                    <a href=""><i class="fa-brands fa-instagram"></i></a>
                    <a href=""><i class="fa-brands fa-twitter"></i></a>
                    <a href=""><i class="fa-brands fa-youtube"></i></a>
                </div>
                <div class="footerNav">
                    <ul><li><a href="">Home</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footerBottom">
                <p>Copyright &copy;2024; Designed by <span class="designer">CASSanga</span></p>
            </div>
        </footer>

    </div>

    <!-- JavaScript for dropdown -->
    <script>
        function toggleDropdown(id) {
            var dropdown = document.getElementById(id);
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>

</body>
</html>

<?php $conn->close(); ?>
