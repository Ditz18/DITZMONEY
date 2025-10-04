<?php
session_start();
include "includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Cek user berdasarkan email
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Simpan session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama']    = $row['nama'];
            $_SESSION['email']   = $row['email'];

            // Arahkan ke dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Password salah!";
        }
    } else {
        $message = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - DitzMoney</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if ($message != ""): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar</a></p>
        <p><a href="forgot_password.php">Lupa Password?</a></p>
    </div>
</body>
</html>
