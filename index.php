<?php
session_start();
// Kalau user sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DitzMoney - Kelola Keuanganmu</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="landing-container">
        <h1>DitzMoney</h1>
        <p>Kelola keuangan harianmu dengan mudah, cepat, dan stylish!</p>
        <div class="landing-buttons">
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        </div>
    </div>
</body>
</html>
