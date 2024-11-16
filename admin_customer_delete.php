<?php

include('db_connect.php');


if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

   
    $query = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);  
    if ($stmt->execute()) {
        
        header("Location: admin_customer_list.php");
        exit();
    } else {
        echo "Error deleting customer: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No customer ID provided!";
    exit();
}

$conn->close();
?>
