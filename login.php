<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Login</title>
</head>
<body class="form-page">
    <div class="container">
        <div class="box form-box">
            <?php 
              include("php/config.php");

              // Initialize error modal as hidden
              $showErrorModal = false;

              // Check if the form was submitted
              if(isset($_POST['submit'])){
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $password = mysqli_real_escape_string($con, $_POST['password']);

                $result = mysqli_query($con, "SELECT * FROM users WHERE Email='$email' AND Password='$password'") or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                if(is_array($row) && !empty($row)){
                    $_SESSION['valid'] = $row['Email'];
                    $_SESSION['username'] = $row['Username'];
                    $_SESSION['age'] = $row['Age'];
                    $_SESSION['id'] = $row['Id'];
                    header("Location: home.php"); // Redirect if login is successful
                    exit; // Add exit after the redirect
                } else {
                    // Show error modal only if login attempt failed
                    $showErrorModal = true;
                }
              }
            ?>
            
            <header>Login to Continue</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="links">
                    Don't have an account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Error Modal -->
    <?php if($showErrorModal): ?>
    <div class="modal" id="errorModal" style="display: flex;">
        <div class="modal-content">
            <p>Wrong Username or Password</p>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
    </script>
</body>
</html>