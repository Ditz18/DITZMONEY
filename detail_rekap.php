<?php
include "includes/db.php";
session_start();
$user_id = $_SESSION['user_id'];

$type = $_GET['type'];
$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];
$monthNumber = date('m', strtotime($bulan));

$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id='$user_id' AND jenis='$type' AND MONTH(tanggal)='$monthNumber' AND YEAR(tanggal)='$tahun'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Rekap <?= ucfirst($type) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <section class="rekap-detail">
        <h2>Detail Rekap <?= ucfirst($type) ?> Bulan <?= $bulan ?> <?= $tahun ?></h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $total = 0;
                while($row = mysqli_fetch_assoc($query)) {
                    $total += $row['nominal'];
                    echo "<tr>
                            <td>".$no++."</td>
                            <td>".$row['item']."</td>
                            <td>Rp. ".number_format($row['nominal'],0,',','.')."</td>
                            <td>".$row['tanggal']."</td>
                            <td>".$row['keterangan']."</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <h3>Total: Rp. <?= number_format($total,0,',','.'); ?></h3>
    </section>
</body>
</html>
