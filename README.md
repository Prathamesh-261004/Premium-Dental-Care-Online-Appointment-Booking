🦷 Premium Dental Care Appointment System

Premium Dental Care is a dynamic, full-stack PHP-based web application designed for efficient appointment booking, patient management, and administrative control in a dental clinic environment. It features a responsive interface for patients to register and book time slots and for admins to manage appointments, upload prescriptions, block/unblock slots, and send reminders.

🔧 Technologies Used
Backend: PHP (with PDO for MySQL)

Database: MySQL

Frontend: HTML5, Inline CSS, JavaScript

File Uploads: Patient photos and prescription files

Email Support: Appointment reminders (via reminder_cron.php)

📁 Folder Structure

/your_project_folder/
│
├── db.php                   # Database connection file
├── register.php             # Patient registration form
├── login.php                # Patient login
├── logout.php               # Session logout
├── book_appointment.php     # Slot booking processor
├── dashboard_patient.php    # Patient dashboard with appointments
├── upload_prescription.php  # Admin uploads prescription
├── admin_login.php          # Admin login
├── dashboard_admin.php      # Admin dashboard
├── reminder_cron.php        # Sends email reminders for appointments
├── uploads/                 # Stores patient photos & prescriptions
└── ...                      # Other related files
⚙️ Setup Instructions
Clone or Copy the Project

Copy all files into your local server directory (e.g., htdocs/ for XAMPP).

Database Setup

Create a MySQL database (e.g., dental_clinic)

Import the SQL file:


-- patients table
CREATE TABLE patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  gender VARCHAR(10),
  dob DATE,
  phone VARCHAR(20),
  address TEXT,
  city VARCHAR(50),
  state VARCHAR(50),
  photo VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- appointments table
CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT,
  slot_time DATETIME,
  notes TEXT,
  prescription_file VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- blocked_slots table
CREATE TABLE blocked_slots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  block_date DATE,
  block_time TIME,
  reason VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
Configure DB Connection

Open db.php and update database credentials:


<?php
$host = 'localhost';
$db = 'dental_clinic';
$user = 'root';
$pass = '';
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
✅ Features
🧑 Patient
Register with photo

Secure login

Book appointment (9AM–5PM, skips 1PM–2PM lunch)

View booked slots

Download/view prescription

🛡️ Admin
Secure login (admin / 12345)

View total patients & appointments

Send reminders for next-day appointments

Block/unblock time slots (e.g., lunch, leave)

View all appointments

Upload/view prescriptions

📨 Reminders (Optional)
reminder_cron.php can be called manually or scheduled via CRON

Sends appointment reminders 1 day prior using PHPMailer

🖼 UI Highlights
Gradient backgrounds with animated effects

Card-style date layout for booking

Clean dashboard design

Fully responsive & mobile friendly

Hover effects, loaders, smooth animations

🔐 Admin Credentials
Username: admin

Password: 12345

⚠️ You should change these credentials in admin_login.php for production.

📌 Final Notes
Tested on PHP 8+, MySQL 5.7+

Compatible with XAMPP, MAMP, WAMP

File uploads require uploads/ folder to have write permissions
