<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, trim($_GET['keyword'])) : "";

$total_penjual = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM user WHERE role='penjual'
"))['total'];

$total_pembeli = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM user WHERE role='pembeli'
"))['total'];

$where_produk = "";
if ($keyword != "") {
    $where_produk = "WHERE produk.nama_produk LIKE '%$keyword%' OR user.username LIKE '%$keyword%'";
}

$produk_query = mysqli_query($koneksi, "
    SELECT 
        produk.*,
        user.username,
        kategori.nama_kategori
    FROM produk
    JOIN user ON produk.id_user = user.id_user
    LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori
    $where_produk
    ORDER BY produk.id_produk DESC
");

if (isset($_GET['hapus'])) {
    $id_produk = (int) $_GET['hapus'];

    $foto_query = mysqli_query($koneksi, "
        SELECT * FROM produk_foto WHERE id_produk='$id_produk'
    ");

    while ($foto = mysqli_fetch_assoc($foto_query)) {
        $path = "../uploads/produk/" . $foto['foto'];
        if (!empty($foto['foto']) && file_exists($path)) {
            unlink($path);
        }
    }

    mysqli_query($koneksi, "DELETE FROM produk_foto WHERE id_produk='$id_produk'");
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id_produk'");

    header("Location: dashboard.php?pesan=hapus_berhasil");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/admin.css?v=1">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="admin-page">

<header class="role-navbar">
    <a href="dashboard.php" class="brand-logo">CINTESA</a>

    <a href="../auth/logout.php" class="icon-button" title="Logout">
        <img src="../assets/icons/logout.png" alt="Logout">
    </a>
</header>

<main class="role-main">
    <section class="page-heading center">
        <h1>Dashboard CINTESA</h1>
        <p>Selamat datang, admin</p>
    </section>

    <section class="summary-grid two">
        <a href="manajemen_user.php?role=penjual" class="summary-card">
            <span>Total Penjual</span>
            <strong><?php echo $total_penjual; ?></strong>
        </a>

        <a href="manajemen_user.php?role=pembeli" class="summary-card">
            <span>Total Pembeli</span>
            <strong><?php echo $total_pembeli; ?></strong>
        </a>
    </section>

    <section class="table-card">
        <div class="table-header">
            <div>
                <h2>Data Postingan Produk</h2>
                <p>Admin dapat melihat dan menghapus produk yang tidak sesuai.</p>
            </div>

            <form method="GET" class="search-form">
                <input 
                    type="text" 
                    name="keyword" 
                    placeholder="Cari produk..." 
                    value="<?php echo htmlspecialchars($keyword); ?>"
                >
            </form>
        </div>

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_berhasil') { ?>
            <div class="alert success">Produk berhasil dihapus.</div>
        <?php } ?>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Penjual</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($produk_query && mysqli_num_rows($produk_query) > 0) { ?>
                        <?php $no = 1; while ($produk = mysqli_fetch_assoc($produk_query)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($produk['nama_produk']); ?></strong><br>
                                    <small><?php echo htmlspecialchars(substr($produk['deskripsi'], 0, 70)); ?>...</small>
                                </td>
                                <td><?php echo htmlspecialchars($produk['username']); ?></td>
                                <td><?php echo htmlspecialchars($produk['nama_kategori'] ?? '-'); ?></td>
                                <td>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($produk['status_produk']); ?></td>
                                <td>
                                    <a 
                                        href="dashboard.php?hapus=<?php echo $produk['id_produk']; ?>" 
                                        class="btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                    >
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="empty-row">Tidak ada data produk.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>