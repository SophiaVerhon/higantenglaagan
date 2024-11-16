<?php

include('db_connect.php');

$plain_password = 'admin123';
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

$query = "INSERT INTO admins (username, password) VALUES ('admin', ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hashed_password);
$stmt->execute();
echo "Password has been hashed and stored.";
?>
