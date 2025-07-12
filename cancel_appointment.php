<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id']) || !isset($_POST['id'])) {
    header("Location: login.php");
    exit;
}

$appointmentId = $_POST['id'];
$patientId = $_SESSION['patient_id'];

// Fetch appointment and validate ownership
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND patient_id = ?");
$stmt->execute([$appointmentId, $patientId]);
$appointment = $stmt->fetch();

if (!$appointment) {
    echo "<script>alert('❌ Appointment not found.'); window.location='dashboard_patient.php';</script>";
    exit;
}

// Check 12-hour restriction
$appointmentTime = strtotime($appointment['slot_time']);
$now = time();

if ($appointmentTime - $now < 12 * 3600) {
    echo "<script>alert('⛔ You can only cancel appointments at least 12 hours in advance.'); window.location='dashboard_patient.php';</script>";
    exit;
}

// Proceed with delete
$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND patient_id = ?");
$stmt->execute([$appointmentId, $patientId]);

echo "<script>alert('✅ Appointment cancelled.'); window.location='dashboard_patient.php';</script>";
exit;
?>
