<?php
include('db_connect.php');
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Retrieve the current tour details
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    $query = "SELECT * FROM tour WHERE tour_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tour = $result->fetch_assoc();
    
    // Retrieve the associated tour guides
    $guide_query = "SELECT tg.guide_id, tg.name, tg.contact_no FROM tourguide tg 
                    JOIN tour_guide_assignment tga ON tg.guide_id = tga.guide_id
                    WHERE tga.tour_id = ?";
    $stmt_guides = $conn->prepare($guide_query);
    $stmt_guides->bind_param("i", $tour_id);
    $stmt_guides->execute();
    $guide_result = $stmt_guides->get_result();
    $tour_guides = $guide_result->fetch_all(MYSQLI_ASSOC);
} else {
    // Handle the case if tour ID is not passed
    die("Tour ID is missing.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_name = $_POST['tour_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price_per_person = $_POST['price_per_person'];
    $location = $_POST['location'];
    $max_bookings = $_POST['max_bookings']; // Get max_bookings from the form
    $guide_names = $_POST['guide_name']; // Array of guide names
    $guide_contact_nos = $_POST['guide_contact_no']; // Array of guide contact numbers

    // Check if a new image is uploaded
    if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == 0) {
        $image_tmp_name = $_FILES['tour_image']['tmp_name'];
        $image_data = file_get_contents($image_tmp_name); // Get image data as binary
    } else {
        // If no image is uploaded, retain the existing image in the database
        $image_data = $tour['image_path'];
    }

    // Update the tour record, including the image data
    $update_query = "UPDATE tour SET tour_name = ?, description = ?, start_date = ?, end_date = ?, price_per_person = ?, location = ?, max_bookings = ?, image_path = ? WHERE tour_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssdsssi", $tour_name, $description, $start_date, $end_date, $price_per_person, $location, $max_bookings, $image_data, $tour_id);

    if ($stmt->execute()) {
        // Update guides if necessary
        foreach ($guide_names as $index => $guide_name) {
            $guide_contact_no = $guide_contact_nos[$index];

            // Update the guide's details in the 'tourguide' table
            $update_guide_query = "UPDATE tourguide SET name = ?, contact_no = ? WHERE guide_id = ?";
            $stmt_guide = $conn->prepare($update_guide_query);
            $stmt_guide->bind_param("ssi", $guide_name, $guide_contact_no, $tour_guides[$index]['guide_id']);
            $stmt_guide->execute();
        }

        // If the tour update is successful, redirect back to the admin tour page
        header("Location: admin_tour.php?msg=Tour updated successfully");
        exit();
    } else {
        $message = "Error updating tour: " . $stmt->error;
    }
}
?>

<!-- HTML Form remains the same -->


<!-- HTML for displaying the edit form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour</title>
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/addtour_style.css">
</head>
<body>
    <div class="navbar">
        <a href="admin_home.php">Home</a>
        <a href="admin_tour.php">Tours</a>
        <a href="admin_about.php">About Us</a>
        <a href="review.php">Review</a>
        <a href="tour_add.php">+ Add New Tour</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <button class="close-button" onclick="window.location.href='admin_tour.php';">×</button>
    
    <div class="container">
        <h2>Edit Tour</h2>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="touredit.php?id=<?php echo $tour['tour_id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="tour_image">Tour Image:</label>
                <?php if (!empty($tour['image_path'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($tour['image_path']); ?>" alt="Tour Image" style="max-width: 200px;">
                <?php endif; ?>
                <input type="file" name="tour_image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="tour_name">Tour Name:</label>
                <input type="text" name="tour_name" value="<?php echo htmlspecialchars($tour['tour_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($tour['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($tour['start_date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($tour['end_date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price_per_person">Price per Person:</label>
                <input type="number" name="price_per_person" value="<?php echo htmlspecialchars($tour['price_per_person']); ?>" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($tour['location']); ?>" required>
            </div>

            <div class="form-group">
                <label for="max_bookings">Max Bookings:</label>
                <input type="number" name="max_bookings" value="<?php echo htmlspecialchars($tour['max_bookings']); ?>" required>
            </div>

            <!-- Tour Guide Fields (Editable) -->
            <div id="guideFields">
                <?php foreach ($tour_guides as $guide): ?>
                    <div class="form-group">
                        <label for="guide_name[]">Guide Name:</label>
                        <input type="text" name="guide_name[]" value="<?php echo htmlspecialchars($guide['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="guide_contact_no[]">Guide Contact Number:</label>
                        <input type="text" name="guide_contact_no[]" value="<?php echo htmlspecialchars($guide['contact_no']); ?>" required>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" onclick="addGuideField()">Add Another Guide</button>
            <button type="submit">Update Tour</button>
        </form>
    </div>

    <script>
        function addGuideField() {
            const guideFields = document.getElementById("guideFields");
            const newGuideField = document.createElement("div");

            newGuideField.innerHTML = ` 
                <div class="form-group">
                    <label for="guide_name[]">Guide Name:</label>
                    <input type="text" name="guide_name[]" required>
                </div>

                <div class="form-group">
                    <label for="guide_contact_no[]">Guide Contact Number:</label>
                    <input type="text" name="guide_contact_no[]" required>
                </div>
            `;
            guideFields.appendChild(newGuideField);
        }
    </script>
</body>
</html>
