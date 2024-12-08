<?php
session_start();
include("db_connect.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_type = $_POST['tour_type']; // Get the selected tour type

    // Redirect to the tour add page with the selected option
    header("Location: tour_add.php?tour_type=$tour_type");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tour Option</title>
    <link rel="stylesheet" href="users/style1.css">
</head>
<body>
    <div class="main-container">
        <header>
            <h1>Select Tour Type</h1>
        </header>

        <!-- Buttons for selecting tour type -->
        <form action="touroption.php" method="POST">
            <button type="submit" name="tour_type" value="0" class="tour-button">Regular Tour</button>
            <button type="submit" name="tour_type" value="1" class="tour-button">Exclusive Tour</button>
        </form>
    </div>

    <!-- Styling for buttons (optional) -->
    <style>
        .tour-button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .tour-button:hover {
            background-color: #0056b3;
        }
    </style>
</body>
</html>
