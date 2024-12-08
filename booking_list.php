<?php
include('db_connect.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_log.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $tour_id = $_POST['tour_id'];
    $payment_status = $_POST['payment_status'];
    $total_payment = $_POST['total_payment'];

    
    $booking_date = date('Y-m-d');

   
    $query = "INSERT INTO booking (customer_id, tour_id, booking_date, payment_status,) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisi", $customer_id, $tour_id, $booking_date, $payment_status,);

    if ($stmt->execute()) {
        $message = "Booking added successfully!";
    } else {
        $message = "Error adding booking: " . $stmt->error;
    }
    $stmt->close();
}


$customers_query = "SELECT customer_id, name FROM customer";
$customers_result = $conn->query($customers_query);


$tours_query = "SELECT tour_id, tour_name FROM tour";
$tours_result = $conn->query($tours_query);

// Fetch all bookings to display
$bookings_query = "SELECT b.booking_id, c.name as customer_name, t.tour_name, b.booking_date, b.payment_status
                   FROM booking b
                   JOIN customer c ON b.customer_id = c.customer_id
                   JOIN tour t ON b.tour_id = t.tour_id";
$bookings_result = $conn->query($bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="css/bookings.css">
</head>
<body>

    <div class="navbar">
        <a href="admin_home.php">Home</a>
        <a href="admin_tour.php">Tours</a>
        <a href="admin_about.php">About Us</a>
        <a href="admin_dashboard.php">Review</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <div class="container">
        <h2>Bookings</h2>
        
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
        
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer Name</th>
                    <th>Tour Name</th>
                    <th>Booking Date</th>
                    <th>Payment Status</th>
                    
                </tr>
            </thead>
            <tbody>
            </div>
                <?php while ($row = $bookings_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['booking_id']; ?></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><?php echo $row['tour_name']; ?></td>
                        <td><?php echo $row['booking_date']; ?></td>
                        <td><?php echo $row['payment_status']; ?></td>
                        
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php $conn->close(); ?>
