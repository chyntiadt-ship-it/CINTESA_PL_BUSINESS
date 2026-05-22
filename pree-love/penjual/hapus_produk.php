<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

// Pastikan produk milik penjual yang login
$cek_produk = mysqli_query($koneksi, "SELECT * FROM produk 
    WHERE id_produk='$id_produk' 
    AND id_user='$id_user'
");

if (mysqli_num_rows($cek_produk) == 0) {
    echo "Produk tidak ditemukan atau bukan milik Anda.";
    exit;
}

// Produk tidak benar-benar dihapus,
// hanya diubah status menjadi Dihapus.
$hapus_produk = mysqli_query($koneksi, "UPDATE produk 
    SET status_produk='Dihapus' 
    WHERE id_produk='$id_produk' 
    AND id_user='$id_user'
");

if ($hapus_produk) {
    header("Location: produk.php?pesan=hapus_berhasil");
    exit;
} else {
    header("Location: produk.php?pesan=hapus_gagal");
    exit;
}
?>