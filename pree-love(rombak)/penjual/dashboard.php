<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_penjual = (int) $_SESSION['id_user'];
$username = $_SESSION['username'];

if (isset($_GET['hapus'])) {
    $id_produk = (int) $_GET['hapus'];

    $cek_produk = mysqli_query($koneksi, "
        SELECT * FROM produk 
        WHERE id_produk='$id_produk' 
        AND id_user='$id_penjual'
        LIMIT 1
    ");

    if ($cek_produk && mysqli_num_rows($cek_produk) > 0) {
        $foto_query = mysqli_query($koneksi, "
            SELECT * FROM produk_foto 
            WHERE id_produk='$id_produk'
        ");

        while ($foto = mysqli_fetch_assoc($foto_query)) {
            $path = "../uploads/produk/" . $foto['foto'];
            if (!empty($foto['foto']) && file_exists($path)) {
                unlink($path);
            }
        }

        mysqli_query($koneksi, "DELETE FROM produk_foto WHERE id_produk='$id_produk'");
        mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id_produk' AND id_user='$id_penjual'");
    }

    header("Location: dashboard.php?pesan=hapus_berhasil");
    exit;
}

$query_produk = mysqli_query($koneksi, "
    SELECT 
        produk.*,
        kategori.nama_kategori,
        (
            SELECT produk_foto.foto 
            FROM produk_foto 
            WHERE produk_foto.id_produk = produk.id_produk 
            ORDER BY produk_foto.id_foto ASC 
            LIMIT 1
        ) AS foto_utama
    FROM produk
    LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori
    WHERE produk.id_user='$id_penjual'
    ORDER BY produk.id_produk DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penjual - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/seller.css?v=1">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="seller-page">

<header class="role-navbar">
    <a href="dashboard.php" class="brand-logo">CINTESA</a>

    <div class="seller-nav-actions">
        <a href="tambah_produk.php" class="icon-button dark" title="Tambah Produk">＋</a>

        <a href="pesan.php" class="icon-button" title="Pesan">
            <svg class="svg-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 5.5C4 4.12 5.12 3 6.5 3h11C18.88 3 20 4.12 20 5.5v7C20 13.88 18.88 15 17.5 15H10l-4.2 3.15A1.1 1.1 0 0 1 4 17.27V5.5Zm2.5-.3a.3.3 0 0 0-.3.3v9.57l3.07-2.3c.19-.14.42-.22.66-.22h7.57a.3.3 0 0 0 .3-.3V5.5a.3.3 0 0 0-.3-.3h-11Z"/>
            </svg>
        </a>

        <a href="../auth/logout.php" class="icon-button" title="Logout">
            <img src="../assets/icons/logout.png" alt="Logout">
        </a>
    </div>
</header>

<main class="role-main">
    <section class="page-heading center">
        <h1>Halo, <?php echo htmlspecialchars($username); ?></h1>
        <p>Ayo unggah produkmu, dan berjualanlah</p>
    </section>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_berhasil') { ?>
        <div class="alert success">Produk berhasil dihapus.</div>
    <?php } ?>

    <section class="seller-product-grid">
        <?php if ($query_produk && mysqli_num_rows($query_produk) > 0) { ?>
            <?php while ($produk = mysqli_fetch_assoc($query_produk)) { ?>
                <article class="seller-product-card">
                    <div class="seller-product-image">
                        <?php
                        $foto_path = "../uploads/produk/" . $produk['foto_utama'];
                        if (!empty($produk['foto_utama']) && file_exists($foto_path)) {
                        ?>
                            <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Produk">
                        <?php } else { ?>
                            <div class="no-image">Tidak ada foto</div>
                        <?php } ?>
                    </div>

                    <div class="seller-product-body">
                        <div class="product-top">
                            <span class="category">
                                <?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Tanpa kategori'); ?>
                            </span>

                            <span class="status-pill">
                                <?php echo htmlspecialchars($produk['status_produk']); ?>
                            </span>
                        </div>

                        <h2><?php echo htmlspecialchars($produk['nama_produk']); ?></h2>

                        <p class="price">
                            Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                        </p>

                        <p class="product-desc">
                            <?php echo htmlspecialchars(substr($produk['deskripsi'], 0, 90)); ?>...
                        </p>

                        <div class="product-actions">
                            <a href="edit_produk.php?id=<?php echo $produk['id_produk']; ?>" class="main-button small">
                                Edit
                            </a>

                            <a 
                                href="dashboard.php?hapus=<?php echo $produk['id_produk']; ?>" 
                                class="delete-button"
                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
                            >
                                Hapus
                            </a>
                        </div>
                    </div>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-box">
                Belum ada produk. Klik tombol + di navbar untuk menambahkan produk pertama.
            </div>
        <?php } ?>
    </section>
</main>

</body>
</html>