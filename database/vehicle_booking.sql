CREATE DATABASE IF NOT EXISTS vehicle_booking_system;

USE vehicle_booking_system;


-- ===============================
-- VEHICLES TABLE
-- ===============================

CREATE TABLE IF NOT EXISTS vehicles (

    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    category VARCHAR(50) NOT NULL,

    price_per_day DECIMAL(10,2) NOT NULL,

    description VARCHAR(255) NOT NULL

);


-- ===============================
-- BOOKINGS TABLE
-- ===============================

CREATE TABLE IF NOT EXISTS bookings (

    id INT AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(100) NOT NULL,

    email VARCHAR(100) NOT NULL,

    phone VARCHAR(20) NOT NULL,

    vehicle_id INT NOT NULL,

    booking_date DATE NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (vehicle_id)
        REFERENCES vehicles(id)

);


-- ===============================
-- SAMPLE VEHICLES
-- ===============================

INSERT INTO vehicles
(name, category, price_per_day, description)
VALUES

(
    'Honda City',
    'Car',
    2500,
    'Comfortable sedan suitable for city and highway travel.'
),

(
    'Toyota Fortuner',
    'SUV',
    4500,
    'Premium SUV with spacious interiors and powerful performance.'
),

(
    'Royal Enfield Classic 350',
    'Bike',
    1200,
    'Popular motorcycle suitable for city rides and long trips.'
);