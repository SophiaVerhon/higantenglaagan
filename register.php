<?php  
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body class="form-page">
    <div class="container">
        <div class="box form-box">
        <?php 
            include("config.php"); // Include database configuration file
            if (isset($_POST['submit'])) {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];

                // Check if the email is already registered
                $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");
                if (mysqli_num_rows($verify_query) != 0) {
                    echo "<div class='message'>
                            <p>This email is already registered. Please use another one!</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } else {
                    // Hash the password before saving
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert the new user into the database
                    $insert_query = "INSERT INTO users (Username, Email, Password) VALUES ('$username', '$email', '$hashed_password')";
                    if (mysqli_query($con, $insert_query)) {
                        echo "<div class='message'>
                                <p>Registration successful!</p>
                              </div> <br>";
                        echo "<a href='login.php'><button class='btn'>Login Now</button>";
                    } else {
                        echo "<div class='message'>
                                <p>There was an error. Please try again later.</p>
                              </div> <br>";
                    }
                }
            } else {  
        ?>
            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register">
                </div>
                <div class="links">
                    Already a member? <a href="login.php">Sign In</a>
                </div>
            </form>
        <?php } ?>
        </div>
    </div>
</body>
</html>
