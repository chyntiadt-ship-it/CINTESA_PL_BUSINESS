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

if (!isset($_GET['id'])) {
    header("Location: cari_produk.php");
    exit;
}

function waktu_lalu($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return "Baru saja";
    if ($diff < 3600) return floor($diff / 60) . " menit yang lalu";
    if ($diff < 86400) return floor($diff / 3600) . " jam yang lalu";
    if ($diff < 2592000) return floor($diff / 86400) . " hari yang lalu";
    if ($diff < 31536000) return floor($diff / 2592000) . " bulan yang lalu";

    return floor($diff / 31536000) . " tahun yang lalu";
}

function status_label($status) {
    if ($status == 'Tersedia') return 'Aktif';
    if ($status == 'Terjual') return 'Stok Habis';
    if ($status == 'Pre-Order') return 'Pre-Order';
    if ($status == 'Dihapus') return 'Dihapus';
    return $status;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori, user.username, user.nama_lengkap
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.id_produk = '$id_produk'
");

if (mysqli_num_rows($query) == 0) {
    echo "Produk tidak ditemukan.";
    exit;
}

$produk = mysqli_fetch_assoc($query);

$foto_produk = mysqli_query($koneksi, "SELECT * FROM produk_foto WHERE id_produk='$id_produk'");
$foto_utama = mysqli_fetch_assoc($foto_produk);
$status_tampil = status_label($produk['status_produk']);
$produk_nonaktif = (
    $produk['status_produk'] == 'Dihapus' ||
    $produk['status_produk'] == 'Terjual' ||
    $status_tampil == 'Stok Habis'
);

$nego_boleh = strtolower(trim($produk['keterangan_nego'])) == 'bisa nego';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/detail_produk.css?v=30">
</head>
<body>

<main class="product-page">

    <header class="product-topbar">
        <a href="cari_produk.php" class="top-icon">‹</a>

        <div class="top-actions">
            <a href="cari_produk.php" class="top-icon">⌕</a>
            <a href="keranjang.php" class="top-icon">🛒</a>
        </div>
    </header>

    <section class="layout-detail">

        <div class="left-panel">
    <section class="product-hero">
        <?php if (!empty($foto_utama['foto'])) { ?>
            <img src="../uploads/produk/<?php echo htmlspecialchars($foto_utama['foto']); ?>" class="hero-image" alt="Foto Produk">
        <?php } else { ?>
            <div class="hero-placeholder">
                <span>🛍️</span>
                <p>Foto produk belum tersedia</p>
            </div>
        <?php } ?>
    </section>

    <a href="chat.php?id_produk=<?php echo $produk['id_produk']; ?>" class="seller-chat-btn">
        <span>💬</span> Chat ke Penjual
    </a>
</div>

        <div class="right-panel">
            <section class="product-main-card">
                <h1><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>

                <h2>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></h2>

                <p class="product-short-desc">
                    <?php echo htmlspecialchars(substr($produk['deskripsi'], 0, 95)); ?>
                    <?php echo strlen($produk['deskripsi']) > 95 ? '...' : ''; ?>
                </p>

                <div class="rating-row">
                    <span>⭐ 4.9</span>
                    <small>64 rating • 22 ulasan</small>
                </div>

                <div class="meta-row">
                    <span><?php echo waktu_lalu($produk['tanggal_upload']); ?></span>
                    <span><?php echo htmlspecialchars($produk['nama_kategori']); ?></span>
                </div>
            </section>

            <section class="detail-card">
                <div class="info-row">
                    <span>Kondisi</span>
                    <strong><?php echo !empty($produk['kondisi_barang']) ? htmlspecialchars($produk['kondisi_barang']) : '-'; ?></strong>
                </div>

                <div class="info-row">
                    <span>Katalog</span>
                    <strong class="catalog-text"><?php echo htmlspecialchars($produk['nama_kategori']); ?></strong>
                </div>

                <div class="info-row">
                    <span>Status Ketersediaan Produk</span>
                    <strong>
                        <em class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $status_tampil)); ?>">
                            <?php echo htmlspecialchars($status_tampil); ?>
                        </em>
                    </strong>
                </div>
                <div class="info-row nego-row">
    <span>Status Nego</span>

    <div class="nego-wrapper">

        <?php if ($nego_boleh) { ?>

            <em class="nego-badge nego-green">
                Boleh nego
            </em>

            <p class="nego-desc">
                Harga bisa didiskusikan. Chat penjual untuk tawar harga terbaik.
            </p>

        <?php } else { ?>

            <em class="nego-badge nego-red">
                Tidak boleh nego
            </em>

            <p class="nego-desc">
                Harga sudah final. Penjual tidak menerima tawaran harga.
            </p>

        <?php } ?>

    </div>
</div>
            </section>

            <section class="accordion active">
                <button class="accordion-header" type="button">
                    <span>Deskripsi produk</span>
                    <span class="arrow">⌄</span>
                </button>

                <div class="accordion-content">
                    <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
                </div>
            </section>

            <section class="seller-card">
                <h3>Informasi penjual</h3>

                <div class="seller-profile">
                    <div class="seller-avatar">
                        <?php echo strtoupper(substr($produk['username'], 0, 1)); ?>
                    </div>

                    <div>
                        <strong><?php echo htmlspecialchars($produk['nama_lengkap']); ?></strong>
                        <p>@<?php echo htmlspecialchars($produk['username']); ?></p>
                    </div>
                </div>
            </section>
        </div>

    </section>

</main>

<nav class="bottom-action">
    <?php if ($produk_nonaktif) { ?>
        <button class="cart-action disabled-action" disabled>
            🛒 + Keranjang
        </button>

        <button class="buy-btn disabled-action" disabled>
            Beli Langsung
        </button>
    <?php } else { ?>
        <a href="keranjang.php?id_produk=<?php echo $produk['id_produk']; ?>" class="cart-action">
            🛒 + Keranjang
        </a>

        <a href="chat.php?id_produk=<?php echo $produk['id_produk']; ?>" class="buy-btn">
            Beli Langsung
        </a>
    <?php } ?>
</nav>

<script src="../assets/js/detail_produk.js?v=30"></script>
</body>
</html>