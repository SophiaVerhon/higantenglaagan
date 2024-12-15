<?php
include('db_connect.php');
$message = "";

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if (!isset($_GET['tour_id'])) {
    die("Tour ID not specified. Please go back and select a tour.");
}
$tour_id = $_GET['tour_id'];

// Fetch the tour details including exclusive flag, min_bookings, max_bookings
$tour_query = "SELECT tour_name, max_bookings, min_bookings, is_exclusive FROM tour WHERE tour_id = ?";
$stmt_tour = $conn->prepare($tour_query);
$stmt_tour->bind_param("i", $tour_id);
$stmt_tour->execute();
$stmt_tour->store_result();
$stmt_tour->bind_result($tour_name, $max_bookings, $min_bookings, $isExclusive);
$stmt_tour->fetch();
$stmt_tour->close();

// For exclusive tours, ensure the form has enough fields for min_bookings
if ($isExclusive && $min_bookings > 0) {
    $num_adventurers = $min_bookings - 1; // Subtract 1 for the main customer
} else {
    $num_adventurers = 0; // No default adventurers for non-exclusive tours
}

// Get current number of bookings for this tour
$booking_count_query = "SELECT COUNT(*) FROM booking WHERE tour_id = ?";
$stmt_booking_count = $conn->prepare($booking_count_query);
$stmt_booking_count->bind_param("i", $tour_id);
$stmt_booking_count->execute();
$stmt_booking_count->bind_result($current_booking_count);
$stmt_booking_count->fetch();
$stmt_booking_count->close();

// Calculate available slots for max bookings (excluding the main customer)
$availableSlots = max(0, $max_bookings - 1 - $current_booking_count);

if ($max_bookings > 0 && $current_booking_count >= $max_bookings) {
    $message = "Sorry, this tour has already reached the maximum number of bookings.";
    exit();
}

