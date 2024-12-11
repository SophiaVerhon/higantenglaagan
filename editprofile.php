<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="editprofile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Profile Account</title>
</head>
<body>

<div class="loginbackground" style="background-image: url('image/bgforest1.jpg'); 
background-size: cover; background-position: center; height: 100vh; 
position: absolute; top: 0; left: 0; width: 100%; z-index: -1;"></div>

<header class="TOURmain-header">
  <div class="TOURheader-logo-text">
    <img src="image/logo.png" alt="Logo" class="TOURlogo-image">
    <span class="TOURheader-text">Higanteng Laagan Travel & Tours</span>
  </div>
  <nav class="TOURheader-navHP">
    <a href="home.php" class="TOURnav-linkHP">GO BACK TO HOMEPAGE</a>
    <div class="TOURdropdown">
      <span class="TOURnav-linkHP dropdown-toggle" onclick="toggleDropdown('profile-dropdown')">MY PROFILE</span>
      <div id="profile-dropdown" class="TOURdropdown-menu">
        <a href="profile.php" class="TOURdropdown-item">My Account</a>
        <a href="bkstatus.php" class="TOURdropdown-item">Booking Status</a>
        <a href="index.php" class="TOURdropdown-item">Log Out</a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="profile-container">
    <!-- Profile Section -->
    <section class="profile-info">
      <h2>Profile Information</h2>
      <div class="profile-pic">
        <!-- Static placeholder image for profile picture -->
        <img src="image/userdp.png" alt="user dp" class="profile-image">
      </div>
      <form class="profile-form">
        <div class="form-group">
          <label for="username"><strong>Username:</strong></label>
          <!-- Remove readonly to make it editable -->
          <input type="text" id="username" name="username" value="user123">
        </div>
        <div class="form-group">
          <label for="email"><strong>Email:</strong></label>
          <!-- Remove readonly to make it editable -->
          <input type="email" id="email" name="email" value="example@email.com">
        </div>
        <div class="form-group">
          <label for="password"><strong>Password:</strong></label>
          <!-- Remove readonly to make it editable -->
          <input type="password" id="password" name="password" value="**********">
        </div>

        <!-- Centered Buttons inside the form -->
        <div class="form-buttons">
          <button type="button" class="cancel-button" onclick="window.location.href='profile.php';">Cancel</button>
          <button type="submit" class="save-button">Save Changes</button>
        </div>
      </form>
    </section>
  </div>
</main> 


<!-- JavaScript for dropdown -->
<script>
    function toggleDropdown(menuId) {
    const dropdown = document.getElementById(menuId);
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

// Close dropdown if clicked outside
    window.onclick = function (event) {
        if (!event.target.matches('.dropdown-toggle')) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
            dropdown.style.display = 'none';
            });
            }
        }

  function editAccount() {
    window.location.href = 'edit-account.php';
  }

  function deleteAccount() {
    if (confirm('Are you sure you want to delete your account?')) {
      window.location.href = 'delete-account.php';
    }
  }
</script>

</body>
</html>