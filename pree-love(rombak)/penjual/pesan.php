<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_penjual = (int) $_SESSION['id_user'];

$query_pesan = mysqli_query($koneksi, "
    SELECT 
        chat.*,
        produk.nama_produk,
        produk.harga,
        user.username AS username_pembeli,
        user.nama_lengkap AS nama_pembeli
    FROM chat
    JOIN produk ON chat.id_produk = produk.id_produk
    JOIN user ON chat.id_pembeli = user.id_user
    WHERE chat.id_penjual='$id_penjual'
    ORDER BY chat.tanggal_chat DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Penjual - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/seller.css?v=2">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="seller-page">

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
    <section class="page-heading center">
        <h1>Pesan Masuk</h1>
        <p>Daftar pesan dari pembeli yang tertarik dengan produkmu.</p>
    </section>

    <section class="seller-message-list">
        <?php if ($query_pesan && mysqli_num_rows($query_pesan) > 0) { ?>
            <?php while ($pesan = mysqli_fetch_assoc($query_pesan)) { ?>
                <article class="seller-message-item">
                    <div class="seller-avatar">
                        <?php echo strtoupper(substr($pesan['username_pembeli'], 0, 1)); ?>
                    </div>

                    <div class="seller-message-content">
                        <h2><?php echo htmlspecialchars($pesan['nama_pembeli'] ?: $pesan['username_pembeli']); ?></h2>
                        <p>@<?php echo htmlspecialchars($pesan['username_pembeli']); ?></p>
                        <span>
                            Produk: <?php echo htmlspecialchars($pesan['nama_produk']); ?> - 
                            Rp<?php echo number_format($pesan['harga'], 0, ',', '.'); ?>
                        </span>
                    </div>

                    <a href="detail_pesan.php?id=<?php echo $pesan['id_chat']; ?>" class="main-button small">
                        Pesan
                    </a>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-box">
                Belum ada pesan masuk dari pembeli.
            </div>
        <?php } ?>
    </section>
</main>

</body>
</html>