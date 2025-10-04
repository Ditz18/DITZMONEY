<?php
session_start();
include "includes/db.php";

// pastikan user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ambil data transaksi berdasarkan ID
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id' AND user_id='$user_id'");
    $transaksi = mysqli_fetch_assoc($result);

    if(!$transaksi) {
        echo "Data tidak ditemukan!";
        exit();
    }
}

// update data jika form disubmit
if($_SERVER['REQUEST_METHOD'] == "POST") {
    $item       = mysqli_real_escape_string($conn, $_POST['item']);
    $jenis      = mysqli_real_escape_string($conn, $_POST['jenis']);
    $nominal    = floatval($_POST['nominal']);
    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $update = "UPDATE transaksi SET 
                item='$item',
                jenis='$jenis',
                nominal='$nominal',
                tanggal='$tanggal',
                keterangan='$keterangan'
               WHERE id='$id' AND user_id='$user_id'";

    if(mysqli_query($conn, $update)) {
        header("Location: dashboard.php?msg=updated");
        exit();
    } else {
        echo "Gagal update data!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
   <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark-theme">
    <div class="form-container">
        <h2>Edit Transaksi</h2>
        <form method="POST">
            <label>Item</label>
            <input type="text" name="item" value="<?= htmlspecialchars($transaksi['item']) ?>" required>

            <label>Jenis</label>
            <select name="jenis" required>
                <option value="pemasukan" <?= $transaksi['jenis']=="pemasukan"?"selected":"" ?>>Pemasukan</option>
                <option value="pengeluaran" <?= $transaksi['jenis']=="pengeluaran"?"selected":"" ?>>Pengeluaran</option>
            </select>

            <label>Nominal</label>
            <input type="number" name="nominal" value="<?= $transaksi['nominal'] ?>" required>

            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= $transaksi['tanggal'] ?>" required>

            <label>Keterangan</label>
            <textarea name="keterangan"><?= htmlspecialchars($transaksi['keterangan']) ?></textarea>

            <button type="submit" class="btn-edit">Update</button>
            <a href="dashboard.php" class="btn-hapus">Batal</a>
        </form>
    </div>
</body>
</html>
