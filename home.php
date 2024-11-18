<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=style.css">
    <title>Home</title>
</head>
<body class="form-page">
    <div class="container">
        <div class="box form-box">
            <header>Welcome, <?php echo $_SESSION['username']; ?></header>
            <div class="links">
                <?php 
                    $id = $_SESSION['id'];
                    $query = mysqli_query($con,"SELECT * FROM users WHERE Id=$id");

                    while($result = mysqli_fetch_assoc($query)){
                        $res_id = $result['Id'];
                    }
                
                    echo "<a href='edit.php?Id=$res_id'>Change Profile</a>";
                ?>
                <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
            </div>
        </div>
    </div>
</body>
</html>