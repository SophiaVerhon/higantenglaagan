<?php  
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>LandingPage</title>
</head>
<body>
    <video class="background-video" autoplay muted loop>
        <source src="image/720p.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Main container to apply the fixed 150% zoom effect -->
    <div class="main-container">
        <header class="main-header">
            <div class="header-logo-text">
                <img src="image/logo.png" alt="Logo" class="logo-image">
                <span class="header-text">Higanteng Laagan Travel & Tours</span>
            </div>
            <nav class="header-nav">
                <a href="#tours" class="nav-link">TOURS</a>
                <a href="#about" class="nav-link">ABOUT US</a>
                <a href="#reviews" class="nav-link">REVIEWS</a>
                <a href="login.php" class="nav-link">LOG IN</a>
            </nav>
        </header>

        <div class="container">
            <p class="welcome-text">not sure what to put here</p>
        </div>

        <section id="tours" class="tours-section">
        <div class="section-header">
        <p class="section-title">MOST POPULAR TOURS</p>
        <a href="login.php" class="see-more">See More</a>
    </div>

            <div class="scroll-container">
                <div class="rectangle">
                    <img src="photo/bglogo.jpg.jpg" class="tour-image" alt="Tour Image">
                    <div class="tour-title">Somewhere Beach (5 days)</div>
                    <a href="login.php" class="tour-button">₱ 18,950.00</a>
                </div>
                <div class="rectangle">
                    <img src="image/bglogo.jpg" class="tour-image" alt="Tour Image">
                    <div class="tour-title">Lake of Skemberdoo (5 days)</div>
                    <a href="login.php" class="tour-button">₱ 5,655.00</a>
                </div>
                <div class="rectangle">
                    <img src="image/bglogo.jpg" class="tour-image" alt="Tour Image">
                    <div class="tour-title">Mount Bundok (2 days & night)</div>
                    <a href="login.php" class="tour-button">₱ 3,750.00</a>
                </div>
                <div class="rectangle">
                    <img src="image/bglogo.jpg" class="tour-image" alt="Tour Image">
                    <div class="tour-title">Mount Name (1 day)</div>
                    <a href="login.php" class="tour-button">₱ 10,000.00</a>
                </div>
                <div class="rectangle">
                    <img src="image/bglogo.jpg" class="tour-image" alt="Tour Image">
                    <div class="tour-title">Mount Bundok (2 days & night)</div>
                    <a href="login.php" class="tour-button">₱ 3,750.00</a>
                </div>
            </div>
        </section>

        <section id="about" class="about-section white-background">
            <div class="section-header">
                <p class="section-title">TRAVEL MINDANAO WITH US</p>
            </div>

            <div class="about-content">
                <div class="intro-text">
                    <p>Lorem ipsum dolor sit amet, consectetuer<br>
                adipiscing elit. Aenean commodo ligula eget<br>
                dolor. Aenean massa. Cum sociis 2w  natoque<br>
                penatibus et magnis dis parturient montes, <br>
                nascetur ridiculus mus. Donec quam felis,  <br>
                ultricies nec, pellentesque eu, pretium qu,<br>
                sem. Nulla consequat massa quis enim. Donec<br>
                pede justo, fringilla velnec, vulpute eget,<br>
                arcu.. In enim justo, rhoncus ut, imperdiet<br>
                a, venenatis vitae, justo. Nullam dictum   <br>
                felis eu pede mollis pretium. Integerdunt. <br>
                Cras dapibus. Vivamus e</p>
                </div>

                <div class="image-row">
                    <img src="image/bglogo.jpg" alt="Destination 1" class="destination-image">
                    <img src="image/bglogo.jpg" alt="Destination 2" class="destination-image">
                    <img src="image/bglogo.jpg" alt="Destination 3" class="destination-image">
                    <img src="image/bglogo.jpg" alt="Destination 4" class="destination-image">
                    <img src="image/bglogo.jpg" alt="Destination 5" class="destination-image">
                </div>
            </div>

            <div class="details-text">
                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. 
                    Aenean commodo ligula eget dolor. Aenean massa. Cum sociis 
                    natoque penatibus et magnis dis parturient montes, nascetur 
                    ridiculus mus. Donec quam felis, ultricies nec, pellentesque 
                    eu, pretium quis, sem. Nulla consequat massa quis enim. Donec 
                    pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. 
                    In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. 
                    Nullam dictum felis eu pede mollis pretium. Integer tincidunt. 
                    Cras dapibus. Vivamus e</p>
            </div>
        </section>


    <section class="white-background">
            <img src="image/yellow.jpg" width="100%" height="auto" alt="join us"/>
    </section>


        <section id="reviews" class="reviews-section">
            <p class="section-title">REVIEWS</p>
            <div class="scroll-container">
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Princess Marie Kate</div>
                        <div class="review-content">OMG! Most unforgettable<br>moment of my life!!</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Penguin</div>
                        <div class="review-content">Mi Muhe Ye!</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">Botuser1234</div>
                        <div class="review-content">Sisi Dada yo</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">John Doe</div>
                        <div class="review-content">Great experience!</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">John Doe</div>
                        <div class="review-content">Great experience!</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">John Doe</div>
                        <div class="review-content">Great experience!</div>
                    </div>
                </div>
                <div class="review-rectangle">
                    <img src="image/logo.png" alt="Reviewer" class="reviewer-image">
                    <div class="review-text">
                        <div class="reviewer-name">John Doe</div>
                        <div class="review-content">Great experience!</div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <p>&copy; <?php echo date("Y"); ?> All rights reserved.</p>
        </footer>
    </div>
</body>
</html>