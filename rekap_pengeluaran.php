<?php
include "includes/db.php";
session_start();
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM rekap_pengeluaran WHERE user_id='$user_id' ORDER BY tahun DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pengeluaran</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <section class="rekap">
        <h2>Rekap Pengeluaran Bulanan</h2>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Total Pengeluaran</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['bulan']; ?></td>
                        <td><?= $row['tahun']; ?></td>
                        <td>Rp. <?= number_format($row['total_pengeluaran'],0,',','.'); ?></td>
                        <td><a href="detail_rekap.php?type=pengeluaran&bulan=<?= $row['bulan']; ?>&tahun=<?= $row['tahun']; ?>">Lihat</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
</body>
</html>
