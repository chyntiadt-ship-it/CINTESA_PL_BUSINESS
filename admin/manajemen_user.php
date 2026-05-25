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

$total_user = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role!='admin'");
$data_user = mysqli_fetch_assoc($total_user);

$total_aktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role!='admin' AND status='aktif'");
$data_aktif = mysqli_fetch_assoc($total_aktif);

$total_nonaktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role!='admin' AND status='nonaktif'");
$data_nonaktif = mysqli_fetch_assoc($total_nonaktif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/admin.css?v=13">
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-circle"></div>
            <h2>CINTESA</h2>
        </div>

        <nav class="sidebar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="manajemen_user.php" class="active">Manajemen User</a>
            <a href="manajemen_postingan.php">Manajemen Postingan</a>
            <a href="customer_service.php">Customer Service</a>
            <a href="profile.php">Profile Admin</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <main class="main-content">

        <section class="topbar">
            <div>
                <h1>Manajemen User</h1>
                <p>
                    Kelola data pengguna CINTESA, lihat daftar penjual dan pembeli, 
                    serta aktifkan atau nonaktifkan akun user.
                </p>
            </div>

            <div class="admin-badge">
                User Management
            </div>
        </section>

        <section class="stats-grid">
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

            <div class="stat-card">
                <h3>User Aktif</h3>
                <p><?php echo $data_aktif['total']; ?></p>
            </div>
        </section>

        <section class="section-card">
            <h2>Pilih Jenis User</h2>

            <div class="quick-menu user-menu">
                <a href="list_penjual.php" class="quick-card user-card">
                    <div class="user-icon">◎</div>
                    <h3>Penjual</h3>
                    <p>Total penjual terdaftar: <strong><?php echo $data_penjual['total']; ?></strong></p>
                    <span class="card-link">Lihat Data Penjual</span>
                </a>

                <a href="list_pembeli.php" class="quick-card user-card">
                    <div class="user-icon">□</div>
                    <h3>Pembeli</h3>
                    <p>Total pembeli terdaftar: <strong><?php echo $data_pembeli['total']; ?></strong></p>
                    <span class="card-link">Lihat Data Pembeli</span>
                </a>

                <div class="quick-card user-card">
                    <div class="user-icon">✓</div>
                    <h3>Akun Aktif</h3>
                    <p>Jumlah akun aktif: <strong><?php echo $data_aktif['total']; ?></strong></p>
                    <span class="card-link muted-link">Status pengguna</span>
                </div>

                <div class="quick-card user-card">
                    <div class="user-icon">!</div>
                    <h3>Akun Nonaktif</h3>
                    <p>Jumlah akun nonaktif: <strong><?php echo $data_nonaktif['total']; ?></strong></p>
                    <span class="card-link muted-link">Perlu pengecekan</span>
                </div>
            </div>
        </section>

    </main>

</div>

</body>
</html>