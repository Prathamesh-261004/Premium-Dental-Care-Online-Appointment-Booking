<?php
session_start();
$admin_user = "admin";
$admin_pass = "12345";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;
        header("Location: dashboard_admin.php");
        exit;
    } else {
        $error = "❌ Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>🔐 Admin Login</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: bgShift 8s ease infinite;
        }

        @keyframes bgShift {
            0%, 100% { background: linear-gradient(135deg, #667eea, #764ba2); }
            50% { background: linear-gradient(135deg, #764ba2, #667eea); }
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 350px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideFade 0.6s ease-out;
        }

        @keyframes slideFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            font-weight: bold;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(45deg, #0056b3, #007bff);
            box-shadow: 0 8px 20px rgba(0,123,255,0.3);
            transform: translateY(-2px);
        }

        .error {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
            margin-top: 10px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div>
        <h2>🔐 Admin Login</h2>
        <form method="POST">
            <input name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
