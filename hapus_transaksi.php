<?php
session_start();
include "includes/db.php";

// pastikan user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // pastikan data milik user yg sedang login
    $query = "DELETE FROM transaksi WHERE id='$id' AND user_id='$user_id'";
    if(mysqli_query($conn, $query)) {
        header("Location: dashboard.php?msg=deleted");
    } else {
        echo "Gagal menghapus data!";
    }
} else {
    header("Location: dashboard.php");
}
?>
