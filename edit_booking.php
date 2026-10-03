<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "vehicle_booking_system";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


/* =================================
   GET BOOKING ID
================================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid booking ID.");
}

$id = (int) $_GET["id"];


/* =================================
   GET BOOKING
================================= */

$stmt = $conn->prepare(
    "SELECT * FROM bookings WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

$stmt->close();


/* =================================
   UPDATE BOOKING
================================= */

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $vehicleId = (int) ($_POST["vehicle_id"] ?? 0);
    $bookingDate = $_POST["booking_date"] ?? "";

    if (
        $fullName === "" ||
        $email === "" ||
        $phone === "" ||
        $vehicleId <= 0 ||
        $bookingDate === ""
    ) {
        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";

    } elseif (strlen($phone) < 10) {
        $error = "Please enter a valid phone number.";

    } else {

        $update = $conn->prepare("
            UPDATE bookings
            SET
                full_name = ?,
                email = ?,
                phone = ?,
                vehicle_id = ?,
                booking_date = ?
            WHERE id = ?
        ");

        $update->bind_param(
            "sssisi",
            $fullName,
            $email,
            $phone,
            $vehicleId,
            $bookingDate,
            $id
        );

        if ($update->execute()) {

            $update->close();

            header("Location: bookings.php");
            exit;

        } else {

            $error = "Update failed: " . $update->error;

            $update->close();
        }
    }
}


/* =================================
   GET VEHICLES
================================= */

$vehicles = [];

$vehicleResult = $conn->query("
    SELECT id, name, price_per_day
    FROM vehicles
    ORDER BY id
");

if ($vehicleResult) {

    while ($row = $vehicleResult->fetch_assoc()) {
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

    <title>Edit Booking - VehicleGo</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="css/style.css">

</head>

<body>

    <!-- ===============================
         NAVBAR
    ================================ -->

    <nav class="navbar navbar-dark bg-dark">

        <div class="container">

            <a
                class="navbar-brand fw-bold"
                href="index.php">

                VehicleGo

            </a>

            <a
                href="bookings.php"
                class="btn btn-outline-light">

                Back to Bookings

            </a>

        </div>

    </nav>


    <!-- ===============================
         EDIT FORM
    ================================ -->

    <section class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-7">

                <div class="card shadow">

                    <div class="card-body p-4">

                        <h2 class="text-center mb-4">
                            Edit Booking
                        </h2>


                        <?php if ($error !== ""): ?>

                            <div class="alert alert-danger">

                                <?php
                                echo htmlspecialchars($error);
                                ?>

                            </div>

                        <?php endif; ?>


                        <form method="POST">


                            <!-- NAME -->

                            <div class="mb-3">

                                <label
                                    for="full_name"
                                    class="form-label">

                                    Full Name

                                </label>

                                <input
                                    type="text"
                                    id="full_name"
                                    name="full_name"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($booking["full_name"]); ?>"
                                    required>

                            </div>


                            <!-- EMAIL -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label">

                                    Email

                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($booking["email"]); ?>"
                                    required>

                            </div>


                            <!-- PHONE -->

                            <div class="mb-3">

                                <label
                                    for="phone"
                                    class="form-label">

                                    Phone Number

                                </label>

                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($booking["phone"]); ?>"
                                    required>

                            </div>


                            <!-- VEHICLE -->

                            <div class="mb-3">

                                <label
                                    for="vehicle_id"
                                    class="form-label">

                                    Select Vehicle

                                </label>

                                <select
                                    id="vehicle_id"
                                    name="vehicle_id"
                                    class="form-select"
                                    required>

                                    <?php foreach ($vehicles as $vehicle): ?>

                                        <option
                                            value="<?php echo (int) $vehicle["id"]; ?>"
                                            <?php
                                            if (
                                                (int) $vehicle["id"] ===
                                                (int) $booking["vehicle_id"]
                                            ) {
                                                echo "selected";
                                            }
                                            ?>>

                                            <?php
                                            echo htmlspecialchars(
                                                $vehicle["name"]
                                            );
                                            ?>

                                            -

                                            ₹<?php
                                            echo number_format(
                                                $vehicle["price_per_day"]
                                            );
                                            ?>/day

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- DATE -->

                            <div class="mb-4">

                                <label
                                    for="booking_date"
                                    class="form-label">

                                    Booking Date

                                </label>

                                <input
                                    type="date"
                                    id="booking_date"
                                    name="booking_date"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($booking["booking_date"]); ?>"
                                    required>

                            </div>


                            <!-- UPDATE -->

                            <button
                                type="submit"
                                class="btn btn-success w-100">

                                Update Booking

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

</body>
</html>

<?php

$conn->close();

?>