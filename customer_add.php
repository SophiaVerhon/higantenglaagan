<?php
include('db_connect.php'); // Database connection
$message = ""; // Message for success or errors

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    $address = $_POST['address'];
    $tour_id = $_POST['tour_id']; 
    $valid_id_path = ""; 

    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES['valid_id']['name']);
        $target_file = $target_dir . uniqid() . "-" . $file_name; // Unique name for uploaded file

        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['valid_id']['tmp_name'], $target_file)) {
                $valid_id_path = $target_file; // Save file path for the database
            } else {
                $message = "Error uploading valid ID. Please try again.";
            }
        } else {
            $message = "Invalid file type for valid ID. Allowed types: JPG, PNG, PDF.";
        }
    }

    if (empty($message)) {
        // Insert customer details
        $customer_query = "INSERT INTO customer (name, email, phone_no, address, valid_id_path) VALUES (?, ?, ?, ?, ?)";
        $stmt_customer = $conn->prepare($customer_query);
        $stmt_customer->bind_param("sssss", $name, $email, $phone, $address, $valid_id_path);

        if ($stmt_customer->execute()) {
            $customer_id = $stmt_customer->insert_id;

            $booking_query = "INSERT INTO booking (customer_id, tour_id, booking_date) VALUES (?, ?, NOW())";
            $stmt_booking = $conn->prepare($booking_query);
            $stmt_booking->bind_param("ii", $customer_id, $tour_id);

            if ($stmt_booking->execute()) {
                $message = "Successfully Booked, See You on Tour!";
            } else {
                $message = "Error adding booking: " . $stmt_booking->error;
            }

            $stmt_booking->close();
        } else {
            $message = "Error adding customer: " . $stmt_customer->error;
        }

        $stmt_customer->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/customer_form_style.css"> <!-- Link to your CSS -->
    <title>Booking Confirmation</title>
</head>
<body>
<div class="container">
    <?php if (!empty($message)): ?>
        <div class="message-box">
            <p><?php echo $message; ?></p>
            <a href="users/user_tour.php" class="btn">Go Back to Tours</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
