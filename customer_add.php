<?php

include('db_connect.php');

$message = "";
$messageClass = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    $address = $_POST['address'];

    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageClass = "error";
    } elseif (!is_numeric($phone)) {
        $message = "Phone number must be numeric.";
        $messageClass = "error";
    } else {
       
        $query = "INSERT INTO customer (name, email, phone_no, address) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssis", $name, $email, $phone, $address);

        
        if ($stmt->execute()) {
            $message = "Customer registered successfully!";
            $messageClass = "success";
        } else {
            $message = "Error registering customer: " . $stmt->error;
            $messageClass = "error";
        }

        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-container button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #218838;
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h2>Add New Customer</h2>

<?php if ($message != "") { ?>
    <div class="message <?php echo $messageClass; ?>">
        <?php echo $message; ?>
    </div>
<?php } ?>
<?php if ($message == "Customer registered successfully!") { ?>
        <button class="ok-button" onclick="window.location.href='admin_home.php'">OK</button>
    <?php } ?>
<div class="form-container">
    <form action="customer_add.php" method="POST">
</form>
</div>
