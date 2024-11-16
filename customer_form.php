<?php

include('db_connect.php');
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    $address = $_POST['address'];

    $query = "INSERT INTO customer (name, email, phone_no, address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $email, $phone, $address);

    if ($stmt->execute()) {
        $message = "Customer added successfully!";
        header("Location: customer_list.php"); 
        exit();
    } else {
        $message = "Error adding customer: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/customer_form_style.css">
    <title>Add New Customer</title>
</head>
<body>

<div class="container">
    <h2>Customer Form</h2>

    
    <?php if ($message != "") { echo "<p class='message'>$message</p>"; } ?>

    
    <form action="customer_add.php" method="POST" class="customer-form">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="phone">Phone Number:</label>
        <input type="text" name="phone_no" required><br>

        <label for="address">Address:</label>
        <textarea name="address" required></textarea><br>

        <button type="submit">Customer Form</button>
    </form>
</div>

</body>
</html>
