<?php
session_start();
include "includes/db.php"; // koneksi ke database

// pastikan user login
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_id    = $_SESSION['user_id'];
    $item       = mysqli_real_escape_string($conn, $_POST['item']);
    $jenis      = mysqli_real_escape_string($conn, $_POST['jenis']);
    $nominal    = mysqli_real_escape_string($conn, $_POST['nominal']);
    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $sql = "INSERT INTO transaksi (user_id, item, jenis, nominal, tanggal, keterangan) 
            VALUES ('$user_id', '$item', '$jenis', '$nominal', '$tanggal', '$keterangan')";

    if(mysqli_query($conn, $sql)){
        header("Location: dashboard.php?status=sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
