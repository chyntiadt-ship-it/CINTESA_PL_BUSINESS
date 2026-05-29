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

$keyword = "";
$id_kategori = "";

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
}

if (isset($_GET['id_kategori'])) {
    $id_kategori = mysqli_real_escape_string($koneksi, $_GET['id_kategori']);
}

$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$query_produk = "SELECT produk.*, kategori.nama_kategori, user.username 
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.status_produk = 'Tersedia'
";

if (!empty($keyword)) {
    $query_produk .= " AND produk.nama_produk LIKE '%$keyword%'";
}

if (!empty($id_kategori)) {
    $query_produk .= " AND produk.id_kategori = '$id_kategori'";
}

$query_produk .= " ORDER BY produk.tanggal_upload DESC";

$produk = mysqli_query($koneksi, $query_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Produk - CINTESA</title>
    <link rel="stylesheet" href="../assets/css/cari_produk.css?v=5">
</head>
<body>

<main class="search-page">

    <header class="search-header">
        <a href="dashboard.php" class="back-btn">‹</a>

        <form action="" method="GET" class="search-form" autocomplete="off">
            <span class="search-icon">⌕</span>

            <input
                type="text"
                name="keyword"
                id="searchInput"
                placeholder="Cari produk di CINTESA"
                value="<?php echo htmlspecialchars($keyword); ?>"
            >

            <select name="id_kategori">
                <option value="">Semua Kategori</option>

                <?php while ($row = mysqli_fetch_assoc($kategori)) { ?>
                    <option value="<?php echo $row['id_kategori']; ?>"
                        <?php if ($id_kategori == $row['id_kategori']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($row['nama_kategori']); ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Cari</button>
        </form>
    </header>

    <section class="result-section">
        <h2>Hasil Produk</h2>

        <div class="product-grid">

            <?php if (mysqli_num_rows($produk) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($produk)) { 
                    $id_produk = $row['id_produk'];

                    $foto_query = mysqli_query($koneksi, "
                        SELECT * FROM produk_foto
                        WHERE id_produk='$id_produk'
                        LIMIT 1
                    ");

                    $foto = mysqli_fetch_assoc($foto_query);
                ?>

                    <div class="product-card">
                        <?php if (!empty($foto['foto'])) { ?>
                            <img
                                src="../uploads/produk/<?php echo htmlspecialchars($foto['foto']); ?>"
                                class="product-image"
                                alt="<?php echo htmlspecialchars($row['nama_produk']); ?>"
                            >
                        <?php } else { ?>
                            <div class="no-image">Tidak ada foto</div>
                        <?php } ?>

                        <div class="product-content">
                            <span class="product-category">
                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                            </span>

                            <h3><?php echo htmlspecialchars($row['nama_produk']); ?></h3>

                            <p class="product-price">
                                Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </p>

                            <p class="product-location">
                                📍 <?php echo htmlspecialchars($row['alamat_produk']); ?>
                            </p>

                            <p class="product-seller">
                                👤 <?php echo htmlspecialchars($row['username']); ?>
                            </p>

                            <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" class="detail-btn">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="empty-box">
                    Produk tidak ditemukan.
                </div>

            <?php } ?>

        </div>
    </section>

</main>

<script src="../assets/js/cari_produk.js?v=5"></script>
</body>
</html>
