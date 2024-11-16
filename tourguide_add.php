<<?php

include('db_connect.php');


$message = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $guide_name = $_POST['name'];
    $contact_no = $_POST['contact_no'];

    $query = "INSERT INTO tourguide (name, contact_no) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $guide_name, $contact_no); 

    if ($stmt->execute()) {
        
        $message = "Tour guide added successfully!";
    } else {
        
        $message = "Error adding tour guide: " . $stmt->error;
    }
    if ($stmt->execute()) {
        
        header("Location: admin_customer_list.php");
        exit();
    }
    
    

    // Close the statement
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Tour Guide</title>
</head>
<body>

<h2>Add New Tour Guide</h2>

<!-- Display message if set -->
<?php if ($message != "") { echo "<p>$message</p>"; } ?>

<!-- Form to submit a new tour guide -->
<form action="tourguide_add.php" method="POST">
    <label for="name">Tour Guide Name:</label>
    <input type="text" name="name" required><br>

    <label for="contact_no">Contact Number:</label>
    <input type="text" name="contact_no" required><br>

    <button type="submit">Add Tour Guide</button>
</form>

</body>
</html>
