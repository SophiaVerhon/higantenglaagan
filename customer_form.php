<?php
include('db_connect.php'); // Database connection
$message = "";

$tour_query = "SELECT tour_name FROM tour WHERE tour_id = ?";
$stmt_tour = $conn->prepare($tour_query);
$stmt_tour->bind_param("i", $tour_id); // Bind the tour_id as an integer
$stmt_tour->execute();
$stmt_tour->store_result();
$stmt_tour->bind_result($tour_name);
$stmt_tour->fetch();

// Get the tour_id from the URL
if (!isset($_GET['tour_id'])) {
    die("Tour ID not specified. Please go back and select a tour.");
}
$tour_id = $_GET['tour_id']; // Capture the tour ID

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Main customer data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $valid_id_path = "valid_id_path";

    // Handle file upload for main customer valid ID
    if (isset($_FILES['valid_id_path']) && $_FILES['valid_id_path']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES['valid_id_path']['name']);
        $target_file = $target_dir . uniqid() . "-" . $file_name;

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['valid_id_path']['tmp_name'], $target_file)) {
                $valid_id_path = $target_file;
            } else {
                $message = "Error uploading valid ID. Please try again.";
            }
        } else {
            $message = "Invalid file type. Allowed types: JPG, PNG, PDF.";
        }
    }

    // Insert main customer and booking details into the database
    if (empty($message)) {
        // Insert the main customer
        $customer_query = "INSERT INTO customer (name, email, phone_no, age, address, valid_id_path) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_customer = $conn->prepare($customer_query);
        $stmt_customer->bind_param("sssiss", $name, $email, $phone, $age, $address, $valid_id_path);

        if ($stmt_customer->execute()) {
            $customer_id = $stmt_customer->insert_id;

            // Insert booking information
            $booking_query = "INSERT INTO booking (customer_id, tour_id, booking_date) VALUES (?, ?, NOW())";
            $stmt_booking = $conn->prepare($booking_query);
            $stmt_booking->bind_param("ii", $customer_id, $tour_id);

            if ($stmt_booking->execute()) {
                // Insert additional adventurers if provided
                if (isset($_POST['adventurer_name']) && is_array($_POST['adventurer_name'])) {
                    for ($i = 0; $i < count($_POST['adventurer_name']); $i++) {
                        $additional_name = $_POST['adventurer_name'][$i];
                        $additional_email = $_POST['adventurer_emails'][$i];
                        $additional_phone = $_POST['adventurer_phones'][$i];
                        $additional_age = $_POST['adventurer_ages'][$i];
                        $additional_address = $_POST['adventurer_address'][$i];
                        $additional_valid_id = "adventurer_valid_id";

                        // Handle file upload for additional adventurer valid ID
                        if (isset($_FILES['adventurer_valid_id']) && $_FILES['adventurer_valid_id']['error'][$i] === UPLOAD_ERR_OK) {
                            $file_name = basename($_FILES['adventurer_valid_id']['name'][$i]);
                            $target_file = $target_dir . uniqid() . "-" . $file_name;

                            if (move_uploaded_file($_FILES['adventurer_valid_id']['tmp_name'][$i], $target_file)) {
                                $additional_valid_id = $target_file;
                            } else {
                                $message = "Error uploading valid ID for adventurer $additional_name. Please try again.";
                            }
                        }

                        // Ensure the fields are not empty
                        if (!empty($additional_name) && !empty($additional_email) && !empty($additional_phone)) {
                            // Insert each additional person into the customer table
                            $stmt_additional = $conn->prepare("INSERT INTO customer (name, email, phone_no, age, address, valid_id_path) VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt_additional->bind_param("sssis", $additional_name, $additional_email, $additional_phone, $additional_age, $additional_address, $additional_valid_id);

                            if ($stmt_additional->execute()) {
                                $additional_customer_id = $stmt_additional->insert_id;

                                // Link the additional customer to the booking
                                $stmt_additional_booking = $conn->prepare("INSERT INTO booking (customer_id, tour_id, booking_date) VALUES (?, ?, NOW())");
                                $stmt_additional_booking->bind_param("ii", $additional_customer_id, $tour_id);
                                $stmt_additional_booking->execute();
                                $stmt_additional_booking->close();
                            } else {
                                $message = "Error adding additional adventurer: " . $stmt_additional->error;
                            }

                            $stmt_additional->close();
                        }
                    }
                }

                // Add notification for the booking
                $notification_message = "New Booking: $name has booked for $tour_name.";

                // Insert the notification message into the notifications table
                $notification_query = "INSERT INTO notifications (message) VALUES (?)";
                $stmt_notification = $conn->prepare($notification_query);
                $stmt_notification->bind_param("s", $notification_message); // Bind the notification message as a string
                $stmt_notification->execute();

                // Redirect to homepage after successful booking
                header("Location: users/home.php");
                exit();
            } else {
                $message = "Error adding booking: " . $stmt_booking->error;
            }

            $stmt_booking->close();
        } else {
            $message = "Error adding customer: " . $stmt_customer->error;
        }

        $stmt_customer->close();
    }
}
if (isset($_GET['tour_id'])) {
    $tour_id = $_GET['tour_id'];

    // Query to check the current number of bookings and max bookings for the selected tour
    $check_bookings_query = "SELECT max_bookings, 
                                    (SELECT COUNT(*) FROM booking WHERE tour_id = ?) AS current_bookings
                             FROM tour 
                             WHERE tour_id = ?";
    $stmt = $conn->prepare($check_bookings_query);
    $stmt->bind_param("ii", $tour_id, $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tour = $result->fetch_assoc();

    if ($tour) {
        $max_bookings = $tour['max_bookings'];
        $current_bookings = $tour['current_bookings'];

        // Check if the tour is fully booked
        if ($max_bookings > 0 && $current_bookings >= $max_bookings) {
            $is_fully_booked = true;
        } else {
            $is_fully_booked = false;
        }
    } else {
        die("Tour not found.");
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="users/customer_booking.css">
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
        <a href="profile.php" class="TOURdropdown-item">My Account</a>
        <a href="bkstatus.php" class="TOURdropdown-item">Booking Status</a>
        <a href="index.php" class="TOURdropdown-item">Log Out</a>
      </div>
    </div>
  </nav>
</header>

<section>
    <div class="logincreateform-box">
        <div class="logincreateform-value">
        <form action="customer_form.php?tour_id=<?php echo htmlspecialchars($tour_id); ?>" method="POST" enctype="multipart/form-data">
                <h2 class="logincreateh2">Booking Form</h2>

                <!-- First Name and Last Name side-by-side -->
                <div class="input-row">
                    <div class="inputbox">
                        <label for="first_name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
                <div class="input-row">
                    <div class="inputbox">
                        <label for="Email">Email:</label>
                        <input type="text" id="email" name="email" required>
                    </div>
                </div>


                <!-- Contact Number -->
                <div class="inputbox">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone_no" name="phone_no" required>
                </div>
                <div class="inputbox">
                    <label for="age">Age:</label>
                    <input type="text" id="age" name="age" required>
                </div>
                <div class="inputbox">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>

            

                <!-- Number of People -->
                <div class="inputbox">
                    <label for="num_people">Number of People</label>
                    <input type="number" id="num_people" name="num_people" min="1" required>
                </div>

                <!-- Name of Other Adventurers -->
                <div class="inputbox">
                    <label for="other_adventurers">Name of Other Adventurers</label>
                    <ul id="adventurer_list"></ul>
                </div>

                <!-- File Upload Section -->
                <div class="inputbox">
                    <label for="id_upload">Upload Your Valid ID <span>(Each adventurer must have one valid ID)</span></label>
                    <input type="file" id="id_upload" name="id_upload[]" accept="image/*" multiple required>
                </div>

                <div class="preview-container">
                    <p>Uploaded Photos Preview:</p>
                    <div id="photo_preview" class="photo-preview-box"></div>
                </div>

                <!-- Submit Button -->
                <?php if ($is_fully_booked): ?>
    <p>Sorry, this tour is fully booked. No more bookings can be made.</p>
<?php else: ?>
    <form method="POST" action="book_tour.php">
        <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
        <button type="submit" name="book_tour" class="submit-btn">Book Now</button>
    </form>
<?php endif; ?>

            </form>
        </div>
    </div>
</section>


    <!-- Footer -->
    <footer id="about-us-footer">
        <div class="TOURfooterContainer">
            <div class="TOURsocialIcons">
                <a href=""><i class="fa-brands fa-facebook"></i></a>
                <a href=""><i class="fa-brands fa-instagram"></i></a>
                <a href=""><i class="fa-brands fa-twitter"></i></a>
                <a href=""><i class="fa-brands fa-youtube"></i></a>
            </div>
            <div class="TOURfooterNav">
                <ul>
                    <li><a href="users/home.php">Home</a></li>
                    <li><a href="">About Us</a></li>
                    <li><a href="">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="TOURfooterBottom">
            <p>Copyright &copy;2024; Designed by <span class="TOURdesigner">CASSanga</span></p>
        </div>
    </footer>
    

<script>
// Adjust adventurer fields dynamically
document.getElementById('num_people').addEventListener('input', function () {
    const numPeople = parseInt(this.value, 10) || 0;
    const adventurerList = document.getElementById('adventurer_list');

    // Adjust the adventurer inputs based on number of people
    while (adventurerList.children.length < numPeople - 1) {
        const li = document.createElement('li');
        li.innerHTML = `
                <div class="input-row">
                    <div class="inputbox">
                        <label for="name">Name:</label>
                        <input type="name" name="adventurer_name]" placeholder="Enter name" required>
                    </div>
                    <div class="inputbox">
                        <label for="email">Email:</label>
                        <input type="email" name="adventurer_emails[]" placeholder="Enter email" required>
                    </div>
                    <div class="inputbox">
                        <label for="phone">Phone Number:</label>
                        <input type="text" name="adventurer_phones[]" placeholder="Enter phone number" required>
                    </div>
                    <div class="inputbox">
                        <label for="age">Age:</label>
                        <input type="number" name="adventurer_ages[]" placeholder="Enter age" required>
                    </div>
                    <div class="inputbox">
                        <label for="address">Address:</label>
                        <input type="text" name="adventurer_address[]" placeholder="Enter address" required>
                    </div>
                </div>
            `;
        adventurerList.appendChild(li);
    }
    while (adventurerList.children.length > numPeople - 1) {
        adventurerList.removeChild(adventurerList.lastChild);
    }
});

// Manage file uploads and previews
let selectedFiles = []; // Track selected files

document.getElementById('id_upload').addEventListener('change', function (event) {
    const fileInput = event.target;
    const newFiles = Array.from(fileInput.files);
    const previewContainer = document.getElementById('photo_preview');
    const newDataTransfer = new DataTransfer();

    // Add new files to the selectedFiles array
    selectedFiles = [...selectedFiles, ...newFiles];

    // Remove duplicates by checking filenames
    selectedFiles = selectedFiles.reduce((acc, file) => {
        if (!acc.some(f => f.name === file.name)) acc.push(file);
        return acc;
    }, []);

    // Clear preview and render all selected files
    previewContainer.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            // Create image preview
            const imgWrapper = document.createElement('div');
            imgWrapper.style.position = 'relative';
            imgWrapper.style.display = 'inline-block';
            imgWrapper.style.marginRight = '10px';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.border = '1px solid #ccc';
            img.style.borderRadius = '5px';

            // Create delete button
            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = 'X';
            deleteBtn.style.position = 'absolute';
            deleteBtn.style.top = '5px';
            deleteBtn.style.right = '5px';
            deleteBtn.style.backgroundColor = 'red';
            deleteBtn.style.color = 'white';
            deleteBtn.style.border = 'none';
            deleteBtn.style.borderRadius = '50%';
            deleteBtn.style.cursor = 'pointer';

            deleteBtn.addEventListener('click', () => {
                // Remove file preview and update file input
                selectedFiles.splice(index, 1);
                updateFileInput(fileInput, selectedFiles);
                imgWrapper.remove();
            });

            imgWrapper.appendChild(img);
            imgWrapper.appendChild(deleteBtn);
            previewContainer.appendChild(imgWrapper);
        };
        reader.readAsDataURL(file);

        // Add file to DataTransfer object for input field
        newDataTransfer.items.add(file);
    });

    // Update the file input with all selected files
    fileInput.files = newDataTransfer.files;
});

// Update the file input field
function updateFileInput(fileInput, files) {
    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
}
</script>


<script>
        window.onload = function () {
            if (window.location.hash) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            window.scrollTo(0, 0);
        };
    </script>

</body>
</html>