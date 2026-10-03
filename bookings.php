<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "vehicle_booking_system";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT
        bookings.id,
        bookings.full_name,
        bookings.email,
        bookings.phone,
        vehicles.name AS vehicle_name,
        vehicles.category,
        bookings.booking_date,
        bookings.created_at
    FROM bookings
    INNER JOIN vehicles
        ON bookings.vehicle_id = vehicles.id
    ORDER BY bookings.id DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Booking Management - VehicleGo</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="css/style.css">

</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar navbar-dark bg-dark">

        <div class="container">

            <a
                class="navbar-brand fw-bold"
                href="index.php">
                VehicleGo
            </a>

            <a
                href="index.php"
                class="btn btn-outline-light">
                Back to Booking
            </a>

        </div>

    </nav>


    <!-- BOOKING MANAGEMENT -->

    <section class="container py-5">

        <h1 class="text-center mb-4">
            Booking Management
        </h1>

        <div class="card shadow">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-striped table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Vehicle</th>
                                <th>Category</th>
                                <th>Booking Date</th>
                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if ($result->num_rows > 0): ?>

                                <?php while ($booking = $result->fetch_assoc()): ?>

                                    <tr>

                                        <td>
                                            <?php echo (int)$booking["id"]; ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["full_name"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["email"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["phone"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["vehicle_name"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["category"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $booking["booking_date"]
                                            );
                                            ?>
                                        </td>

                                        <td>

                                            <a
                                                href="edit_booking.php?id=<?php echo (int)$booking["id"]; ?>"
                                                class="btn btn-sm btn-warning">
                                                Edit
                                            </a>

                                            <a
                                                href="delete_booking.php?id=<?php echo (int)$booking["id"]; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this booking?');">
                                                Delete
                                            </a>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="8"
                                        class="text-center text-muted">

                                        No bookings found.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>


    <!-- FOOTER -->

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