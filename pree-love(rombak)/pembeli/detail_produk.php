<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pembeli') {
    header("Location: ../auth/login.php");
    exit;
}

$id_produk = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_produk <= 0) {
    header("Location: dashboard.php");
    exit;
}

$query_produk = mysqli_query($koneksi, "
    SELECT 
        produk.*,
        kategori.nama_kategori,
        user.username AS nama_penjual,
        user.nama_lengkap AS nama_lengkap_penjual
    FROM produk
    JOIN user ON produk.id_user = user.id_user
    LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori
    WHERE produk.id_produk = '$id_produk'
    LIMIT 1
");

if (!$query_produk || mysqli_num_rows($query_produk) == 0) {
    echo "Produk tidak ditemukan.";
    exit;
}

$produk = mysqli_fetch_assoc($query_produk);

$query_foto = mysqli_query($koneksi, "
    SELECT * FROM produk_foto
    WHERE id_produk='$id_produk'
    ORDER BY id_foto ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/buyer.css?v=2">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="buyer-page">

<header class="role-navbar">
    <a href="dashboard.php" class="icon-button back-button" title="Kembali">
        <img 
            src="../assets/icons/penjual/back-arrow.png" 
            class="back-icon" 
            alt="Kembali"
            >
    </a>

    <a href="dashboard.php" class="brand-logo">CINTESA</a>

    <a href="../auth/logout.php" class="icon-button" title="Logout">
        <img src="../assets/icons/logout.png" alt="Logout">
    </a>
</header>

<main class="role-main">
    <section class="detail-card">
        <div class="detail-gallery">
            <?php if ($query_foto && mysqli_num_rows($query_foto) > 0) { ?>
                <?php while ($foto = mysqli_fetch_assoc($query_foto)) { 
                    $foto_path = "../uploads/produk/" . $foto['foto'];
                ?>
                    <?php if (!empty($foto['foto']) && file_exists($foto_path)) { ?>
                        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Produk">
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <div class="no-image detail-no-image">Tidak ada foto produk</div>
            <?php } ?>
        </div>

        <div class="detail-info">
            <span class="category">
                <?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Tanpa kategori'); ?>
            </span>

            <h1><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>

            <p class="price large">
                Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?>
            </p>

            <div class="info-list">
                <div>
                    <strong>Status Produk</strong>
                    <p><?php echo htmlspecialchars($produk['status_produk']); ?></p>
                </div>

                <div>
                    <strong>Penjual</strong>
                    <p><?php echo htmlspecialchars($produk['nama_lengkap_penjual'] ?: $produk['nama_penjual']); ?></p>
                </div>

                <div>
                    <strong>Username Penjual</strong>
                    <p>@<?php echo htmlspecialchars($produk['nama_penjual']); ?></p>
                </div>
            </div>

            <div class="description-box">
                <strong>Deskripsi Produk</strong>
                <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
            </div>

            <a 
                href="pesan.php?id_produk=<?php echo $produk['id_produk']; ?>" 
                class="main-button full"
            >
                Tanya Penjual
            </a>
        </div>
    </section>
</main>

</body>
</html>