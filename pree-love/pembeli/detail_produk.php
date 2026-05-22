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

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori, user.username, user.nama_lengkap, user.nomor_telepon
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk - Pree Love</title>
</head>
<body>

    <h2>Detail Produk</h2>

    <a href="cari_produk.php">Kembali ke Cari Produk</a>

    <hr>

    <h3>Foto Produk</h3>

    <?php
    if (mysqli_num_rows($foto_produk) > 0) {
        while ($foto = mysqli_fetch_assoc($foto_produk)) {
    ?>
            <img src="../uploads/produk/<?php echo $foto['foto']; ?>" width="150" style="margin-right:10px;">
    <?php
        }
    } else {
        echo "<p>Belum ada foto produk.</p>";
    }
    ?>

    <hr>

    <h3><?php echo $produk['nama_produk']; ?></h3>

    <p><strong>Kategori:</strong> <?php echo $produk['nama_kategori']; ?></p>
    <p><strong>Harga:</strong> Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
    <p><strong>Deskripsi:</strong> <?php echo $produk['deskripsi']; ?></p>
    <p><strong>Alamat Produk:</strong> <?php echo $produk['alamat_produk']; ?></p>
    <p><strong>Keterangan Nego:</strong> <?php echo $produk['keterangan_nego']; ?></p>
    <p><strong>Status:</strong> <?php echo $produk['status_produk']; ?></p>
    <p><strong>Tanggal Upload:</strong> <?php echo $produk['tanggal_upload']; ?></p>

    <hr>

    <h3>Informasi Penjual</h3>
    <p><strong>Username Penjual:</strong> <?php echo $produk['username']; ?></p>
    <p><strong>Nama Penjual:</strong> <?php echo $produk['nama_lengkap']; ?></p>
    <p><strong>Nomor Telepon:</strong> <?php echo $produk['nomor_telepon']; ?></p>

    <hr>

    <?php if ($produk['status_produk'] == 'Tersedia') { ?>
        <a href="chat.php?id_produk=<?php echo $produk['id_produk']; ?>">Chat Penjual</a>
    <?php } else { ?>
        <p style="color:red;">Produk sudah terjual.</p>
    <?php } ?>

</body>
</html>