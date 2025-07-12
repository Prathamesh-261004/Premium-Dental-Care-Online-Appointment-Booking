<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
require 'db.php';

$reminderStatus = "";
if (isset($_POST['send_reminders'])) {
    ob_start();
    include 'reminder_cron.php';
    $reminderStatus = ob_get_clean();
}

// Stats
$totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();

// Fetch Patients
$patients = $pdo->query("SELECT * FROM patients ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Appointments + Patient
$appointments = $pdo->query("
    SELECT a.*, p.name, p.email 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    ORDER BY a.slot_time DESC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unblock']) && isset($_POST['unblock_id'])) {
    $unblockId = intval($_POST['unblock_id']);

    $stmt = $pdo->prepare("DELETE FROM blocked_slots WHERE id = ?");
    if ($stmt->execute([$unblockId])) {
        // Redirect to same page to avoid form resubmission and reload cleanly
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<div class='status' style='background:#f8d7da; color:#721c24; border-color:#f5c6cb;'>❌ Failed to unblock the slot. Please try again.</div>";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>🛡️ Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            animation: bgShift 10s ease infinite;
            padding: 30px;
        }

        @keyframes bgShift {
            0%, 100% { background: linear-gradient(135deg, #667eea, #764ba2); }
            50% { background: linear-gradient(135deg, #764ba2, #667eea); }
        }

        h2, h3, h4 {
            color: #fff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        a { color: #fff; text-decoration: none; }

        .box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: linear-gradient(45deg, #0056b3, #007bff);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,123,255,0.3);
        }

        .status {
            padding: 12px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            margin-top: 15px;
            border-radius: 8px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 10px;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background: #007bff;
            color: white;
        }

        input, select {
            padding: 8px;
            margin: 5px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
            font-size: 14px;
        }

        input[type="file"] {
            border: none;
        }

        form.upload-form {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        form[action="upload_prescription.php"] button {
            padding: 6px 12px;
            font-size: 13px;
        }

        button[name="unblock"] {
            background: #dc3545;
        }

        button[name="unblock"]:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<h2>🛡️ Admin Dashboard</h2>

<div class="box">
    <p><strong>👤 Total Patients:</strong> <?= $totalPatients ?></p>
    <p><strong>📅 Total Appointments:</strong> <?= $totalAppointments ?></p>
</div>

<div class="box">
    <form method="POST">
        <button name="send_reminders" class="btn">📧 Send Tomorrow's Appointment Reminders</button>
    </form>
    <?php if ($reminderStatus): ?>
        <div class="status"><?= nl2br(htmlspecialchars($reminderStatus)) ?></div>
    <?php endif; ?>
</div>

<div class="box">
    <h3>👤 Registered Patients</h3>
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Registered On</th></tr>
        <?php foreach ($patients as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['phone']) ?></td>
            <td><?= date("d M Y", strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="box">
    <h3>⛔ Block Specific Slot</h3>
    <form method="POST">
        <label>Date: <input type="date" name="block_date" required></label>
        <label>Time:
            <select name="block_time" required>
                <?php for ($h = 9; $h < 17; $h++): if ($h == 13) continue; ?>
                    <option value="<?= sprintf("%02d:00:00", $h) ?>">
                        <?= date("h:i A", strtotime("$h:00:00")) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </label>
        <label>Reason: <input type="text" name="reason" placeholder="Optional"></label>
        <button class="btn" name="block_slot">🚫 Block</button>
    </form>

    <?php
    if (isset($_POST['block_slot'])) {
        $block_date = $_POST['block_date'];
        $block_time = $_POST['block_time'];
        $reason = $_POST['reason'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO blocked_slots (block_date, block_time, reason) VALUES (?, ?, ?)");
        $stmt->execute([$block_date, $block_time, $reason]);

        echo "<div class='status'>✅ Slot blocked for " . date("d M Y", strtotime($block_date)) . " at " . date("h:i A", strtotime($block_time)) . "</div>";
    }

    $blocked = $pdo->query("SELECT * FROM blocked_slots ORDER BY block_date DESC, block_time")->fetchAll();
    if ($blocked):
    ?>
        <h4>📛 Blocked Slots</h4>
        <table>
            <tr><th>Date</th><th>Time</th><th>Reason</th><th>Action</th></tr>
            <?php foreach ($blocked as $b): ?>
                <tr>
                    <td><?= date("d M Y", strtotime($b['block_date'])) ?></td>
                    <td><?= date("h:i A", strtotime($b['block_time'])) ?></td>
                    <td><?= htmlspecialchars($b['reason']) ?: '-' ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="unblock_id" value="<?= $b['id'] ?>">
                            <button name="unblock" class="btn">❌ Unblock</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif;

    if (isset($_POST['unblock'])) {
        $pdo->prepare("DELETE FROM blocked_slots WHERE id = ?")->execute([$_POST['unblock_id']]);
        echo "<script>location.reload();</script>";
    }
    ?>
</div>

<div class="box">
    <h3>📅 All Appointments</h3>
    <table>
        <tr><th>ID</th><th>Patient</th><th>Slot</th><th>Notes</th><th>Prescription</th><th>Upload</th></tr>
        <?php foreach ($appointments as $app): ?>
        <tr>
            <td><?= $app['id'] ?></td>
            <td><?= htmlspecialchars($app['name']) ?> (<?= htmlspecialchars($app['email']) ?>)</td>
            <td><?= date("d M Y, h:i A", strtotime($app['slot_time'])) ?></td>
            <td><?= htmlspecialchars($app['notes']) ?: '-' ?></td>
            <td>
                <?php if ($app['prescription_file']): ?>
                   <a href="uploads/<?= $app['prescription_file'] ?>" target="_blank"
   style="display:inline-block; padding:6px 12px; background:#007bff; color:#fff; text-decoration:none;
          border-radius:4px; font-size:13px; transition:background 0.3s;">
   📄 View
</a>

                <?php else: ?>—
                <?php endif; ?>
            </td>
            <td>
                <form class="upload-form" method="POST" action="upload_prescription.php" enctype="multipart/form-data">
                    <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                    <input type="file" name="prescription" required>
                    <button type="submit" class="btn">📤 Upload</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<p style="text-align:center; margin-top: 30px;">
    <a href="logout.php" class="btn" style="background:#dc3545">🚪 Logout</a>
</p>

</body>
</html>
