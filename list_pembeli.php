<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manajemen_postingan.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

// Cek produk
$cek_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_produk'");

if (mysqli_num_rows($cek_produk) == 0) {
    echo "Postingan tidak ditemukan.";
    exit;
}

// Admin tidak benar-benar menghapus produk,
// tetapi mengubah status produk menjadi Dihapus.
$hapus_postingan = mysqli_query($koneksi, "UPDATE produk 
    SET status_produk='Dihapus' 
    WHERE id_produk='$id_produk'
");

if ($hapus_postingan) {
    header("Location: manajemen_postingan.php?pesan=hapus_berhasil");
    exit;
} else {
    header("Location: manajemen_postingan.php?pesan=hapus_gagal");
    exit;
}
?>