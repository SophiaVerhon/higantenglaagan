<?php

include('db_connect.php');
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $tour_name = $_POST['tour_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price_per_person = $_POST['price_per_person'];
    $location = $_POST['location'];

    // Check if file was uploaded without errors
    if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == 0) {
        $image_name = $_FILES['tour_image']['name'];
        $image_tmp = $_FILES['tour_image']['tmp_name'];
        $image_folder = "uploads/tours/";

        // Ensure the uploads directory exists
        if (!file_exists($image_folder)) {
            mkdir($image_folder, 0777, true);
        }

        $image_path = $image_folder . basename($image_name);

        if (move_uploaded_file($image_tmp, $image_path)) {
            // Insert data, including image path, into the database
            $query = "INSERT INTO tour (tour_name, description, start_date, end_date, price_per_person, location, image_path) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssdss", $tour_name, $description, $start_date, $end_date, $price_per_person, $location, $image_path);

            if ($stmt->execute()) {
                $message = "Tour added successfully!";
            } else {
                $message = "Error adding tour: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Failed to upload image.";
        }
    } else {
        $message = "Please upload an image.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Tour</title>
    <link rel="stylesheet" href="css/addtour_edit.css">
</head>
<body>
<div class="close-btn-container">
    <a href="admin_tour.php" class="close-btn">&times;</a>
</div>

<div class="container">
    <h2>Add New Tour</h2>

    <?php if ($message != "") { echo "<p class='message'>$message</p>"; } ?>

    <form action="tour_add.php" method="POST" onsubmit="return validateDates()" class="tour-form" enctype="multipart/form-data">
        <!-- Other form fields -->

        <div class="form-group">
            <label for="tour_image">Tour Image:</label>
            <input type="file" name="tour_image" id="tour_image" accept="image/*" required>
        </div>

        <div class="form-group">
            <label for="tour_name">Tour Name:</label>
            <input type="text" name="tour_name" id="tour_name" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>

        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" required>
        </div>

        <div class="form-group">
            <label for="price_per_person">Price per Person:</label>
            <input type="number" name="price_per_person" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" name="location" id="location" required>
        </div>

        

        <button type="submit" class="submit-btn">Add Tour</button>
        <a href ="admin_tour.php"></a>
    </form>
</div>

<script>
    const today = new Date().toISOString().split("T")[0];
    
    const startDateInput = document.getElementById("start_date");
    const endDateInput = document.getElementById("end_date");
    startDateInput.min = today;
    endDateInput.min = today;

    startDateInput.value = today;

    startDateInput.addEventListener("change", function() {
        const selectedStartDate = startDateInput.value;
        endDateInput.min = selectedStartDate;
    });

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (endDate <= startDate) {
            alert("End date must be after the start date.");
            return false;
        }
        return true;
    }
</script>

</body>
</html>
