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

    // Delete related records from tour_guide_assignment first
    $delete_guides_query = "DELETE FROM tour_guide_assignment WHERE tour_id = ?";
    $stmt_guides = $conn->prepare($delete_guides_query);
    $stmt_guides->bind_param("i", $tour_id);

    // Execute the deletion of related guide assignments
    if ($stmt_guides->execute()) {
        // Now delete the tour itself
        $delete_tour_query = "DELETE FROM tour WHERE tour_id = ?";
        $stmt_tour = $conn->prepare($delete_tour_query);
        $stmt_tour->bind_param("i", $tour_id);

        // Execute the deletion of the tour
        if ($stmt_tour->execute()) {
            header("Location: admin_tour.php?msg=Tour deleted successfully");
            exit();
        } else {
            echo "Error deleting tour: " . $stmt_tour->error;
        }
        $stmt_tour->close();
    } else {
        echo "Error deleting guide assignments: " . $stmt_guides->error;
    }
    $stmt_guides->close();
} else {
    echo "Invalid tour ID.";
}

$conn->close();
?>
