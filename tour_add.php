
<?php
include('db_connect.php');
session_start();

$message = "";
$tour_type = isset($_POST['tour_type']) ? $_POST['tour_type'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_name = $_POST['tour_name'];
    $description = $_POST['description'];
    $price_per_person = $_POST['price_per_person'];
    $location = $_POST['location'];
    $max_bookings = $_POST['max_bookings'];
    $min_bookings = NULL;
    $start_date = NULL;
    $end_date = NULL;

    if ($tour_type == 1) { // Exclusive tour
        $min_bookings = $_POST['min_bookings'] ?? NULL;
    
        // Allow NULL for exclusive dates if they are not set
        $start_date = !empty($_POST['exclusive_start_date']) ? $_POST['exclusive_start_date'] : NULL;
        $end_date = !empty($_POST['exclusive_end_date']) ? $_POST['exclusive_end_date'] : NULL;
    } else { // Regular tour
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
    }
    if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == 0) {
        $image_tmp_name = $_FILES['tour_image']['tmp_name'];
        $image_data = file_get_contents($image_tmp_name);
    } else {
        $message = "Please upload a valid image.";
    } // Get image data as binary

        // Get Tour Guide details
        $guide_names = $_POST['guide_name']; // Array of guide names
        $guide_contact_nos = $_POST['guide_contact_no']; // Array of guide contact numbers

        $query = "INSERT INTO tour (tour_name, description, start_date, end_date, price_per_person, location, max_bookings, is_exclusive, image_path, min_bookings) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        $is_exclusive = ($tour_type == 1) ? 1 : 0; 
        $stmt->bind_param(
            "ssssdssssi",
            $tour_name,
            $description,
            $start_date,
            $end_date,
            $price_per_person,
            $location,
            $max_bookings,
            $is_exclusive,
            $image_data,
            $min_bookings
        );

        if (!$stmt) {
            echo "Prepare Error: " . $conn->error . "<br>";
        }

        if ($stmt->execute()) {
            $tour_id = $conn->insert_id; 

            // Insert each guide into the tourguide table and assign them to the tour
                            // Insert each guide into the tourguide table and assign them to the tour
                for ($i = 0; $i < count($guide_names); $i++) {
                    $guide_name = $guide_names[$i];
                    $guide_contact_no = $guide_contact_nos[$i];

                    // Check if the guide already exists
                    $check_guide_query = "SELECT guide_id FROM tourguide WHERE name = ? AND contact_no = ?";
                    $stmt_check = $conn->prepare($check_guide_query);
                    $stmt_check->bind_param("ss", $guide_name, $guide_contact_no);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows > 0) {
                        // Guide exists, get the guide_id
                        $row = $result_check->fetch_assoc();
                        $guide_id = $row['guide_id'];
                    } else {
                        // Guide doesn't exist, insert a new one
                        $guide_query = "INSERT INTO tourguide (name, contact_no) VALUES (?, ?)";
                        $stmt_guide = $conn->prepare($guide_query);
                        $stmt_guide->bind_param("ss", $guide_name, $guide_contact_no);
                        $stmt_guide->execute();
                        $guide_id = $stmt_guide->insert_id; // Get the newly inserted guide ID
                    }

                    // Assign the guide to the current tour
                    $assign_guide_query = "INSERT INTO tour_guide_assignment (tour_id, guide_id) VALUES (?, ?)";
                    $stmt_assign = $conn->prepare($assign_guide_query);
                    $stmt_assign->bind_param("ii", $tour_id, $guide_id);
                    $stmt_assign->execute();
                }


            header("Location: tour_add.php?success=true");
            exit();
        } else {
            $message = "Error adding tour: " . $stmt->error;
        }

        $stmt->close();
        if (isset($stmt_guide)) $stmt_guide->close();
        if (isset($stmt_assign)) $stmt_assign->close();
   
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Tour</title>
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/addtour_style.css">
</head>
<body>

<div class="navbar">
    <a href="admin_home.php">Home</a>
    <a href="admin_tour.php">Tours</a>
    <a href="admin_about.php">About Us</a>
    <a href="review.php">Review</a>
    <a href="tour_add.php">+ Add New Tour</a>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

<button class="close-button" onclick="window.location.href='admin_tour.php';">×</button>
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Success</h2>
        <p>Tour added successfully!</p>
        <button class="ok-btn" id="okBtn">OK</button>
    </div>
</div>

