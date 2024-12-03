<?php
include('db_connect.php'); // Database connection
$message = "";

// Get the tour_id from the URL
if (!isset($_GET['tour_id'])) {
    die("Tour ID not specified. Please go back and select a tour.");
}
$tour_id = $_GET['tour_id']; // Capture the tour ID

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    $age = $_POST['age']; // Capture the age input
    $address = $_POST['address'];
    $valid_id_path = "";

    // Handle file upload for valid ID
    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES['valid_id']['name']);
        $target_file = $target_dir . uniqid() . "-" . $file_name;

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['valid_id']['tmp_name'], $target_file)) {
                $valid_id_path = $target_file;
            } else {
                $message = "Error uploading valid ID. Please try again.";
            }
        } else {
            $message = "Invalid file type. Allowed types: JPG, PNG, PDF.";
        }
    }

    // Insert customer and booking details into the database
    if (empty($message)) {
        $customer_query = "INSERT INTO customer (name, email, phone_no, age, address, valid_id_path) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_customer = $conn->prepare($customer_query);
        $stmt_customer->bind_param("sssiss", $name, $email, $phone, $age, $address, $valid_id_path);

        if ($stmt_customer->execute()) {
            $customer_id = $stmt_customer->insert_id;

            // Insert booking information
            $booking_query = "INSERT INTO booking (customer_id, tour_id, booking_date) VALUES (?, ?, NOW())";
            $stmt_booking = $conn->prepare($booking_query);
            $stmt_booking->bind_param("ii", $customer_id, $tour_id);

            if ($stmt_booking->execute()) {
                $message = "Successfully Booked, See You on Tour!";
                
                // **INSERT NOTIFICATION CODE HERE**
                $notification_message = "New Booking: $name has booked for Tour ID $tour_id.";
                $notification_query = "INSERT INTO notifications (message) VALUES (?)";
                $stmt_notification = $conn->prepare($notification_query);
                $stmt_notification->bind_param("s", $notification_message);
                $stmt_notification->execute();
                $stmt_notification->close();
                // **END NOTIFICATION CODE**
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
    <link rel="stylesheet" href="css/customer_form.css">
    <title>Booking Confirmation</title>
</head>
<body>
<div class="close-btn-container">
    <a href="users/home.php" class="close-btn">&times;</a>
</div>
<div class="container">
    <?php if (!empty($message)): ?>
        <div class="message-box">
            <p><?php echo $message; ?></p>
            <a href="users/home.php" class="btn">Back to Tours</a>
        </div>
    <?php else: ?>
        <h2>Customer Booking Form</h2>
        <form action="customer_form.php?tour_id=<?php echo htmlspecialchars($tour_id); ?>" method="POST" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input type="text" name="name" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br>

    <label for="phone">Phone Number:</label>
    <input type="text" name="phone_no" required><br>

    <label for="age">Age:</label>
    <input type="number" name="age" min="1" required><br> <!-- New Age Field -->

    <label for="address">Address:</label>
    <textarea name="address" required></textarea><br>

    <label for="valid_id">Upload Valid ID:</label>
    <input type="file" name="valid_id" accept=".jpg, .jpeg, .png, .pdf" required><br>

    <button type="submit">Submit Booking</button>
</form>

    <?php endif; ?>
</div>
</body>
</html>
