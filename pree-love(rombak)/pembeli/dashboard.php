<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pembeli') {
    header("Location: ../auth/login.php");
    exit;
}

$id_pembeli = (int) $_SESSION['id_user'];
$username = $_SESSION['username'];

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, trim($_GET['keyword'])) : "";

$where = "";
if ($keyword != "") {
    $where = "WHERE produk.nama_produk LIKE '%$keyword%' 
              OR produk.deskripsi LIKE '%$keyword%'
              OR kategori.nama_kategori LIKE '%$keyword%'
              OR user.username LIKE '%$keyword%'";
}

$query_produk = mysqli_query($koneksi, "
    SELECT 
        produk.*,
        kategori.nama_kategori,
        user.username AS nama_penjual,
        (
            SELECT produk_foto.foto 
            FROM produk_foto 
            WHERE produk_foto.id_produk = produk.id_produk 
            ORDER BY produk_foto.id_foto ASC 
            LIMIT 1
        ) AS foto_utama
    FROM produk
    JOIN user ON produk.id_user = user.id_user
    LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori
    $where
    ORDER BY produk.id_produk DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pembeli - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/buyer.css?v=1">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="buyer-page">

<header class="role-navbar">
    <a href="dashboard.php" class="brand-logo">CINTESA</a>

    <form method="GET" class="buyer-search">
        <input 
            type="text" 
            name="keyword" 
            placeholder="Cari produk..." 
            value="<?php echo htmlspecialchars($keyword); ?>"
        >
    </form>

    <a href="../auth/logout.php" class="icon-button" title="Logout">
        <img src="../assets/icons/logout.png" alt="Logout">
    </a>
</header>

<main class="role-main">
    <section class="page-heading center">
        <h1>Halo, <?php echo htmlspecialchars($username); ?></h1>
        <p>Temukan produk terbaik dan kelola aktivitas belanjamu di CINTESA</p>
    </section>

    <?php if ($keyword != "") { ?>
        <div class="search-info">
            Hasil pencarian untuk: <strong><?php echo htmlspecialchars($keyword); ?></strong>
            <a href="dashboard.php">Tampilkan semua</a>
        </div>
    <?php } ?>

    <section class="product-grid">
        <?php if ($query_produk && mysqli_num_rows($query_produk) > 0) { ?>
            <?php while ($produk = mysqli_fetch_assoc($query_produk)) { ?>
                <article class="product-card">
                    <div class="product-image">
                        <?php 
                        $foto_path = "../uploads/produk/" . $produk['foto_utama'];
                        if (!empty($produk['foto_utama']) && file_exists($foto_path)) { 
                        ?>
                            <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Produk">
                        <?php } else { ?>
                            <div class="no-image">Tidak ada foto</div>
                        <?php } ?>
                    </div>

                    <div class="product-body">
                        <div class="product-top">
                            <span class="category">
                                <?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Tanpa kategori'); ?>
                            </span>

                            <span class="status-pill">
                                <?php echo htmlspecialchars($produk['status_produk']); ?>
                            </span>
                        </div>

                        <h2><?php echo htmlspecialchars($produk['nama_produk']); ?></h2>

                        <p class="seller-name">
                            Penjual: <?php echo htmlspecialchars($produk['nama_penjual']); ?>
                        </p>

                        <p class="price">
                            Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                        </p>

                        <p class="product-desc">
                            <?php echo htmlspecialchars(substr($produk['deskripsi'], 0, 90)); ?>...
                        </p>

                        <a 
                            href="detail_produk.php?id=<?php echo $produk['id_produk']; ?>" 
                            class="main-button small full"
                        >
                            Lihat Detail
                        </a>
                    </div>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-box">
                Belum ada produk yang tersedia.
            </div>
        <?php } ?>
    </section>
</main>

</body>
</html>