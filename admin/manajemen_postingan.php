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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Postingan - Pree Love</title>
</head>
<body>

    <h2>Manajemen Postingan</h2>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "hapus_berhasil") {
            echo "<p style='color:green;'>Postingan berhasil dihapus.</p>";
        } elseif ($_GET['pesan'] == "hapus_gagal") {
            echo "<p style='color:red;'>Postingan gagal dihapus.</p>";
        }
    }
    ?>

    <table border="1" cellpadding="10" cellspacing="0">
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
        ?>

        <tr>
            <td><?php echo $no++; ?></td>

            <td>
                <?php if (!empty($foto['foto'])) { ?>
                    <img src="../uploads/produk/<?php echo $foto['foto']; ?>" width="80">
                <?php } else { ?>
                    Tidak ada foto
                <?php } ?>
            </td>

            <td><?php echo $produk['nama_produk']; ?></td>
            <td><?php echo $produk['nama_kategori']; ?></td>
            <td>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
            <td><?php echo $produk['status_produk']; ?></td>
            <td><?php echo $produk['username']; ?> / <?php echo $produk['nama_lengkap']; ?></td>
            <td><?php echo $produk['tanggal_upload']; ?></td>

            <td>
                <a href="hapus_postingan.php?id=<?php echo $produk['id_produk']; ?>" onclick="return confirm('Yakin ingin menghapus postingan ini?')">
                    Hapus
                </a>
            </td>
        </tr>

        <?php
            }
        } else {
        ?>

        <tr>
            <td colspan="9">Belum ada postingan produk.</td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>