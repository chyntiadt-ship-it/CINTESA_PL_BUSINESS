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

$total_produk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$data_produk = mysqli_fetch_assoc($total_produk);

$total_user = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user");
$data_user = mysqli_fetch_assoc($total_user);

$total_penjual = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='penjual'");
$data_penjual = mysqli_fetch_assoc($total_penjual);

$total_pembeli = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pembeli'");
$data_pembeli = mysqli_fetch_assoc($total_pembeli);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Pree Love</title>
</head>
<body>

    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>

    <hr>

    <h3>Ringkasan Website</h3>
    <p>Total Produk: <?php echo $data_produk['total']; ?></p>
    <p>Total User: <?php echo $data_user['total']; ?></p>
    <p>Total Penjual: <?php echo $data_penjual['total']; ?></p>
    <p>Total Pembeli: <?php echo $data_pembeli['total']; ?></p>

    <hr>

    <h3>Menu Admin</h3>
    <ul>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="manajemen_postingan.php">Manajemen Postingan</a></li>
        <li><a href="customer_service.php">Customer Service</a></li>
        <li><a href="manajemen_user.php">Manajemen User</a></li>
    </ul>

    <a href="../auth/logout.php">Logout</a>

</body>
</html>