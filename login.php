<?php
session_start();
require 'db.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['patient_id'] = $user['id'];
        $_SESSION['patient_name'] = $user['name'];
        header("Location: dashboard_patient.php");
        exit;
    } else {
        $error = "❌ Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>🔐 Patient Login</title>
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
            max-width: 400px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s ease;
        }

        input:focus {
            border-color: #667eea;
            outline: none;
        }

        button {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(45deg, #0056b3, #007bff);
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 123, 255, 0.4);
        }

        p.error {
            color: red;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<form method="POST">
    <h2>🔐 Patient Login</h2>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">🚪 Login</button>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
</form>

</body>
</html>
