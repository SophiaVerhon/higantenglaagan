<?php
include('db_connect.php'); // Database connection

// Check if the form was submitted and the file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
    // Get the uploaded file's binary data
    $valid_id_data = file_get_contents($_FILES['valid_id']['tmp_name']);
    
    // Check if the file content is properly fetched
    if (!$valid_id_data) {
        echo "Error reading the file.";
        exit;
    }

    // Get other form data (e.g., customer name)
    $customer_name = $_POST['customer_name']; // Adjust according to your form fields

    // Prepare and insert the data into the database
    $query = "INSERT INTO customer (name, valid_id_path) VALUES (?, ?)";

    if ($stmt = $conn->prepare($query)) {
        // Bind the customer name and valid ID (binary data)
        $stmt->bind_param('sb', $customer_name, $valid_id_data);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "Customer added successfully!";
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
} else {
    echo "Error uploading file!";
}

// Close the database connection
$conn->close();
?>