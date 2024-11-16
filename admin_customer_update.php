<?php

include('db_connect.php');


if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    
    $query = "SELECT * FROM customer WHERE customer_id = ?";  
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);  
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows == 1) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found!";
        exit();
    }
} else {
    echo "No customer ID provided!";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = $_POST['name'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    $address = $_POST['address'];

   
    $query = "UPDATE customer SET name = ?, age = ?, email = ?, phone_no = ?, address = ? WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    
    
    $stmt->bind_param("sssss", $name, $age, $email, $phone, $address, $customer_id);
  
    if ($stmt->execute()) {
        
        header("Location: admin_customer_list.php");
        exit();
    } else {
        echo "Error updating customer: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
</head>
<body>

<h2>Edit Customer</h2>


<form action="admin_customer_update.php?id=<?php echo $customer['customer_id']; ?>" method="POST">
    <label for="name">Name:</label>
    <input type="text" name="name" value="<?php echo $customer['name']; ?>" required><br>

    <label for="age">Age:</label>
    <input type="text" name="age" value="<?php echo $customer['age']; ?>" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $customer['email']; ?>" required><br>

    <label for="phone">Phone Number:</label>
    <input type="text" name="phone_no" value="<?php echo $customer['phone_no']; ?>"><br>

    <label for="address">Address:</label>
    <textarea name="address" required><?php echo $customer['address']; ?></textarea><br>

    <button type="submit">Update Customer</button>
</form>

</body>
</html>

<?php

$conn->close();
?>
