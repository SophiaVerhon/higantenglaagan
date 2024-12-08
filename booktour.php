<?php
include('db_connect.php');
session_start();

if (isset($_POST['book_tour'])) {
    $tour_id = $_POST['tour_id'];
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in and the user ID is stored in the session

    // Query to check the current number of bookings and max bookings
    $check_bookings_query = "SELECT max_bookings, 
                                    (SELECT COUNT(*) FROM booking WHERE tour_id = ?) AS current_bookings
                             FROM tour 
                             WHERE tour_id = ?";
    $stmt = $conn->prepare($check_bookings_query);
    $stmt->bind_param("ii", $tour_id, $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tour = $result->fetch_assoc();

    if ($tour) {
        $max_bookings = $tour['max_bookings'];
        $current_bookings = $tour['current_bookings'];

        // Check if the booking limit has been reached
        if ($max_bookings > 0 && $current_bookings >= $max_bookings) {
            echo "Sorry, this tour is fully booked. You cannot book it now.";
        } else {
            // Proceed with booking if space is available
            $book_query = "INSERT INTO booking (tour_id, user_id, booking_date) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($book_query);
            $stmt->bind_param("ii", $tour_id, $user_id);

            if ($stmt->execute()) {
                echo "Tour booked successfully!";
            } else {
                echo "Error booking the tour: " . $stmt->error;
            }
        }
    } else {
        echo "Tour not found.";
    }
}
