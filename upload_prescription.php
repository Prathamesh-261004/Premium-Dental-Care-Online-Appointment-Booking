<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die("Unauthorized.");
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['prescription']) && isset($_POST['appointment_id'])) {
    $id = $_POST['appointment_id'];
    $file = $_FILES['prescription'];

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = "presc_" . time() . "_" . rand(1000,9999) . "." . $ext;
    $target = __DIR__ . "/uploads/" . $newName;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $pdo->prepare("UPDATE appointments SET prescription_file = ? WHERE id = ?");
        $stmt->execute([$newName, $id]);
        header("Location: dashboard_admin.php");
        exit;
    } else {
        echo "Upload failed.";
    }
}
?>