// If max_bookings is NULL or 0, proceed with open bookings
if ($max_bookings === NULL || $max_bookings === 0) {
    // Open slots for booking (no restriction)
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        // Collect customer details
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone_no'];
        $age = $_POST['age'];
        $address = $_POST['address'];
        $valid_id_path = null;

        // Handle file upload for the main customer (LONGBLOB storage)
        if (isset($_FILES['valid_id_path']) && $_FILES['valid_id_path']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['valid_id_path']['name'];
            $file_data = file_get_contents($_FILES['valid_id_path']['tmp_name']);
            $valid_id_path = $file_data; // Store the file's binary data
        }

        // Insert the customer data into the customer table
        $customer_query = "INSERT INTO customer (name, email, phone_no, age, address, valid_id_path, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_customer = $conn->prepare($customer_query);
        $stmt_customer->bind_param("sssissi", $name, $email, $phone, $age, $address, $valid_id_path, $user_id);

        if ($stmt_customer->execute()) {
            $customer_id = $stmt_customer->insert_id;

            // Insert booking for the main customer
            $booking_query = "INSERT INTO booking (customer_id, tour_id, user_id, booking_date) VALUES (?, ?, ?, NOW())";
            $stmt_booking = $conn->prepare($booking_query);
            $stmt_booking->bind_param("iii", $customer_id, $tour_id, $user_id);

            if (!$stmt_booking->execute()) {
                throw new Exception("Error adding booking for main customer.");
            }

            // Update the current booking count after adding the main customer
            $current_booking_count++;
            // Collect payment details if provided
            if (isset($_POST['ref_number']) && !empty($_POST['ref_number'])) {
                $reference_no = $_POST['ref_number'];
                $payment_upload = null;
                if (isset($_FILES['payment_upload']) && $_FILES['payment_upload']['error'] === UPLOAD_ERR_OK) {
                    $payment_upload = file_get_contents($_FILES['payment_upload']['tmp_name']); // Store the uploaded image
                }

                $amount_paid = $_POST['amount_paid']; // Assuming this is part of the form

                // Insert payment details into the payment table
                $payment_query = "INSERT INTO payment (booking_id, date, reference_no, payment_proof, amount_paid) VALUES (?, NOW(), ?, ?, ?)";
                $stmt_payment = $conn->prepare($payment_query);
                $stmt_payment->bind_param("issi", $customer_id, $reference_no, $payment_upload, $amount_paid);

                if (!$stmt_payment->execute()) {
                    throw new Exception("Error adding payment details.");
                }
            }

            $notification_message = "New Booking: $name has booked for $tour_name.";
            $notification_query = "INSERT INTO notifications (message) VALUES (?)";
            $stmt_notification = $conn->prepare($notification_query);
            $stmt_notification->bind_param("s", $notification_message);
            $stmt_notification->execute();

            // Handle additional adventurers
           // Handling adventurers' bookings
                if (isset($_POST['adventurer_name']) && is_array($_POST['adventurer_name'])) {
                    for ($i = 0; $i < count($_POST['adventurer_name']); $i++) {
                        // Check if adding this adventurer exceeds the max booking limit
                        if ($totalAdventurers + 1 > $availableSlots) {  // +1 for the main customer
                            throw new Exception("You cannot add more adventurers than available slots.");
                        }

                    $additional_name = $_POST['adventurer_name'][$i];
                    $additional_email = $_POST['adventurer_emails'][$i];
                    $additional_phone = $_POST['adventurer_phones'][$i];
                    $additional_age = $_POST['adventurer_ages'][$i];
                    $additional_address = $_POST['adventurer_address'][$i];
                    $additional_valid_id = null;

                    if (isset($_FILES['adventurer_valid_id']) && $_FILES['adventurer_valid_id']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_data = file_get_contents($_FILES['adventurer_valid_id']['tmp_name'][$i]);
                        $additional_valid_id = $file_data;
                    }

                    // Insert additional adventurer
                    $stmt_additional = $conn->prepare("INSERT INTO customer (name, email, phone_no, age, address, valid_id_path, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt_additional->bind_param("sssissi", $additional_name, $additional_email, $additional_phone, $additional_age, $additional_address, $additional_valid_id, $user_id);

                    if ($stmt_additional->execute()) {
                        $additional_customer_id = $stmt_additional->insert_id;

                        // Insert booking for the adventurer
                        $stmt_additional_booking = $conn->prepare("INSERT INTO booking (customer_id, tour_id, user_id, booking_date) VALUES (?, ?, ?, NOW())");
                        $stmt_additional_booking->bind_param("iii", $additional_customer_id, $tour_id, $user_id);

                        if (!$stmt_additional_booking->execute()) {
                            throw new Exception("Error adding booking for adventurer $additional_name.");
                        }

                        // Update the booking count after adding an adventurer
                        $current_booking_count++;
                    } else {
                        throw new Exception("Error adding adventurer: " . $stmt_additional->error);
                    }

                    $notification_message = "New Booking: $additional_name has booked for $tour_name.";
                    $stmt_notification = $conn->prepare($notification_query);
                    $stmt_notification->bind_param("s", $notification_message);
                    $stmt_notification->execute();
                }
            }

            $conn->commit();
            header("Location: users/home.php");
            exit();
        } else {
            throw new Exception("Error adding main customer: " . $stmt_customer->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = $e->getMessage();
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/customer_formstyle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Booking</title>
</head>

<body>


<header class="TOURmain-header">
  <div class="TOURheader-logo-text">
    <img src="image/logo.png" alt="Logo" class="TOURlogo-image">
    <span class="TOURheader-text">Higanteng Laagan Travel & Tours</span>
  </div>
  <nav class="TOURheader-navHP">
    <a href="users/home.php "class="TOURnav-linkHP">GO BACK TO HOMEPAGE</a>
    <div class="TOURdropdown">
      <span class="TOURnav-linkHP dropdown-toggle" onclick="toggleDropdown()">MY PROFILE</span>
      <div id="profile-dropdown" class="TOURdropdown-menu">
        <a href="users/profile.php" class="TOURdropdown-item">My Account</a>
        <a href="users/bkstatus.php" class="TOURdropdown-item">Booking Status</a>
        <a href="users/index.php" class="TOURdropdown-item">Log Out</a>
      </div>
    </div>
  </nav>
</header>

<section>
    <div class="logincreateform-box">
        <div class="logincreateform-value">
        <form action="customer_form.php?tour_id=<?php echo htmlspecialchars($tour_id); ?>" method="POST" enctype="multipart/form-data">
                <h2 class="logincreateh2">Booking Form</h2>

                <div class="input-row">
                    <div class="inputbox">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="inputbox">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="input-row">
                    <div class="inputbox">
                        <label for="phone_no">Phone Number:</label>
                        <input type="tel" id="phone_no" name="phone_no" required>
                    </div>
                    <div class="inputbox">
                        <label for="age">Age:</label>
                        <input type="number" id="age" name="age" required>
                    </div>
                </div>

                <div class="input-row">
                    <div class="inputbox">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="inputbox">
                        <label for="valid_id_path">Valid ID for Customer:</label>
                        <input type="file" id="valid_id_path" name="valid_id_path" required>
                    </div>
                </div>

                <h2>Adventurers</h2>
                <div class="input-row">
                    <label for="num_people">Number of Adventurers:</label>
                    <input type="number" id="num_people" name="num_people" min="" value="">
                </div>

                <div id="adventurer-fields">
                </div>
                <div id="booking-limit-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">×</span>
        <p>Booking limit reached! You cannot add more adventurers.</p>
    </div>
</div>

                <button type="button" class="submit-btn" onclick="togglePaymentSection()">Proceed to Payment</button>
               
<div id="payment-section" class="collapsible-content" style="display: none;">
    <div class="inputbox">
        <label for="ref_number">GCASH Reference Number (Required)</label>
        <input type="text" id="ref_number" name="ref_number" required>
    </div>

    <div class="inputbox">
        <label for="payment_upload">Upload Payment Proof (Optional)</label>
        <input type="file" id="payment_upload" name="payment_upload" accept="image/*">
    </div>

    <div class="inputbox">
        <label for="amount_paid">Amount Paid (Required)</label>
        <input type="number" id="amount_paid" name="amount_paid" required>
    </div>

    <button type="submit" name="book_tour" class="submit-btn">Book Now</button>
</div>
</form>

<script>
    // Toggle payment section visibility
    function togglePaymentSection() {
        const paymentSection = document.getElementById('payment-section');
        paymentSection.style.display = (paymentSection.style.display === 'none' || paymentSection.style.display === '') ? 'block' : 'none';
    }
</script>
            </form>
        </div>
    </div>
</section>

<script>
// Set the default number of adventurers based on the min_bookings value (fetched from PHP)
// For exclusive tours, this will be min_bookings - 1, otherwise, it will be based on the number set in the database
let numAdventurers = <?php echo $num_adventurers; ?>; // This value is dynamically fetched from the database
let availableSlots = <?php echo $availableSlots; ?>;

// Set the value of the num_people input field to the default number of adventurers
document.getElementById('num_people').value = numAdventurers; 

// Dynamically add adventurer fields based on the default number of adventurers when the page loads
let adventurerFields = document.getElementById('adventurer-fields');
for (let i = 0; i < numAdventurers; i++) {
    adventurerFields.innerHTML += `
        <h3>Adventurer ${i + 1}</h3>
        <div class="input-row">
            <div class="inputbox">
                <label for="adventurer_name_${i}">Name:</label>
                <input type="text" id="adventurer_name_${i}" name="adventurer_name[]" required>
            </div>
            <div class="inputbox">
                <label for="adventurer_email_${i}">Email:</label>
                <input type="email" id="adventurer_email_${i}" name="adventurer_emails[]" required>
            </div>
        </div>
        <div class="input-row">
            <div class="inputbox">
                <label for="adventurer_phone_${i}">Phone Number:</label>
                <input type="tel" id="adventurer_phone_${i}" name="adventurer_phones[]" required>
            </div>
            <div class="inputbox">
                <label for="adventurer_age_${i}">Age:</label>
                <input type="number" id="adventurer_age_${i}" name="adventurer_ages[]" required>
            </div>
        </div>
        <div class="input-row">
            <div class="inputbox">
                <label for="adventurer_address_${i}">Address:</label>
                <input type="text" id="adventurer_address_${i}" name="adventurer_address[]" required>
            </div>
            <div class="inputbox">
                <label for="adventurer_valid_id_${i}">Valid ID for Adventurer ${i + 1}:</label>
                <input type="file" id="adventurer_valid_id_${i}" name="adventurer_valid_id[]" required>
            </div>
        </div>`;
}

// Update the adventurer fields dynamically when the num_people input changes
document.getElementById('num_people').addEventListener('input', function() {
    let numPeople = this.value;
    adventurerFields.innerHTML = ''; // Clear previous fields

    // Check if the number of people exceeds the available slots
    if (numPeople > availableSlots) {
        alert("You cannot add more adventurers than available slots!");
        return; // Stop adding fields if the max bookings are exceeded
    }

    // Add the adventurer fields dynamically based on the updated number of people
    for (let i = 0; i < numPeople; i++) {
        adventurerFields.innerHTML += `
            <h3>Adventurer ${i + 1}</h3>
            <div class="input-row">
                <div class="inputbox">
                    <label for="adventurer_name_${i}">Name:</label>
                    <input type="text" id="adventurer_name_${i}" name="adventurer_name[]" required>
                </div>
                <div class="inputbox">
                    <label for="adventurer_email_${i}">Email:</label>
                    <input type="email" id="adventurer_email_${i}" name="adventurer_emails[]" required>
                </div>
            </div>
            <div class="input-row">
                <div class="inputbox">
                    <label for="adventurer_phone_${i}">Phone Number:</label>
                    <input type="tel" id="adventurer_phone_${i}" name="adventurer_phones[]" required>
                </div>
                <div class="inputbox">
                    <label for="adventurer_age_${i}">Age:</label>
                    <input type="number" id="adventurer_age_${i}" name="adventurer_ages[]" required>
                </div>
            </div>
            <div class="input-row">
                <div class="inputbox">
                    <label for="adventurer_address_${i}">Address:</label>
                    <input type="text" id="adventurer_address_${i}" name="adventurer_address[]" required>
                </div>
                <div class="inputbox">
                    <label for="adventurer_valid_id_${i}">Valid ID for Adventurer ${i + 1}:</label>
                    <input type="file" id="adventurer_valid_id_${i}" name="adventurer_valid_id[]" required>
                </div>
            </div>`;
    }
});
</script>


</body>
</html>
