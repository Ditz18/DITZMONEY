<?php
session_start();
include "includes/db.php";

// Pastikan login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama'];
$email   = $_SESSION['email'] ?? 'Belum diatur';

// Ambil target bulan ini
$bulan  = date('m');
$tahun  = date('Y');

$target_query = mysqli_query($conn, "SELECT target_pengeluaran FROM settings WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
$target = mysqli_num_rows($target_query) > 0 ? mysqli_fetch_assoc($target_query)['target_pengeluaran'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/saya.css">
  <script src="https://kit.fontawesome.com/a2e0b5f6c1.js" crossorigin="anonymous"></script>
</head>
<body>

  <div class="container-saya">
    <div class="profile-card">
      <div class="profile-icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <h2><?php echo htmlspecialchars($nama); ?></h2>
      <p class="email"><?php echo htmlspecialchars($email); ?></p>
      <P>Developer By : <a href="Https://www.instagram.com/bgs_adiityaa" class="igfooter" target="blank">Adit</a> </p>
    </div>

    <div class="setting-card">
      <h3>Pengaturan Akun</h3>
      <ul>
        <li>
          <span>🎯 Target Pengeluaran Bulan Ini</span>
          <strong>Rp <?php echo number_format($target, 0, ',', '.'); ?></strong>
          <a href="edit_target.php" class="btn-edit">Ubah</a>
        </li>
        <li>
          <span>📊 Lihat Rekap Bulanan</span>
          <a href="rekap.php" class="btn-link">Buka</a>
        </li>
      </ul>
    </div>

    <div class="logout-card">
      <a href="logout.php" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> Keluar
      </a>
    </div>
  </div>

</body>
</html>
