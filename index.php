<?php

// ===============================
// DATABASE CONNECTION
// ===============================

$host = "localhost";
$username = "root";
$password = "";
$database = "vehicle_booking_system";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// ===============================
// HANDLE BOOKING FORM
// ===============================

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $vehicleId = intval($_POST["vehicle_id"] ?? 0);
    $bookingDate = $_POST["booking_date"] ?? "";

    if (
        $fullName === "" ||
        $email === "" ||
        $phone === "" ||
        $vehicleId <= 0 ||
        $bookingDate === ""
    ) {
        $message = "Please fill in all fields.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";
    } elseif (strlen($phone) < 10) {
        $message = "Please enter a valid phone number.";
        $messageType = "danger";
    } else {

        // Check whether vehicle exists
        $vehicleQuery = $conn->prepare(
            "SELECT name, price_per_day FROM vehicles WHERE id = ?"
        );

        $vehicleQuery->bind_param("i", $vehicleId);
        $vehicleQuery->execute();

        $vehicleResult = $vehicleQuery->get_result();

        if ($vehicleResult->num_rows === 0) {

            $message = "Selected vehicle was not found.";
            $messageType = "danger";

        } else {

            $vehicle = $vehicleResult->fetch_assoc();

            // Insert booking
            $bookingQuery = $conn->prepare(
                "INSERT INTO bookings
                (full_name, email, phone, vehicle_id, booking_date)
                VALUES (?, ?, ?, ?, ?)"
            );

            $bookingQuery->bind_param(
                "sssis",
                $fullName,
                $email,
                $phone,
                $vehicleId,
                $bookingDate
            );

            if ($bookingQuery->execute()) {

                $message =
                    "Booking confirmed successfully for " .
                    htmlspecialchars($vehicle["name"]) .
                    " on " .
                    htmlspecialchars($bookingDate) .
                    ".";

                $messageType = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $messageType = "danger";
            }

            $bookingQuery->close();
        }

        $vehicleQuery->close();
    }
}


// ===============================
// GET VEHICLES
// ===============================

$vehicles = [];

$result = $conn->query(
    "SELECT id, name, category, price_per_day, description
     FROM vehicles
     ORDER BY id"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>VehicleGo - Online Vehicle Booking</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="css/style.css">

</head>

<body>


<!-- ===============================
     NAVBAR
================================ -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="#">
            VehicleGo
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div
            class="collapse navbar-collapse"
            id="navbarContent">

            <ul class="navbar-nav ms-auto">

    <li class="nav-item">
        <a
            class="nav-link"
            href="#">
            Home
        </a>
    </li>

    <li class="nav-item">
        <a
            class="nav-link"
            href="#vehicles">
            Vehicles
        </a>
    </li>

    <li class="nav-item">
        <a
            class="nav-link"
            href="#booking">
            Book Now
        </a>
    </li>

    <li class="nav-item">
        <a
            class="nav-link"
            href="bookings.php">
            Manage Bookings
        </a>
    </li>

</ul>

        </div>

    </div>

</nav>


<!-- ===============================
     HERO
================================ -->

<section class="hero-section">

    <div class="container text-center">

        <h1>
            Online Vehicle Booking System
        </h1>

        <p>
            Book your preferred vehicle quickly and easily.
        </p>

        <a
            href="#vehicles"
            class="btn btn-primary btn-lg">
            Browse Vehicles
        </a>

    </div>

</section>


<!-- ===============================
     VEHICLES
================================ -->

<section
    id="vehicles"
    class="container py-5">

    <h2 class="text-center mb-5">
        Available Vehicles
    </h2>

    <div class="row g-4">

        <?php foreach ($vehicles as $vehicle): ?>

            <div class="col-lg-4 col-md-6">

                <div class="card vehicle-card h-100">

                    <div class="vehicle-image">

                        <?php
                        $images = [
                            "Car",
                            "SUV",
                            "Bike"
                        ];

                        $imageText =
                            $images[($vehicle["id"] - 1) % count($images)];
                        ?>

                        <span>
                            <?php echo $imageText; ?>
                        </span>

                    </div>

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            <?php echo htmlspecialchars($vehicle["category"]); ?>
                        </span>

                        <h4 class="card-title">
                            <?php echo htmlspecialchars($vehicle["name"]); ?>
                        </h4>

                        <p class="card-text text-muted">
                            <?php echo htmlspecialchars($vehicle["description"]); ?>
                        </p>

                        <h5>
                            ₹<?php echo number_format($vehicle["price_per_day"]); ?>
                            <small class="text-muted">
                                / day
                            </small>
                        </h5>

                        <a
                            href="#booking"
                            class="btn btn-primary w-100 mt-3 select-vehicle"
                            data-vehicle="<?php echo $vehicle["id"]; ?>">

                            Book This Vehicle

                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>


<!-- ===============================
     BOOKING
================================ -->

<section
    id="booking"
    class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow booking-card">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4">
                        Book Your Vehicle
                    </h2>


                    <!-- Message -->

                    <?php if ($message !== ""): ?>

                        <div
                            class="alert alert-<?php echo $messageType; ?>">

                            <?php echo $message; ?>

                        </div>

                    <?php endif; ?>


                    <form
                        method="POST"
                        id="bookingForm">


                        <!-- Name -->

                        <div class="mb-3">

                            <label
                                for="full_name"
                                class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="full_name"
                                name="full_name"
                                placeholder="Enter your full name"
                                required>

                        </div>


                        <!-- Email -->

                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Enter your email"
                                required>

                        </div>


                        <!-- Phone -->

                        <div class="mb-3">

                            <label
                                for="phone"
                                class="form-label">
                                Phone Number
                            </label>

                            <input
                                type="tel"
                                class="form-control"
                                id="phone"
                                name="phone"
                                placeholder="Enter phone number"
                                required>

                        </div>


                        <!-- Vehicle -->

                        <div class="mb-3">

                            <label
                                for="vehicle_id"
                                class="form-label">
                                Select Vehicle
                            </label>

                            <select
                                class="form-select"
                                id="vehicle_id"
                                name="vehicle_id"
                                required>

                                <option value="">
                                    Select a vehicle
                                </option>

                                <?php foreach ($vehicles as $vehicle): ?>

                                    <option
                                        value="<?php echo $vehicle["id"]; ?>">

                                        <?php echo htmlspecialchars($vehicle["name"]); ?>
                                        -
                                        ₹<?php echo number_format($vehicle["price_per_day"]); ?>/day

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Date -->

                        <div class="mb-3">

                            <label
                                for="booking_date"
                                class="form-label">
                                Booking Date
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="booking_date"
                                name="booking_date"
                                required>

                        </div>


                        <!-- Submit -->

                        <button
                            type="submit"
                            class="btn btn-success w-100">

                            Confirm Booking

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ===============================
     FOOTER
================================ -->

<footer class="bg-dark text-white text-center py-4">

    <p class="mb-0">
        © 2026 VehicleGo. All rights reserved.
    </p>

</footer>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<!-- Custom JS -->

<script src="js/script.js"></script>

</body>
</html>

<?php

$conn->close();

?>