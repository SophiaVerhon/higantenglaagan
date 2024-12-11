<?php
include('../db_connect.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT b.booking_id, t.tour_name, b.booking_date
          FROM booking b
          JOIN customer c ON b.customer_id = c.customer_id
          JOIN tour t ON b.tour_id = t.tour_id
          WHERE c.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $bookings = [];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bookkstatus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Booking Status</title>
</head>

<body>
    <header class="TOURmain-header">
        <div class="TOURheader-logo-text">
            <img src="image/logo.png" alt="Logo" class="TOURlogo-image">
            <span class="TOURheader-text">Higanteng Laagan Travel & Tours</span>
        </div>
        <nav class="TOURheader-navHP">
            <a href="homepage.php" class="TOURnav-linkHP">GO BACK TO HOMEPAGE</a>
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

    <!-- Buttons -->
    <div class="BKSTATUSbutton-container">
        <button class="BKSTATUSbutton" id="current-booking-btn">CURRENT BOOKING</button>
        <button class="BKSTATUSbutton" id="completed-btn">COMPLETED</button>
    </div>

    <!-- Status Container -->
    <div class="BKSTATUSstatus-container" id="status-container">
        <!-- Dynamic Content Goes Here -->
        <?php if (count($bookings) > 0): ?>
            <ul class="booking-list">
                <?php foreach ($bookings as $booking): ?>
                    <li>
                        You booked <strong><?php echo $booking['tour_name']; ?></strong> on 
                        <strong><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have not made any bookings yet.</p>
        <?php endif; ?>
    </div>

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
                    <li><a href="USERhompage.php">Home</a></li>
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
    </script>
</body>

</html>
