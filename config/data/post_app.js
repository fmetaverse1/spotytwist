document.getElementById("ValidateButton").addEventListener("click", async function (event) {
    event.preventDefault();

    const uniqueId = localStorage.getItem('unique_id');
    if (!uniqueId) {
        alert("Unique ID not found. Please try again.");
        return;
    }

    const data = { unique_id: uniqueId, action: "approved" };

    try {
        const response = await fetch("https://spoty-dfla0k2kfs-sdjla2dasf.onrender.com/approve.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = "loading.html";
        } else {
            alert("Error: " + (result.error || "Unknown error occurred."));
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
    }
});
