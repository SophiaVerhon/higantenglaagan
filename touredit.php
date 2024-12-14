<?php
include("db_connect.php");
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Check if a tour ID is provided via GET
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];

    // Retrieve the current tour details from the database
    $query = "SELECT * FROM tour WHERE tour_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tour = $result->fetch_assoc();

    // Retrieve the assigned guides for the tour
    $guide_query = "SELECT tg.guide_id, tg.name, tg.contact_no FROM tourguide tg 
                    JOIN tour_guide_assignment tga ON tg.guide_id = tga.guide_id
                    WHERE tga.tour_id = ?";
    $stmt_guides = $conn->prepare($guide_query);
    $stmt_guides->bind_param("i", $tour_id);
    $stmt_guides->execute();
    $guide_result = $stmt_guides->get_result();
    $tour_guides = $guide_result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Tour ID is missing.");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Extract form data
    $tour_name = $_POST['tour_name'];
    $description = $_POST['description'];
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $price_per_person = $_POST['price_per_person'];
    $location = $_POST['location'];
    $max_bookings = $_POST['max_bookings']; 
    $is_exclusive = $_POST['tour_type'] == '1' ? 1 : 0;
    $min_bookings = ($is_exclusive && isset($_POST['min_bookings'])) ? $_POST['min_bookings'] : null; 
    $guide_names = $_POST['guide_name'] ?? []; 
    $guide_contact_nos = $_POST['guide_contact_no'] ?? []; 

    // If it's not an exclusive tour, ensure dates are provided
    if ($is_exclusive == 0 && (empty($start_date) || empty($end_date))) {
        echo "Start Date and End Date are required for regular tours.";
        exit();
    }

    // Check if a new image is uploaded
    if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == 0) {
        $image_tmp_name = $_FILES['tour_image']['tmp_name'];
        $image_data = file_get_contents($image_tmp_name);
    } else {
        $image_data = $tour['image_path']; // Use existing image if no new image uploaded
    }

    // Update tour details
    $update_query = "UPDATE tour SET 
    tour_name = ?, 
    description = ?, 
    start_date = ?, 
    end_date = ?, 
    price_per_person = ?, 
    location = ?, 
    max_bookings = ?, 
    image_path = ?, 
    min_bookings = ? 
    WHERE tour_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssdsssis", $tour_name, $description, $start_date, $end_date, $price_per_person, $location, $max_bookings, $image_data, $min_bookings, $tour_id);

    if ($stmt->execute()) {
        // Delete existing guide assignments for the tour
        $delete_assignments_query = "DELETE FROM tour_guide_assignment WHERE tour_id = ?";
        $stmt_delete_assignments = $conn->prepare($delete_assignments_query);
        $stmt_delete_assignments->bind_param("i", $tour_id);
        $stmt_delete_assignments->execute();

        $message = '';

        // Loop through each guide and update or add them
        foreach ($guide_names as $index => $guide_name) {
            $guide_contact_no = $guide_contact_nos[$index];
        
            // Check if the guide already exists in the system
            $existing_guide_query = "SELECT guide_id FROM tourguide WHERE name = ? AND contact_no = ?";
            $stmt_check = $conn->prepare($existing_guide_query);
            $stmt_check->bind_param("ss", $guide_name, $guide_contact_no);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
        
            if ($result->num_rows > 0) {
                // Guide already exists, get guide_id
                $guide_id = $result->fetch_assoc()['guide_id'];
            } else {
                // Guide does not exist, insert a new guide
                $insert_guide_query = "INSERT INTO tourguide (name, contact_no) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($insert_guide_query);
                $stmt_insert->bind_param("ss", $guide_name, $guide_contact_no);
                $stmt_insert->execute();
                $guide_id = $stmt_insert->insert_id;
            }
        
            // Check if the guide is already assigned to the tour
            $existing_assignment_query = "SELECT 1 FROM tour_guide_assignment WHERE tour_id = ? AND guide_id = ?";
            $stmt_check_assignment = $conn->prepare($existing_assignment_query);
            $stmt_check_assignment->bind_param("ii", $tour_id, $guide_id);
            $stmt_check_assignment->execute();
            $check_assignment_result = $stmt_check_assignment->get_result();
        
            if ($check_assignment_result->num_rows == 0) {
                // If not already assigned, assign the guide to the tour
                $assign_guide_query = "INSERT INTO tour_guide_assignment (tour_id, guide_id) VALUES (?, ?)";
                $stmt_assign = $conn->prepare($assign_guide_query);
                $stmt_assign->bind_param("ii", $tour_id, $guide_id);
                $stmt_assign->execute();
            } else {
                // If guide is already assigned, set the message for the modal
                $message = 'This guide is already assigned to the tour!';
            }
        }
        if ($message) {
            // Show the modal if there's a message
            echo "<script>document.getElementById('modal-message').textContent = '$message'; document.getElementById('modal').style.display = 'block';</script>";
        } else {
            // Redirect to the tour listing page after successful update
            header("Location: admin_tour.php?msg=Tour updated successfully");
            exit();
        }
    } else {
        // If update query fails, show error
        echo "Error updating tour: " . $stmt->error;
    }
}
?>

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

    <button class="close-button" onclick="window.location.href='admin_tour.php';">&times;</button>

    <div class="container">
        <h2>Edit Tour</h2>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="touredit.php?id=<?php echo $tour['tour_id']; ?>" method="POST" enctype="multipart/form-data" onsubmit="return checkSubmission()">
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
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($tour['start_date']); ?>" id="start_date" <?php echo $tour['is_exclusive'] == 1 ? '' : 'required'; ?>>
            </div>

            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($tour['end_date']); ?>" id="end_date" <?php echo $tour['is_exclusive'] == 1 ? '' : 'required'; ?>>
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

            <div class="form-group">
                <label for="tour_type">Tour Type:</label>
                <select id="tour_type" name="tour_type" required>
                    <option value="0" <?php echo $tour['is_exclusive'] == 0 ? 'selected' : ''; ?>>Regular Tour</option>
                    <option value="1" <?php echo $tour['is_exclusive'] == 1 ? 'selected' : ''; ?>>Exclusive Tour</option>
                </select>
            </div>

            <!-- Exclusive Fields (only show when Exclusive Tour is selected) -->
            <div id="exclusiveFields" style="display: <?php echo $tour['is_exclusive'] == 1 ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label for="min_bookings">Minimum Bookings (for exclusive tours):</label>
                    <input type="number" name="min_bookings" value="<?php echo $tour['min_bookings']; ?>" <?php echo $tour['is_exclusive'] == 1 ? 'required' : ''; ?>>
                </div>
            </div>

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