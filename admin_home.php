<?php
session_start();
include("db_connect.php"); 
$currency_symbol = "₱";

// Fetch most popular tours
$sql = "
    SELECT t.tour_id, t.tour_name, t.image_path, t.price_per_person, COUNT(b.booking_id) AS booking_count
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

// Fetch upcoming tours
$upcoming_query = "
    SELECT t.tour_id, t.tour_name, t.description, t.start_date, t.end_date, t.price_per_person, t.location, t.image_path 
    FROM tour t
    WHERE t.start_date > NOW()  -- Only upcoming tours
    ORDER BY t.start_date ASC
    LIMIT 4"; 
$upcoming_result = $conn->query($upcoming_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="users/style1.css">
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
                <a href="admin_tour.php" class="nav-linkHP">ALL TOURS</a>
                <a href="#tours" class="nav-linkHP">SEARCH</a>
                <a href="#about-us-footer" class="nav-linkHP">ABOUT US</a>
                <a href="tour_add.php" class="nav-linkHP">+Add New Tour</a>
                <a href="logout.php " class="nav-linkHP">Logout</a>
                
                
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
                        <div class="rectangle" onclick="window.location.href='tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
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
                            </div>
                            <a href="tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button">Book Now</a>
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
                            </div>
                            <a href="tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button">Book Now</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No upcoming tours available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- All Upcoming Tours Section -->
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
                    <button class="filter-button" data-category="day-tour">Day Tour</button>
                    <button class="filter-button" data-category="2d1n-adventure">2D1N Adventure</button>
                    <button class="filter-button" data-category="3d2n-adventure">3D2N Adventure</button>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-container">
        <?php
        // Updated query to fetch only upcoming tours (start_date > NOW())
        $allToursQuery = "
            SELECT t.tour_id, t.tour_name, t.description, t.start_date, t.end_date, t.price_per_person, t.location, t.image_path
            FROM tour t
            WHERE t.start_date > NOW()  -- Only upcoming tours
            ORDER BY t.start_date ASC";  
        $allToursResult = $conn->query($allToursQuery);
        
        if ($allToursResult->num_rows > 0) {
            while ($tour = $allToursResult->fetch_assoc()) {
                echo "<div class='rectangle'>";
                echo "<div class='image-container'>";
                // Output image from BLOB (checking for image availability)
                if (!empty($tour['image_path'])):
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($tour['image_path']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="tour-image">';
                else:
                    echo "<p>Image not available</p>";
                endif;
                echo "</div>";
                echo "<div class='tour-details'>";
                echo "<p class='tour-title2'>" . htmlspecialchars($tour['tour_name']) . "</p>";
                echo "<p>" . $currency_symbol . htmlspecialchars($tour['price_per_person']) . " per person</p>";
                echo "</div>";
                echo "<a href='tour_details.php?tour_id=" . htmlspecialchars($tour['tour_id']) . "' class='tour-button'>View Details</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No upcoming tours available.</p>";
        }
        ?>
    </div>
</section>

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
                        <li><a href="">About Us</a></li>
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
