<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE patient_id = ? ORDER BY slot_time ASC");
$stmt->execute([$_SESSION['patient_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>🦷 My Appointments</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            padding: 40px;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background: linear-gradient(135deg, #667eea, #764ba2); }
            50% { background: linear-gradient(135deg, #764ba2, #667eea); }
        }

        h2, h3 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            max-width: 800px;
            margin: 0 auto 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .profile img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
        }

        .profile-info p {
            margin: 6px 0;
            font-size: 14px;
            color: #333;
        }

        .actions {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            margin: 10px;
            display: inline-block;
            transition: 0.3s;
        }

        .btn:hover {
            background: linear-gradient(45deg, #0056b3, #007bff);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            border-collapse: collapse;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }

        th {
            background: #667eea;
            color: white;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #b02a37;
            transform: scale(1.05);
        }

        a.view-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a.view-link:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
            color: white;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>🦷 Welcome, <?= htmlspecialchars($patient['name']) ?></h2>

<div class="profile">
    <img src="uploads/<?= htmlspecialchars($patient['photo']) ?>" alt="Patient Photo">
    <div class="profile-info">
        <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($patient['phone']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($patient['gender']) ?></p>
        <p><strong>DOB:</strong> <?= htmlspecialchars($patient['dob']) ?></p>
    </div>
</div>

<div class="actions">
    <a class="btn" href="index.php">📅 Book New Appointment</a>
    <a class="btn" href="logout.php">🚪 Logout</a>
</div>

<h3>📋 Your Appointments</h3>

<?php if ($appointments): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Date & Time</th>
            <th>Notes</th>
            <th>Prescription</th>
            <th>Action</th>
        </tr>
        <?php foreach ($appointments as $i => $app): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= date("d M Y, h:i A", strtotime($app['slot_time'])) ?></td>
                <td><?= htmlspecialchars($app['notes']) ?: '-' ?></td>
                <td>
                    <?php if (!empty($app['prescription_file'])): ?>
                        <a class="view-link" href="uploads/<?= htmlspecialchars($app['prescription_file']) ?>" target="_blank">📄 View</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" action="cancel_appointment.php" onsubmit="return confirm('Cancel this appointment?');">
                        <input type="hidden" name="id" value="<?= $app['id'] ?>">
                        <button type="submit" class="btn-cancel">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>🙁 You have no appointments yet.</p>
<?php endif; ?>

</body>
</html>
