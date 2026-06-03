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

if (!isset($_GET['id'])) {
    header("Location: manajemen_postingan.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

$cek_produk = mysqli_query($koneksi, "SELECT produk.*, user.username, user.nama_lengkap 
    FROM produk
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.id_produk='$id_produk'
");

if (mysqli_num_rows($cek_produk) == 0) {
    echo "Postingan tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($cek_produk);

if (isset($_POST['hapus'])) {
    $hapus_postingan = mysqli_query($koneksi, "UPDATE produk 
        SET status_produk='Dihapus' 
        WHERE id_produk='$id_produk'
    ");

    if ($hapus_postingan) {
        header("Location: manajemen_postingan.php?pesan=hapus_berhasil");
        exit;
    } else {
        header("Location: manajemen_postingan.php?pesan=hapus_gagal");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Postingan - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/hapus_postingan.css">
</head>
<body>

<div class="confirm-wrapper">
    <div class="confirm-modal">

        <div class="confirm-icon">
            !
        </div>

        <h2>Hapus Postingan</h2>

        <p>
            Apakah kamu yakin ingin menghapus postingan
            <span><?php echo htmlspecialchars($data['nama_produk']); ?></span>
            milik
            <span><?php echo htmlspecialchars($data['username']); ?></span>?
        </p>

        <form method="POST">
            <div class="confirm-actions">
                <a href="manajemen_postingan.php" class="cancel-btn">
                    Batal
                </a>

                <button type="submit" name="hapus" class="danger-btn">
                    Ya, Hapus
                </button>
            </div>
        </form>

    </div>
</div>

</body>
</html>