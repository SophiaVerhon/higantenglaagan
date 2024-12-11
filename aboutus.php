<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="aboutus.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>About Us</title>
</head>
<body class="tour">

<header class="TOURmain-header">
  <div class="TOURheader-logo-text">
    <img src="image/logo.png" alt="Logo" class="TOURlogo-image">
    <span class="TOURheader-text">Higanteng Laagan Travel & Tours</span>
  </div>
  <nav class="TOURheader-navHP">
                <a href="home.php" class="TOURnav-linkHP">GO BACK TO HOMEPAGE</a>
    <div class="TOURdropdown">
      <span class="TOURnav-linkHP dropdown-toggle" onclick="toggleDropdown('profile-dropdown')">MY PROFILE</span>
      <div id="profile-dropdown" class="TOURdropdown-menu">
        <a href="profile.php" class="TOURdropdown-item">My Account</a>
        <a href="bkstatus.php" class="TOURdropdown-item">Booking Status</a>
        <a href="index.php" class="TOURdropdown-item">Log Out</a>
      </div>
    </div>
  </nav>
</header>

<section id="about" class="tours-section2">
            <div class="section-header">
            <div class="left-header">
                <p class="section-title">TRAVEL MINDANAO WITH US</p>
            </div>
            </div>

            <div class="details-text">
                <p>Welcome to <b>HIGANTENG LAAGAN TRAVEL AND TOURS</b> — your gateway to breathtaking adventures across the 
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

            <div class="aboutusimage-container">
            <img src="image/AU1.jpg" alt="Image 1">
            <img src="image/AU3.jpg" alt="Image 3">
            <img src="image/AU5.jpg" alt="Image 5">
            <img src="image/AU6.jpg" alt="Image 6">
            <img src="image/AU7.jpg" alt="Image 7">
            <img src="image/AU9.jpg" alt="Image 9">
            <img src="image/AU10.jpg" alt="Image 10">
            </div>


        </section>


        <footer id="about-us-footer">
            <div class="TOURfooterContainer">
                <div class="TOURsocialIcons">
                    <a href=""><i class="fa-brands fa-facebook"></i></a>
                    <a href=""><i class="fa-brands fa-instagram"></i></a>
                    <a href=""><i class="fa-brands fa-twitter"></i></a>
                    <a href=""><i class="fa-brands fa-youtube"></i></a>
                </div>
                <div class="TOURfooterNav">
                    <ul>
                        <li><a href="hompage.php">Home</a></li>
                        <li><a href="">About Us</a></li>
                        <li><a href="">Contact</a></li>
                    </ul>
                </div>

            </div>
            <div class="TOURfooterBottom">
                <p>Copyright &copy;2024; Designed by <span class="TOURdesigner">CASSanga</span></p>
            </div>
        </footer>

<!-- JavaScript for dropdown -->
<script>
    function toggleDropdown(menuId) {
    const dropdown = document.getElementById(menuId);
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

// Close dropdown if clicked outside
    window.onclick = function (event) {
        if (!event.target.matches('.dropdown-toggle')) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
            dropdown.style.display = 'none';
            });
            }
        }

  function editAccount() {
    window.location.href = 'edit-account.php';
  }

  function deleteAccount() {
    if (confirm('Are you sure you want to delete your account?')) {
      window.location.href = 'delete-account.php';
    }
  }
</script>
</body>
</html>