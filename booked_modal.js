document.addEventListener('DOMContentLoaded', function() {
    // Handle the book now button click event
    document.querySelectorAll('.book-now-button').forEach(button => {
        button.addEventListener('click', function(e) {
            // If the tour is fully booked, show the modal instead of redirecting
            if (button.disabled) {
                e.preventDefault(); // Prevent the form submission or redirection
                document.getElementById('fullyBookedModal').style.display = 'block';
            }
        });
    });

    // Close the modal when the user clicks "Close"
    document.getElementById('closeModalButton').onclick = function() {
        document.getElementById('fullyBookedModal').style.display = 'none';
    };
});