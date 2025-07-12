<?php
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isSlotAvailable($pdo, $datetime) {
    $stmt = $pdo->prepare("SELECT id FROM appointments WHERE slot_time = ?");
    $stmt->execute([$datetime]);
    return $stmt->rowCount() === 0;
}

function formatDateTime($datetime) {
    return date("d M Y, h:i A", strtotime($datetime));
}
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
          /*$mail->Username   = 'your-email@gmail.com';
        $mail->Password   = 'your-app-password';  // Use app password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('your-email@gmail.com', 'Dental Clinic');*/
        
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: {$mail->ErrorInfo}");
    }
}
