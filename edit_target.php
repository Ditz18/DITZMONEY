<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$bulan = date('m');
$tahun = date('Y');

// Deteksi struktur aktif
$use_settings = false;
$use_target = false;

$cekTbl = mysqli_query($conn, "SHOW TABLES LIKE 'settings'");
if (mysqli_num_rows($cekTbl) > 0) {
    $cekCol = mysqli_query($conn, "SHOW COLUMNS FROM settings LIKE 'total_pemasukan'");
    if (mysqli_num_rows($cekCol) > 0) $use_settings = true;
}

$cekTbl2 = mysqli_query($conn, "SHOW TABLES LIKE 'target'");
if (mysqli_num_rows($cekTbl2) > 0) {
    $cekCol2 = mysqli_query($conn, "SHOW COLUMNS FROM target LIKE 'jumlah_pemasukan'");
    if (mysqli_num_rows($cekCol2) > 0) $use_target = true;
}

// Ambil data saat ini
$current_target = 0;
$current_income = 0;

if ($use_settings) {
    $q = mysqli_query($conn, "SELECT target_pengeluaran, total_pemasukan FROM settings WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $current_target = $row['target_pengeluaran'];
        $current_income = $row['total_pemasukan'];
    }
} elseif ($use_target) {
    $q = mysqli_query($conn, "SELECT target_pengeluaran, jumlah_pemasukan FROM target WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $current_target = $row['target_pengeluaran'];
        $current_income = $row['jumlah_pemasukan'];
    }
}

// Proses submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_pengeluaran = floatval($_POST['target_pengeluaran']);
    $total_pemasukan = floatval($_POST['total_pemasukan']);

    if ($use_settings) {
        $cek = mysqli_query($conn, "SELECT id FROM settings WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE settings SET target_pengeluaran='$target_pengeluaran', total_pemasukan='$total_pemasukan' WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
        } else {
            mysqli_query($conn, "INSERT INTO settings (user_id, bulan, tahun, target_pengeluaran, total_pemasukan) VALUES ('$user_id','$bulan','$tahun','$target_pengeluaran','$total_pemasukan')");
        }
    } elseif ($use_target) {
        $cek = mysqli_query($conn, "SELECT id FROM target WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE target SET target_pengeluaran='$target_pengeluaran', jumlah_pemasukan='$total_pemasukan' WHERE user_id='$user_id' AND bulan='$bulan' AND tahun='$tahun'");
        } else {
            mysqli_query($conn, "INSERT INTO target (user_id, bulan, tahun, target_pengeluaran, jumlah_pemasukan) VALUES ('$user_id','$bulan','$tahun','$target_pengeluaran','$total_pemasukan')");
        }
    }

    // redirect otomatis
    header("Location: dashboard.php?update=success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Target</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/edit_target.css">

</head>
<body>
<div class="target-edit-wrapper">
  <h2>Edit Target & Pemasukan</h2>
  <form method="POST">
    <div class="form-group">
      <label>Target Pengeluaran (Rp)</label>
      <input type="number" name="target_pengeluaran" value="<?= htmlspecialchars($current_target); ?>" required>
    </div>
    <div class="form-group">
      <label>Jumlah Pemasukan (Rp)</label>
      <input type="number" name="total_pemasukan" value="<?= htmlspecialchars($current_income); ?>" required>
    </div>
    <p class="note">💡 Saran: Biarkan <b>Jumlah Pemasukan</b> sesuai sistem (jangan diubah).</p>
    <div class="form-actions">
      <button type="submit" class="btn-update">Simpan</button>
      <a href="dashboard.php" class="btn-cancel">Batal</a>
    </div>
  </form>
</div>
</body>
</html>
