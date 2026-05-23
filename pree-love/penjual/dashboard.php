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

$total_produk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE id_user='$id_user'");
$data_produk = mysqli_fetch_assoc($total_produk);

$total_tersedia = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE id_user='$id_user' AND status_produk='Tersedia'");
$data_tersedia = mysqli_fetch_assoc($total_tersedia);

$total_terjual = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE id_user='$id_user' AND status_produk='Terjual'");
$data_terjual = mysqli_fetch_assoc($total_terjual);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penjual - Pree Love</title>
</head>
<body>

    <h2>Dashboard Penjual</h2>
    <p>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>

    <hr>

    <h3>Ringkasan Produk</h3>
    <p>Total Produk Saya: <?php echo $data_produk['total']; ?></p>
    <p>Produk Tersedia: <?php echo $data_tersedia['total']; ?></p>
    <p>Produk Terjual: <?php echo $data_terjual['total']; ?></p>

    <hr>

    <h3>Menu Penjual</h3>
    <ul>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="produk.php">Manajemen Produk</a></li>
        <li><a href="pesan.php">Pesan</a></li>
        <li><a href="customer_service.php">Customer Service</a></li>
    </ul>

    <a href="../auth/logout.php">Logout</a>

</body>
</html>