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

$total_penjual = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='penjual'");
$data_penjual = mysqli_fetch_assoc($total_penjual);

$total_pembeli = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pembeli'");
$data_pembeli = mysqli_fetch_assoc($total_pembeli);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - Pree Love</title>
</head>
<body>

    <h2>Manajemen User</h2>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <h3>Pilih Jenis User</h3>

    <div style="display:flex; gap:20px;">
        <div style="border:1px solid #000; padding:20px; width:200px;">
            <h3>Penjual</h3>
            <p>Total: <?php echo $data_penjual['total']; ?></p>
            <a href="list_penjual.php">Lihat Penjual</a>
        </div>

        <div style="border:1px solid #000; padding:20px; width:200px;">
            <h3>Pembeli</h3>
            <p>Total: <?php echo $data_pembeli['total']; ?></p>
            <a href="list_pembeli.php">Lihat Pembeli</a>
        </div>
    </div>

</body>
</html>