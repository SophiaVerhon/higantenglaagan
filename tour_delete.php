<?php
include('db_connect.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

// Check if `tour_id` is provided
if (isset($_GET['tour_id'])) {
    $tour_id = intval($_GET['tour_id']); // Ensure tour_id is an integer

    // Prepare the DELETE query
    $query = "DELETE FROM tour WHERE tour_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tour_id);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: admin_tour.php?msg=Tour deleted successfully");
        exit();
    } else {
        echo "Error deleting tour: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid tour ID.";
}

$conn->close();
?>
