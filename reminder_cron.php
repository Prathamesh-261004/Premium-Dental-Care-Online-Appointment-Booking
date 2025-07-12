<?php
require 'db.php';
require 'functions.php'; // must include sendEmail()

$tomorrow = date('Y-m-d', strtotime('+1 day'));

$stmt = $pdo->prepare("
    SELECT a.slot_time, p.name, p.email 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE DATE(a.slot_time) = ?
");
$stmt->execute([$tomorrow]);

$appointments = $stmt->fetchAll();

if (!$appointments) {
    echo "No appointments found for tomorrow.";
    return;
}

foreach ($appointments as $appt) {
    $to = $appt['email'];
   $subject = "Appointment Reminder for Tomorrow";

$message = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; }
    .container { padding: 20px; background-color: #fff; border-radius: 8px; max-width: 600px; margin: auto; border: 1px solid #ddd; }
    .header { background-color: #007bff; color: white; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; }
    .content { padding: 20px; }
    .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'>
      <h2>🦷 Dentist Appointment Reminder</h2>
    </div>
    <div class='content'>
      <p>Dear <strong>{$appt['name']}</strong>,</p>

      <p>This is a kind reminder that you have a dental appointment scheduled as follows:</p>

      <ul>
        <li><strong>Date:</strong> " . date("l, d M Y", strtotime($appt['slot_time'])) . "</li>
        <li><strong>Time:</strong> " . date("h:i A", strtotime($appt['slot_time'])) . "</li>
      </ul>

      <p>📍 Please arrive at the clinic at least <strong>10 minutes early</strong> to complete any necessary formalities.</p>

      <p>📝 If you have any questions or need to reschedule, feel free to contact us in advance.</p>

      <p>Looking forward to seeing you!</p>

      <p style='margin-top:30px;'>Warm regards,<br><strong>Dentist Clinic</strong></p>
    </div>
    <div class='footer'>
      This is an automated reminder. Please do not reply to this email.
    </div>
  </div>
</body>
</html>
";


    sendEmail($to, $subject, $message);
}

echo "✅ Reminders sent to " . count($appointments) . " patient(s).";
?>
