const successModal = document.getElementById("successModal");
const closeModal = document.getElementById("closeModal");
const okBtn = document.getElementById("okBtn");

if (window.location.search.includes("success=true")) {
    successModal.style.display = "block";
}

closeModal.addEventListener("click", () => {
    successModal.style.display = "none";
    window.location.href = "tour_add.php"; 
});

okBtn.addEventListener("click", () => {
    successModal.style.display = "none";
    window.location.href = "tour_add.php"; // Redirect to clear the query parameter
});

window.addEventListener("click", (event) => {
    if (event.target === successModal) {
        successModal.style.display = "none";
        window.location.href = "tour_add.php"; // Redirect to clear the query parameter
    }
});
