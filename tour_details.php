<?php
session_start();
include("db_connect.php");

// Check if `tour_id` is set in the URL
if (!isset($_GET['tour_id'])) {
    echo "Tour ID is missing.";
    exit;
}

$tour_id = intval($_GET['tour_id']); // Sanitize the input

// Fetch tour details from the database
$sql = "SELECT * FROM tour WHERE tour_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Tour not found.";
    exit;
}

$tour = $result->fetch_assoc();
$currency_symbol = "₱";

// Retrieve the description and remove the section markers
$description = $tour['description'];

// Clean up the description and separate each section
$description_cleaned = preg_replace('/#(Itinerary|Inclusions|Exclusions|Cancellation Policy)#/', '<h2 class="TOURdiv-title">$1</h2>', $description);
$description_cleaned = nl2br($description_cleaned);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/tours.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title><?php echo htmlspecialchars($tour['tour_name']); ?></title>
</head>
<body>
    <div class="TOURmain-container">
        <header class="TOURmain-header">
            <div class="TOURheader-logo-text">
                <img src="image/logo.png" alt="Logo" class="TOURlogo-image">
                <span class="TOURheader-text">Higanteng Laagan Travel & Tours</span>
            </div>
            <nav class="TOURheader-navHP">
                <a href="#home" class="TOURnav-linkHP">GO BACK TO HOMEPAGE</a>
            </nav>
        </header>

        <!-- Tour Image -->
        <div class="TOURimage-container">
            <?php if (!empty($tour['image_path'])): ?>
                <?php 
                    // Convert the binary image data to base64
                    $image_data = base64_encode($tour['image_path']);
                ?>
                <img src="data:image/jpeg;base64,<?php echo $image_data; ?>" alt="Tour Image" class="TOURbg-logo">
            <?php else: ?>
                <p>No image available for this tour.</p>
            <?php endif; ?>
        </div>

        <!-- Tour Details -->
        <div class="TOURtext-container">
            <h1 class="TOURsection-title">🌿<?php echo htmlspecialchars($tour['tour_name']); ?>🌿</h1>

            <!-- Cleaned Description (with sections) -->
            <p><?php echo $description_cleaned; ?></p>

            <!-- Additional Tour Details -->
            <div class="TOURdetails">
                <p><strong>Location:</strong> <?php echo htmlspecialchars($tour['location']); ?></p>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($tour['start_date']))); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($tour['end_date']))); ?></p>
            </div>

            <div class="tour-rectangle" id="booknow">
                <div class="price-area">
                    <p class="price"><?php echo $currency_symbol . htmlspecialchars($tour['price_per_person']); ?></p>
                </div>

                <div class="orangebutton-container">
                    <form action="customer_form.php" method="get">
                        <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour_id); ?>">
                        <button type="submit" class="orange-button"><b>BOOK NOW</b></button>
                    </form>
                </div>
            </div>
        </div>

        <footer id="about-us-footer">
            <div class="TOURfooterContainer">
                <div class="TOURsocialIcons">
                    <a href=""><i class="fa-brands fa-facebook"></i></a>
                    <a href=""><i class="fa-brands fa-instagram"></i></a>
                    <a href=""><i class="fa-brands fa-twitter"></i></a>
                    <a href=""><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="TOURfooterBottom">
                <p>Copyright &copy;2024; Designed by <span class="TOURdesigner">CASSanga</span></p>
            </div>
        </footer>
    </div>
</body>
</html>

<?php $conn->close(); ?>
