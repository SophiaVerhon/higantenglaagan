<?php
include('db_connect.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Check if the form was submitted
if (isset($_POST['add_tour'])) {
    // Sanitize form inputs
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price_per_person = $_POST['price_per_person'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Handle image upload
    $image_dir = 'uploads/';
    $image_path = '';
    if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == 0) {
        $image_name = $_FILES['tour_image']['name'];
        $image_tmp_name = $_FILES['tour_image']['tmp_name'];
        $image_path = $image_dir . basename($image_name);

        // Check if the image is valid (e.g., check file size, type)
        if (move_uploaded_file($image_tmp_name, $image_path)) {
            // Image uploaded successfully
        } else {
            echo "Error uploading image.";
            exit();
        }
    }

    // Insert tour data into the database
    $query = "INSERT INTO tour (tour_name, description, location, price_per_person, start_date, end_date, image_path) 
              VALUES ('$tour_name', '$description', '$location', '$price_per_person', '$start_date', '$end_date', '$image_path')";
    
    if ($conn->query($query) === TRUE) {
        // Redirect to admin_tour.php to see the updated tour list
        header("Location: admin_tour.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

$conn->close();
?>
