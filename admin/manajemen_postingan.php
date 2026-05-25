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

$query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori, user.username, user.nama_lengkap
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.status_produk != 'Dihapus'
    ORDER BY produk.tanggal_upload DESC
");

$total_postingan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE status_produk != 'Dihapus'");
$data_postingan = mysqli_fetch_assoc($total_postingan);

$total_tersedia = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE status_produk='Tersedia'");
$data_tersedia = mysqli_fetch_assoc($total_tersedia);

$total_terjual = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE status_produk='Terjual'");
$data_terjual = mysqli_fetch_assoc($total_terjual);

$total_dihapus = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE status_produk='Dihapus'");
$data_dihapus = mysqli_fetch_assoc($total_dihapus);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Postingan - CINTESA</title>

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
            <a href="manajemen_user.php">Manajemen User</a>
            <a href="manajemen_postingan.php" class="active">Manajemen Postingan</a>
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
                <h1>Manajemen Postingan</h1>
                <p>
                    Kelola postingan produk yang diunggah oleh penjual.
                    Admin dapat melihat data produk dan menghapus postingan yang tidak sesuai.
                </p>
            </div>

            <div class="admin-badge">
                Posting Management
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <h3>Total Postingan</h3>
                <p><?php echo $data_postingan['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Produk Tersedia</h3>
                <p><?php echo $data_tersedia['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Produk Terjual</h3>
                <p><?php echo $data_terjual['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Postingan Dihapus</h3>
                <p><?php echo $data_dihapus['total']; ?></p>
            </div>
        </section>

        <section class="section-card">

            <div class="section-header">
                <div>
                    <h2>Daftar Postingan Produk</h2>
                    <p>Pantau seluruh produk yang masih tampil di website CINTESA.</p>
                </div>

                <a href="dashboard.php" class="small-btn">Kembali ke Dashboard</a>
            </div>

            <?php
            if (isset($_GET['pesan'])) {
                if ($_GET['pesan'] == "hapus_berhasil") {
                    echo "<div class='alert success'>Postingan berhasil dihapus.</div>";
                } elseif ($_GET['pesan'] == "hapus_gagal") {
                    echo "<div class='alert danger'>Postingan gagal dihapus.</div>";
                }
            }
            ?>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Penjual</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        if (mysqli_num_rows($query) > 0) {
                            while ($produk = mysqli_fetch_assoc($query)) {
                                $id_produk = $produk['id_produk'];

                                $foto_query = mysqli_query($koneksi, "SELECT * FROM produk_foto 
                                    WHERE id_produk='$id_produk' 
                                    LIMIT 1
                                ");

                                $foto = mysqli_fetch_assoc($foto_query);
                                $status_class = strtolower($produk['status_produk']);
                        ?>

                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td>
                                <?php if (!empty($foto['foto'])) { ?>
                                    <img 
                                        src="../uploads/produk/<?php echo htmlspecialchars($foto['foto']); ?>" 
                                        class="table-img"
                                        alt="Foto Produk"
                                    >
                                <?php } else { ?>
                                    <span class="empty-img">Tidak ada foto</span>
                                <?php } ?>
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($produk['nama_produk']); ?></strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($produk['nama_kategori']); ?>
                            </td>

                            <td>
                                Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                            </td>

                            <td>
                                <span class="status-badge status-<?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($produk['status_produk']); ?>
                                </span>
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($produk['username']); ?></strong>
                                <br>
                                <span class="table-muted">
                                    <?php echo htmlspecialchars($produk['nama_lengkap']); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($produk['tanggal_upload']); ?>
                            </td>

                            <td>
                                <a 
                                    href="hapus_postingan.php?id=<?php echo $produk['id_produk']; ?>" 
                                    class="btn-delete"
                                    onclick="return confirm('Yakin ingin menghapus postingan ini?')"
                                >
                                    Hapus
                                </a>
                            </td>
                        </tr>

                        <?php
                            }
                        } else {
                        ?>

                        <tr>
                            <td colspan="9" class="empty-cell">
                                <div class="empty-state">
                                    <div class="empty-icon">□</div>
                                    <h3>Belum ada postingan produk</h3>
                                    <p>Produk yang diunggah oleh penjual akan tampil di tabel ini.</p>
                                </div>
                            </td>
                        </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>

</div>

</body>
</html>