<?php
session_start();
include('../db_connect.php');
$error_message = ''; // To store error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT user_id, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                header("Location: home.php"); // Redirect to homepage
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No account found with that email.";
        }
        $stmt->close();
    } else {
        $error_message = "Database query failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login Page</title>
</head>
<body>
<div class="loginbackground" style="background-image: url('image/bgforest1.jpg'); background-size: cover;">
    <header class="TOURmain-header">
        <div class="TOURheader-logo-text">
            <img src="image/logo.png" alt="Logo" class="TOURlogo-image">
            <span class="TOURheader-text">Higanteng Laagan Travel & Tours</span>
        </div>
    </header>
    <section>
        <div class="logincreateform-box">
            <div class="logincreateform-value">
                <form action="login.php" method="POST">
                    <h2 class="logincreateh2">Login to continue</h2>
                    <?php if (!empty($error_message)) : ?>
                        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>
                    <div class="inputbox">
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <div class="forget">
                        <label><input type="checkbox" name="remember"> Remember Me</label>
                        <a href="#">Forgot Password</a>
                    </div>
                    <button type="submit" class="logincreatebutton">Log in</button>
                    <div class="register">
                        <p>Don't have an account? <a href="register.php">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
