<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot_time = trim($_POST['slot_time']);
    $notes = trim($_POST['notes'] ?? '');

    if (empty($slot_time)) {
        $message = "❌ Please provide a valid slot time.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM appointments WHERE slot_time = ?");
        $stmt->execute([$slot_time]);

        if ($stmt->rowCount() > 0) {
            $message = "❌ Slot already booked. Please choose another.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, slot_time, notes) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['patient_id'], $slot_time, $notes]);
            $message = "✅ Appointment booked successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📅 Book Appointment</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background: linear-gradient(135deg, #667eea, #764ba2); }
            50% { background: linear-gradient(135deg, #764ba2, #667eea); }
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: white;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            color: #444;
            display: block;
            margin-bottom: 6px;
            margin-top: 10px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        input:disabled {
            background: #f1f1f1;
            color: #555;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            width: 100%;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 12px;
            border: none;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(45deg, #20c997, #28a745);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-out;
        }

        .message.success { color: #28a745; }
        .message.error { color: #dc3545; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .links {
            margin-top: 20px;
            text-align: center;
        }

        .links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div>
    <h2>🦷 Book an Appointment</h2>

    <?php if ($message): ?>
        <p class="message <?= str_contains($message, '✅') ? 'success' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Date:</label>
        <input type="date" name="date_display" value="<?= htmlspecialchars($date) ?>" disabled>

        <label>Time:</label>
        <input type="time" name="time_display" value="<?= htmlspecialchars($time) ?>" disabled>

        <label>Reason / Notes (optional):</label>
        <textarea name="notes" placeholder="E.g., Toothache, cleaning, etc."></textarea>

        <input type="hidden" name="slot_time" value="<?= $date . ' ' . $time ?>">

        <button type="submit">✅ Confirm Appointment</button>
    </form>

    <div class="links">
        <a href="index.php">← Back to Slot View</a> |
        <a href="dashboard_patient.php">🏠 Dashboard</a>
    </div>
</div>

</body>
</html>
