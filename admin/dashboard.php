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
    <link rel="stylesheet" href="../assets/css/admin.css?v=13">
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-circle">P</div>
            <h2>CINTESA</h2>
        </div>

        <nav class="sidebar-menu">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="manajemen_user.php">Manajemen User</a>
            <a href="manajemen_postingan.php">Manajemen Postingan</a>
            <a href="customer_service.php">Customer Service</a>
            <a href="profile.php">Profile Admin</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h1>Dashboard Admin</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>. Kelola aktivitas website Pree Love dari halaman ini.</p>
            </div>

            <div class="admin-badge">
                Admin Panel
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <p><?php echo $data_produk['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Total User</h3>
                <p><?php echo $data_user['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Total Penjual</h3>
                <p><?php echo $data_penjual['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Total Pembeli</h3>
                <p><?php echo $data_pembeli['total']; ?></p>
            </div>
        </div>

        <div class="section-card">
            <h2>Menu Cepat</h2>

            <div class="quick-menu">
                <a href="manajemen_user.php" class="quick-card">
                    <h3>Manajemen User</h3>
                    <p>Lihat, aktifkan, atau nonaktifkan akun pembeli dan penjual.</p>
                </a>

                <a href="manajemen_postingan.php" class="quick-card">
                    <h3>Postingan</h3>
                    <p>Kelola postingan produk yang dibuat oleh penjual.</p>
                </a>

                <a href="customer_service.php" class="quick-card">
                    <h3>Customer Service</h3>
                    <p>Pantau pesan, keluhan, atau pertanyaan dari pengguna.</p>
                </a>

                <a href="profile.php" class="quick-card">
                    <h3>Profile Admin</h3>
                    <p>Perbarui username, nomor telepon, dan foto profil admin.</p>
                </a>
            </div>
        </div>

    </main>

</div>

</body>
</html>