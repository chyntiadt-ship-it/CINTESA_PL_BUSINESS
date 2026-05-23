<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'pembeli') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pembeli - Pree Love</title>
</head>
<body>

    <h2>Dashboard Pembeli</h2>
    <p>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>

    <hr>

    <h3>Menu Pembeli</h3>
    <ul>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="cari_produk.php">Cari Produk</a></li>
        <li><a href="chat.php">Chat</a></li>
        <li><a href="customer_service.php">Customer Service</a></li>
    </ul>

    <a href="../auth/logout.php">Logout</a>

</body>
</html>