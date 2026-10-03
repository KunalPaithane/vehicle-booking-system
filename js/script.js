// ===============================
// SET MINIMUM BOOKING DATE
// ===============================

const bookingDate = document.getElementById("booking_date");

if (bookingDate) {

    const today = new Date();

    const year = today.getFullYear();

    const month = String(
        today.getMonth() + 1
    ).padStart(2, "0");

    const day = String(
        today.getDate()
    ).padStart(2, "0");

    bookingDate.min =
        `${year}-${month}-${day}`;
}


// ===============================
// SELECT VEHICLE
// ===============================

const vehicleButtons =
    document.querySelectorAll(".select-vehicle");

const vehicleSelect =
    document.getElementById("vehicle_id");

vehicleButtons.forEach(function (button) {

    button.addEventListener("click", function () {

        const vehicleId =
            button.dataset.vehicle;

        vehicleSelect.value = vehicleId;

    });

});


// ===============================
// FORM VALIDATION
// ===============================

const bookingForm =
    document.getElementById("bookingForm");

bookingForm.addEventListener(
    "submit",
    function (event) {

        const name =
            document.getElementById("full_name").value.trim();

        const email =
            document.getElementById("email").value.trim();

        const phone =
            document.getElementById("phone").value.trim();

        const vehicle =
            document.getElementById("vehicle_id").value;

        const date =
            document.getElementById("booking_date").value;


        if (
            name === "" ||
            email === "" ||
            phone === "" ||
            vehicle === "" ||
            date === ""
        ) {

            event.preventDefault();

            alert(
                "Please fill in all the fields."
            );

            return;
        }


        if (phone.length < 10) {

            event.preventDefault();

            alert(
                "Please enter a valid phone number."
            );

        }

    }
);
