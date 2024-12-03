<?php

include('db_connect.php');
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $query = "SELECT password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

   
    if ($stored_password && password_verify($password, $stored_password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/adminlog.css">
</head>

<body>
<div class="navbar">
        <a href="admin_home.php">Home</a>
        <a href="admin_tour.php">Tour</a>
        <a href="admin_about.php">About us</a>
        <a href="admin_about.php">Review</a>
    </div>
<form action="admin_log.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br>

    <button type="submit">Login</button>
</form>

</body>
</html>




<p><?php echo $message; ?></p>