<div class="container">
    <h2>Add New Tour</h2>

    <?php if ($message != "") { echo "<p class='message'>$message</p>"; } ?>

    <form action="tour_add.php" method="POST" onsubmit="return validateDates()" class="tour-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="tour_image">Tour Image:</label>
            <input type="file" name="tour_image" id="tour_image" accept="image/*" required>
        </div>

        <div class="form-group">
            <label for="tour_name">Tour Name:</label>
            <input type="text" name="tour_name" id="tour_name" required>
        </div>

        <div class="form-group">
            <label for="description">Description (Use markers for sections):</label>
            <textarea name="description" id="description" required placeholder="#Note# #Itinerary# #Inclusions# #Exclusions# #CancellationPolicy#"></textarea>
        </div>

        <div class="form-group">
            <label for="price_per_person">Price per Person:</label>
            <input type="number" name="price_per_person" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" name="location" id="location" required>
        </div>

        <div class="form-group">
            <label for="max_bookings">Booking Limit:</label>
            <input type="number" name="max_bookings" id="max_bookings" value="0" required>
        </div>

        <div class="form-group">
        <label for="tour_type">Tour Type:</label>
        <select id="tour_type" name="tour_type" required onchange="toggleExclusiveFields()">
            <option value="0">Regular Tour</option>
            <option value="1">Exclusive Tour</option>
        </select>
    </div>
    <div id="exclusiveFields" style="display: none;">
    <div class="form-group">
        <label for="min_bookings">Minimum Bookings (Admin sets for exclusive tours):</label>
        <input type="number" name="min_bookings" id="min_bookings">
    </div>

    <div class="form-group">
        <label for="exclusive_start_date">Exclusive Start Date:</label>
        <input type="date" name="exclusive_start_date" id="exclusive_start_date">
    </div>

    <div class="form-group">
        <label for="exclusive_end_date">Exclusive End Date:</label>
        <input type="date" name="exclusive_end_date" id="exclusive_end_date">
    </div>
</div>


<div id="regularFields">
    <div class="form-group">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date">
    </div>

    <div class="form-group">
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date">
    </div>
</div>

<div id="guideFields">
    <div class="form-group">
        <label for="guide_name[]">Guide Name:</label>
        <input type="text" name="guide_name[]" required>
    </div>

    <div class="form-group">
        <label for="guide_contact_no[]">Guide Contact Number:</label>
        <input type="text" name="guide_contact_no[]" required>
    </div>
</div>

<!-- Ensure Add Guide button is outside the regularFields -->
<button type="button" onclick="addGuideField()">Add Another Guide</button>

<!-- Ensure Add Tour button is always visible -->
<button type="submit" class="submit-btn">Add Tour</button>
</div>
<script>
    function toggleExclusiveFields() {
        const tourType = document.getElementById("tour_type").value;
        const exclusiveFields = document.getElementById("exclusiveFields");
        const regularFields = document.getElementById("regularFields");

        if (tourType == "1") { // Exclusive Tour
    exclusiveFields.style.display = "block";
    regularFields.style.display = "none";

    // Remove required attributes for regular dates
    document.getElementById("start_date").required = false;
    document.getElementById("end_date").required = false;
    
    // OPTIONAL: Exclusive dates can now be optional
    document.getElementById("exclusive_start_date").required = false;
    document.getElementById("exclusive_end_date").required = false;
} else { // Regular Tour
    exclusiveFields.style.display = "none";
    regularFields.style.display = "block";

    // Set required attributes for regular dates
    document.getElementById("start_date").required = true;
    document.getElementById("end_date").required = true;
}
    }

    // Run on page load to initialize the correct state
    window.onload = function () {
        toggleExclusiveFields();
    };
</script>

<script>
    const today = new Date().toISOString().split("T")[0];
    
    const startDateInput = document.getElementById("start_date");
    const endDateInput = document.getElementById("end_date");
    startDateInput.min = today;
    endDateInput.min = today;

    startDateInput.value = today;

    startDateInput.addEventListener("change", function() {
        const selectedStartDate = startDateInput.value;
        endDateInput.min = selectedStartDate;
    });

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (endDate <= startDate) {
            alert("End date must be after the start date.");
            return false;
        }
        return true;
    }

    function addGuideField() {
        const guideFields = document.getElementById("guideFields");
        const newGuideField = document.createElement("div");

        newGuideField.innerHTML = ` 
            <div class="guide-fields">
                <div class="guide-field">
                    <label for="guide_name[]">Guide Name:</label>
                    <input type="text" name="guide_name[]">
                </div>

                <div class="guide-field">
              <label for="guide_contact_no[]">Guide Contact Number:</label>
                    <input type="text" name="guide_contact_no[]">
                </div>
            </div>
        `;

        guideFields.appendChild(newGuideField);
    }
</script>

<script src="js/modal.js"></script>
</body>
</html>
