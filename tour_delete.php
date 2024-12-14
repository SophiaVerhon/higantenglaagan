<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

if (isset($_GET['tour_id'])) {
    $tour_id = intval($_GET['tour_id']); 

    $delete_guides_query = "DELETE FROM tour_guide_assignment WHERE tour_id = ?";
    $stmt_guides = $conn->prepare($delete_guides_query);
    $stmt_guides->bind_param("i", $tour_id);

    if ($stmt_guides->execute()) {
        $delete_tour_query = "DELETE FROM tour WHERE tour_id = ?";
        $stmt_tour = $conn->prepare($delete_tour_query);
        $stmt_tour->bind_param("i", $tour_id);

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
