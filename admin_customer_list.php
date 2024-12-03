<?php

include('db_connect.php');


$query = "SELECT * FROM customer";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List</title>
   
    <link rel="stylesheet" href="css/customer_list.css"> 
</head>
<body>
<div class="close-btn-container">
    <a href="admin_dashboard.php" class="close-btn">&times;</a>
</div>

<div class="container">
    <h2>Customer List</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
           
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['customer_id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['phone_no'] . "</td>";
                echo "<td>" . $row['address'] . "</td>";
                echo "<td>
                        <a href='admin_customer_edit.php?id=" . $row['customer_id'] . "'>Edit</a> |
                        <a href='admin_customer_delete.php?id=" . $row['customer_id'] . "' onclick='return confirm(\"Are you sure you want to delete this customer?\");'>Delete</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php

$conn->close();
?>
