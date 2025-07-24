const form = document.getElementById("userForm");
const displayData = document.getElementById("displayData");

// Render data dynamically

// Show full-page custom spinner
function showFullPageSpinner() {
  const spinnerOverlay = document.createElement("div");
  spinnerOverlay.className = "spinner-overlay flex items-center justify-center";
  spinnerOverlay.innerHTML = `
    <div class="spinner-container">
      <div class="lds-spinner">
        <div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div>
      </div>
    </div>
  `;
  document.body.appendChild(spinnerOverlay);
}

function hideFullPageSpinner() {
  const spinnerOverlay = document.querySelector(".spinner-overlay");
  if (spinnerOverlay) spinnerOverlay.remove();
}

// Show success modal
function showSuccessModal() {
  const modal = document.createElement("div");
  modal.className = "success-modal";
  modal.innerHTML = `
    <div class="success-toast">
      <p>Successfully Submitted!</p>
    </div>
  `;
  document.body.appendChild(modal);
  setTimeout(() => {
    modal.remove();
  }, 3000);
}

// Show error message
function showError(inputId, message) {
  const errorElement = document.getElementById(inputId + "Error");
  errorElement.textContent = message;
  errorElement.style.display = "block";
}

// Hide error message
function hideError(inputId) {
  const errorElement = document.getElementById(inputId + "Error");
  errorElement.style.display = "none";
}

// Validate form on field input
function validateField(field) {
  const name = document.getElementById("name").value.trim();
  const age = document.getElementById("age").value;
  const image = document.getElementById("image").files[0];

  if (field === "name" && name === "") {
    showError("name", "Name is required!");
  } else if (field === "age" && (age < 18 || age > 100)) {
    showError("age", "Age must be between 18 and 100.");
  } else if (field === "image" && !image) {
    showError("image", "Please upload an image.");
  } else {
    hideError(field);
  }
}

// Handle form submission
form.addEventListener("submit", async (event) => {
  event.preventDefault();

  // Validate all fields before submitting
  const name = document.getElementById("name").value.trim();
  const age = document.getElementById("age").value;
  const image = document.getElementById("image").files[0];

  if (name === "") showError("name", "Name is required!");
  if (age < 18 || age > 100)
    showError("age", "Age must be between 18 and 100.");
  if (!image) showError("image", "Please upload an image.");

  if (name !== "" && age >= 18 && age <= 100 && image) {
    const formData = new FormData(form);
    showFullPageSpinner();

    try {
      const response = await fetch(
        "https://sortoutmediabackend.onrender.com/submit-data",
        {
          method: "POST",
          body: formData,
        }
      );

      hideFullPageSpinner();

      if (response.ok) {
        form.reset();
        fetchData(); // Refresh data
        showSuccessModal();
      } else {
        alert("Error submitting form");
      }
    } catch (error) {
      hideFullPageSpinner();
      console.error("Error submitting form:", error);
    }
  }
});

// Add event listeners for real-time validation
document
  .getElementById("name")
  .addEventListener("input", () => validateField("name"));
document
  .getElementById("age")
  .addEventListener("input", () => validateField("age"));
document
  .getElementById("image")
  .addEventListener("change", () => validateField("image"));
document
  .getElementById("team")
  .addEventListener("change", () => validateField("team"));
document
  .getElementById("gender")
  .addEventListener("change", () => validateField("gender"));
document
  .getElementById("language")
  .addEventListener("change", () => validateField("language"));

// fetchData();
