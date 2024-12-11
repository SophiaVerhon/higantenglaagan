<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
include('../db_connect.php'); // Include your database connection file

$currency_symbol = "₱";

// Fetch most popular tours
$sql = "
    SELECT t.tour_id, t.tour_name, t.image_path, t.price_per_person, COUNT(b.booking_id) AS booking_count
    FROM tour t
    LEFT JOIN booking b ON t.tour_id = b.tour_id
    WHERE t.start_date > NOW() -- Only upcoming tours
    GROUP BY t.tour_id
    ORDER BY booking_count DESC
    LIMIT 4;
";

$result = $conn->query($sql);

// Store popular tours in an array
$popular_tours = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $popular_tours[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Landing Page</title>

    <script>
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    </script>
</head>
<body class="index">
<?php if ($currentPage == 'index.php'): ?>
    <video class="indexbackground-video" autoplay muted loop>
        <source src="image/720p.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <?php endif; ?>

    <div class="main-container"></div>
    <header class="main-header">
            <div class="header-logo-text">
                <img src="image/logo.png" alt="Logo" class="logo-image">
                <span class="header-text">Higanteng Laagan Travel & Tours</span>
            </div>
            <nav class="header-navHP">
                <a href="#" class="nav-linkHP">LANDING PAGE</a>
                <a href="#popular" class="nav-linkHP">TOURS</a>
                <a href="#about" class="nav-linkHP">ABOUT US</a>
                <a href="#review" class="nav-linkHP">REVIEWS</a>
                <a href="login.php" class="nav-linkHP">LOG IN</a>
            </nav>
        </header>


        <div class="videotextcontainer">
            <p class="welcome-text">"ROAMING THE WORLD LIKE A GIANT, DISCOVERING HIDDEN GEMS"</p>
        </div>
        <section id="popular" class="tours-section2">
    <div class="section-header">
        <div class="left-header">
            <p class="section-title">RECENT SUCCESSFUL TOURS</p>
        </div>
    </div>
    <div class="scroll-container">
        <?php if (!empty($popular_tours)): ?>
            <?php foreach ($popular_tours as $tour): ?>
                <div class="rectangle" onclick="window.location.href='../tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
                    <div class="image-container">
                        <?php if (!empty($tour['image_path'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($tour['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" 
                                 class="tour-image">
                        <?php else: ?>
                            <p>Image not available</p>
                        <?php endif; ?>
                    </div>
                    <div class="tour-details">
                        <p class="tour-title2"><?php echo htmlspecialchars($tour['tour_name']); ?></p>
                        <p><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?> per person</p>
                    </div>
                    <a href="../customer_form.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button">Book Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No popular tours available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

        <section id="popular" class="tours-section2">
    <div class="section-header">
        <div class="left-header">
            <p class="section-title">MOST POPULAR TOURS</p>
        </div>
    </div>
    <div class="scroll-container">
        <?php if (!empty($popular_tours)): ?>
            <?php foreach ($popular_tours as $tour): ?>
                <div class="rectangle" onclick="window.location.href='../tour_details.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>'">
                    <div class="image-container">
                        <?php if (!empty($tour['image_path'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($tour['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" 
                                 class="tour-image">
                        <?php else: ?>
                            <p>Image not available</p>
                        <?php endif; ?>
                    </div>
                    <div class="tour-details">
                        <p class="tour-title2"><?php echo htmlspecialchars($tour['tour_name']); ?></p>
                        <p><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?> per person</p>
                    </div>
                    <a href="../customer_form.php?tour_id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="tour-button">Book Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No popular tours available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

        <section id="about" class="tours-section2">
            <div class="section-header">
            <div class="left-header">
                <p class="section-title">TRAVEL MINDANAO WITH US</p>
            </div>
            </div>

            <div class="aboutusimage-container">
            <img src="image/AU1.jpg" alt="Image 1">
            <img src="image/AU2.jpg" alt="Image 2">
            <img src="image/AU3.jpg" alt="Image 3">
            <img src="image/AU4.jpg" alt="Image 4">
            <img src="image/AU5.jpg" alt="Image 5">
            <img src="image/AU6.jpg" alt="Image 6">
            <img src="image/AU7.jpg" alt="Image 7">
            <img src="image/AU8.jpg" alt="Image 8">
            <img src="image/AU9.jpg" alt="Image 9">
            <img src="image/AU10.jpg" alt="Image 10">
            </div>


            <div class="details-text">
                <p>Welcome to HIGANTENG LAAGAN TRAVEL AND TOURS — your gateway to breathtaking adventures across the 
                    Philippines! We are a passionate and dedicated team that believes in the beauty of exploration 
                    and the thrill of discovering the world’s hidden gems. Whether you're an experienced hiker or 
                    just starting out, we offer a variety of tours that cater to all adventure seekers.
                From the towering peak of Mt. Apo, the highest mountain in the Philippines, to the serene and 
                    pristine waters of Lake Holon, we bring you closer to nature's wonders. Our tours range from 
                    minor day hikes to challenging major day hikes, day tours, and exhilarating multi-day adventures 
                    like the 3-day, 2-night or 2-day, 1-night packages.
                With over 20 curated tours across some of the most stunning locations in the country, including 
                    the majestic Hummingbird Mountain of Bukidnon, we are here to help you step out of the ordinary 
                    and into extraordinary landscapes. Every journey with us is crafted to make your exploration not
                     just an adventure but an experience to remember.</p>
            </div>
        </section>



        <section id="review" class="tours-section2">
            <div class="left-header">
            <p class="section-title"> REVIEWS</p>
            </div>
            <div class="review-container">
                <div class="review-rectangle">
                    <img src="image/dp4.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Princess Marie Kate</div>
                        <div class="review-content">OMG! Most unforgettable moment<br>of my life!! I would<br>definitely visit again</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/dp3.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Maria Santos</div>
                        <div class="review-content">The view was breathtaking!<br>Definitely worth the hike!<br>Would book again.</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/dp1.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Juan Dela Cruz</div>
                        <div class="review-content">The hike was challenging, but<br>the natural beauty made every<br>step worth it!</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/dp2.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Luis Gonzales</div>
                        <div class="review-content">Amazing scenery, and the<br>staff were so helpful<br>and friendly!</div>
                        </div>
                </div>
            </div>
        </section>

        <section class="green-background">
            <img src="image/advertisement.jpg" width="100%" height="auto" alt="join us"/>
        </section>

        <div class="orangebutton-container">
            <form action="login.php" method="get">
            <button type="submit" class="orange-button">BOOK NOW</button>
            </form>
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
            <ul><li><a href="#">Landing Page</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
        
    </div>
    <div class="footerBottom">
        <p>Copyright &copy;2024; Designed by <span class="designer">CASSanga</span></p>
    </div>
</footer>
<script>
        window.onload = function () {
            if (window.location.hash) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            window.scrollTo(0, 0);
        };
    </script>
</body>
</html>
