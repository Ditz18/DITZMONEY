<?php
session_start();
include "includes/db.php";

// Hindari cache agar data selalu terbaru
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID user dari session
$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama'];

// Tanggal sekarang
$currentMonth = date('m');
$currentYear  = date('Y');
$lastMonth    = date('m', strtotime('-1 month'));
$lastMonthName = date('F', strtotime('-1 month'));

// 🔹 Cek apakah sudah pernah direkap
$cek_rekap = mysqli_query($conn, "SELECT * FROM rekap_pengeluaran WHERE user_id='$user_id' AND bulan='$lastMonthName' AND tahun='$currentYear'");
if (mysqli_num_rows($cek_rekap) == 0) {

    // Hitung total pengeluaran & pemasukan bulan lalu
    $pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM transaksi WHERE user_id='$user_id' AND jenis='pengeluaran' AND MONTH(tanggal)='$lastMonth'"));
    $pemasukan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM transaksi WHERE user_id='$user_id' AND jenis='pemasukan' AND MONTH(tanggal)='$lastMonth'"));

    $total_pengeluaran = $pengeluaran['total'] ?? 0;
    $total_pemasukan   = $pemasukan['total'] ?? 0;

    mysqli_query($conn, "INSERT INTO rekap_pengeluaran (user_id, bulan, tahun, total_pengeluaran)
                         VALUES ('$user_id', '$lastMonthName', '$currentYear', '$total_pengeluaran')");
    mysqli_query($conn, "INSERT INTO rekap_pemasukan (user_id, bulan, tahun, total_pemasukan)
                         VALUES ('$user_id', '$lastMonthName', '$currentYear', '$total_pemasukan')");
}

// 🔹 Ambil ringkasan keuangan bulan ini
$bulan = date('m');
$tahun = date('Y');

// Ambil Target Pengeluaran & Total Pemasukan dari tabel settings
$target_query = mysqli_query($conn, "
    SELECT target_pengeluaran, total_pemasukan 
    FROM settings 
    WHERE user_id='$user_id' 
    AND bulan='$bulan' 
    AND tahun='$tahun'
");

if (mysqli_num_rows($target_query) > 0) {
    $row_target = mysqli_fetch_assoc($target_query);
    $target = (float)$row_target['target_pengeluaran'];
    $total_pemasukan = (float)$row_target['total_pemasukan'];
} else {
    $target = 0;
    $total_pemasukan = 0;
}

// Hitung total pemasukan dari transaksi
$q_income = mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi 
    WHERE user_id='$user_id' AND jenis='pemasukan' 
    AND MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())");
$total_income = mysqli_fetch_assoc($q_income)['total'] ?? 0;

// Hitung total pengeluaran
$q_expense = mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi 
    WHERE user_id='$user_id' AND jenis='pengeluaran' 
    AND MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())");
$total_expense = mysqli_fetch_assoc($q_expense)['total'] ?? 0;

// Hitung sisa pengeluaran dari target
$sisa_pengeluaran = $target - $total_expense;

// Ambil semua transaksi bulan ini
$transaksi_query = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id='$user_id' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun' ORDER BY tanggal DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DitzMoney</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <h1>DitzMoney</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?php echo $nama; ?></span>
            <a href="logout.php" style="color:#ff00ff; margin-left:10px;"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <!-- Ringkasan Cards -->
<section class="summary">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <p>Target Pengeluaran Bulan Ini</p>
            <h2>Rp. <?php echo number_format($target,0,",","."); ?></h2>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-coins"></i>
        </div>
        <div>
            <p>Jumlah Pemasukan Bulan Ini</p>
            <h2>Rp. <?php echo number_format($total_pemasukan ?: $total_income,0,",","."); ?></h2>
        </div>
    </div>

    <div class="card">
        <i class="fas fa-receipt"></i>
        <div>
            <p>Jumlah Pengeluaran Bulan Ini</p>
            <h2>Rp. <?php echo number_format($total_expense,0,",","."); ?></h2>
            <small>
                Jumlah sisa pengeluaran dari Target Pengeluaran : <br>
                Rp. <?php echo number_format($sisa_pengeluaran,0,",","."); ?>
            </small>
        </div>
    </div>
</section>

    <!-- Form Input Transaksi -->
    <section class="form-input">
        <h3>Form Input Transaksi</h3>
        <form action="proses_input.php" method="POST">
            <input type="text" name="item" placeholder="Item" required>
            <select name="jenis" required>
                <option value="pemasukan">Pemasukan</option>
                <option value="pengeluaran">Pengeluaran</option>
                <option value="tabungan">Tabungan</option>
            </select>
            <input type="number" name="nominal" placeholder="Nominal Rp." required>
            <input type="date" name="tanggal" required>
            <input type="text" name="keterangan" placeholder="Keterangan">
            <button type="submit">Simpan</button>
        </form>
    </section>

    <!-- Tabel Transaksi -->
<section class="tabel-transaksi">
    <h3>Tabel Transaksi Bulanan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($transaksi_query)) {
                echo "<tr>
                    <td>".$no++."</td>
                    <td>".htmlspecialchars($row['item'])."</td>
                    <td>".ucfirst($row['jenis'])."</td>
                    <td>Rp. ".number_format($row['nominal'],0,",",".")."</td>
                    <td>".date('d-m-Y', strtotime($row['tanggal']))."</td>
                    <td>".htmlspecialchars($row['keterangan'])."</td>
                    <td>
                        <a href='edit_transaksi.php?id=".$row['id']."' class='btn-edit'>Edit</a>
                        <a href='hapus_transaksi.php?id=".$row['id']."' class='btn-hapus' onclick=\"return confirm('Yakin mau hapus data ini?');\">Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<!-- Notifikasi -->
<?php if(isset($_GET['msg']) && $_GET['msg'] == "updated"): ?>
    <p style="color:#2196f3; margin:10px 20px;">Transaksi berhasil diupdate ✨</p>
<?php endif; ?>

<?php if(isset($_GET['msg']) && $_GET['msg'] == "deleted"): ?>
    <p style="color:#4caf50; margin:10px 20px;">Transaksi berhasil dihapus ✅</p>
<?php endif; ?>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
  <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
    <i class="fas fa-home"></i><span>Beranda</span>
  </a>
  <a href="rekap_pengeluaran.php" class="nav-link">
    <i class="fas fa-arrow-down"></i><span>Pengeluaran</span>
  </a>
  <a href="rekap_pemasukan.php" class="nav-link">
    <i class="fas fa-arrow-up"></i><span>Pemasukan</span>
  </a>
  <a href="tabungan.php" class="nav-link">
    <i class="fas fa-piggy-bank"></i><span>Tabungan</span>
  </a>
  <a href="saya.php" class="nav-link">
    <i class="fas fa-user"></i><span>Saya</span>
  </a>
</nav>

</body>
</html>
