<?php
include "includes/db.php";
session_start();

$user_id   = $_SESSION['user_id'];
$target    = $_POST['target'];
$pemasukan = $_POST['pemasukan'];

$query = mysqli_query($conn, "UPDATE target 
    SET target_pengeluaran='$target', jumlah_pemasukan='$pemasukan' 
    WHERE user_id='$user_id'");

if ($query) {
    echo "<script>alert('Update sukses!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal update!'); window.location='edit_target.php';</script>";
}
?>
