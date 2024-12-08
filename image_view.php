<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];

    // Prepare the query to get the image data
    $query = "SELECT image_path FROM tour WHERE tour_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $tour_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image_data);

    if ($stmt->fetch()) {
        header("Content-Type: image/jpeg"); // Adjust the content type as per the image type
        echo $image_data; // Output the binary image data
    } else {
        echo "Image not found.";
    }

    $stmt->close();
}
?>
