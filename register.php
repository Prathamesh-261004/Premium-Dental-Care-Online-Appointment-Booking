<?php
require 'db.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = strtolower(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];

    $photo = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];
    $target = "uploads/" . basename($photo);
    move_uploaded_file($tmp, $target);

    try {
        $stmt = $pdo->prepare("INSERT INTO patients (name, email, password, gender, dob, phone, address, city, state, photo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $gender, $dob, $phone, $address, $city, $state, $photo]);
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $error = "❌ Registration failed: Email already in use.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📝 Patient Register</title>
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
            max-width: 500px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #667eea;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: linear-gradient(45deg, #20c997, #28a745);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(32, 201, 151, 0.3);
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

<form method="POST" enctype="multipart/form-data">
    <h2>📝 Patient Registration</h2>

    <input name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="gender" required>
        <option value="">Select Gender</option>
        <option>Male</option>
        <option>Female</option>
        <option>Other</option>
    </select>
    <input type="date" name="dob" required>
    <input name="phone" placeholder="Phone Number" required>
    <textarea name="address" placeholder="Address" required></textarea>
    <input name="city" placeholder="City" required>
    <input name="state" placeholder="State" required>
    <input type="file" name="photo" accept="image/*" required>

    <button type="submit">📥 Register</button>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
</form>

</body>
</html>
