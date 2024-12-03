<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];

    // Fetch the tour data
    $query = "SELECT * FROM tour WHERE tour_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $tour = $result->fetch_assoc();
    } else {
        echo "Tour not found!";
        exit();
    }
} else {
    echo "No tour ID provided!";
    exit();
}

// Handle form submission for updating tour details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_name = $_POST['tour_name'];
    $description = $_POST['description'];
    $price_per_person = $_POST['price_per_person'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $image_path = $_POST['image_path'];

    $query = "UPDATE tour SET tour_name = ?, description = ?, price_per_person = ?, start_date = ?, end_date = ?, location = ?, image_path = ? WHERE tour_id = ?";
    $stmt = $conn->prepare($query);

    // Bind the parameters, including `tour_id`
    $stmt->bind_param("ssissssi", $tour_name, $description, $price_per_person, $start_date, $end_date, $location, $image_path, $tour_id);
    if ($stmt->execute()) {
        // Ensure redirect occurs after a successful update
        header("Location: admin_tour.php");
        exit();
    } else {
        echo "Error updating tour: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/addtour_edit.css">
    <title>Edit Tour</title>
</head>
<body>

<h2>Edit Tour Details</h2>
<button class="close-button" onclick="window.location.href='admin_tour.php';">×</button>

<form action="" method="POST">
    <label for="tour_name">Tour Name:</label>
    <input type="text" name="tour_name" value="<?php echo $tour['tour_name']; ?>" required><br>

    <label for="description">Description:</label>
    <textarea name="description" required><?php echo $tour['description']; ?></textarea><br>

    <label for="price_per_person">Price per Person:</label>
    <input type="number" name="price_per_person" value="<?php echo $tour['price_per_person']; ?>" required><br>

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" value="<?php echo $tour['start_date']; ?>" required><br>

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" value="<?php echo $tour['end_date']; ?>" required><br>

    <label for="location">Location:</label>
    <input type="text" name="location" value="<?php echo $tour['location']; ?>" required><br>

    <label for="image_path">Image Path:</label>
    <input type="text" name="image_path" value="<?php echo $tour['image_path']; ?>"><br>

    <button type="submit">Update Tour</button>
</form>

</body>
</html>
