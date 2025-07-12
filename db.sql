CREATE DATABASE IF NOT EXISTS dentist_db;
USE dentist_db;

-- Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    gender VARCHAR(10),
    dob DATE,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    slot_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);
ALTER TABLE appointments
ADD COLUMN notes TEXT;

ALTER TABLE appointments ADD COLUMN prescription_file VARCHAR(255);

-- Sample admin login is handled via hardcoded credentials in admin_login.php
CREATE TABLE IF NOT EXISTS blocked_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_date DATE NOT NULL,
    block_time TIME NOT NULL,
    reason VARCHAR(255)
);
