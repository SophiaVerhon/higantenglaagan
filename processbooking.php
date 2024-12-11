<?php
include('db_connect.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $tour_id = intval($_POST['tour_id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $age = intval($_POST['age']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Handle file upload
    $valid_id = $_FILES['valid_id'];
    if ($valid_id['error'] === UPLOAD_ERR_OK) {
        $valid_id_data = file_get_contents($valid_id['tmp_name']); // Read file contents
        $valid_id_data = mysqli_real_escape_string($conn, $valid_id_data); // Escape binary data
    } else {
        die("Error uploading the file. Please try again.");
    }

    // Insert booking into the database
    $sql = "INSERT INTO customers (customer_name, email, phone_no, age, address, valid_id_path, tour_id) 
            VALUES ('$customer_name', '$email', '$phone_no', $age, '$address', '$valid_id_data', $tour_id)";

    if (mysqli_query($conn, $sql)) {
        echo "Booking successful! Thank you for booking with us.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>