document.addEventListener("DOMContentLoaded", () => {
    const deleteModal = document.getElementById("deleteModal");
    const cancelButton = document.getElementById("cancelButton");
    const confirmButton = document.getElementById("confirmButton");
    let selectedTourId = null;

    // Open modal on delete button click
    document.querySelectorAll(".delete-button").forEach(button => {
        button.addEventListener("click", () => {
            selectedTourId = button.getAttribute("data-id");
            deleteModal.style.display = "block";
        });
    });

    // Cancel deletion
    cancelButton.addEventListener("click", () => {
        deleteModal.style.display = "none";
        selectedTourId = null;
    });

    // Confirm deletion
    confirmButton.addEventListener("click", () => {
        if (selectedTourId) {
            // Redirect to the delete PHP script
            window.location.href = `tour_delete.php?tour_id=${selectedTourId}`;
        }
    });

    // Close modal when clicking outside of it
    window.addEventListener("click", (event) => {
        if (event.target === deleteModal) {
            deleteModal.style.display = "none";
        }
    });
});
