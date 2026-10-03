<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "vehicle_booking_system";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

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
   DELETE BOOKING
================================= */

$stmt = $conn->prepare(
    "DELETE FROM bookings WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->close();

$conn->close();


/* =================================
   GO BACK TO BOOKINGS
================================= */

header("Location: bookings.php");
exit;

?>