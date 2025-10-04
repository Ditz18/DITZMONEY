<?php
include "includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Cek password sama
    if ($password !== $confirm) {
        $message = "Password dan Konfirmasi Password tidak sama!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email sudah terdaftar
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Email sudah terdaftar, gunakan email lain!";
        } else {
            // Simpan ke database
            $sql = "INSERT INTO users (nama, email, password) 
                    VALUES ('$nama', '$email', '$hashedPassword')";
            if (mysqli_query($conn, $sql)) {
                $message = "Registrasi berhasil! Silakan login.";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - DitzMoney</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Daftar Akun</h2>
        <?php if ($message != ""): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="nama" placeholder="Nama Lengkap" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm" placeholder="Ketik Ulang Password" required><br>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
