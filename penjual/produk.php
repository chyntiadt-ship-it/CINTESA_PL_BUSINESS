<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori 
    FROM produk 
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    WHERE produk.id_user = '$id_user'
    AND produk.status_produk != 'Dihapus'
    ORDER BY produk.tanggal_upload DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Produk - Pree Love</title>
</head>
<body>

    <h2>Manajemen Produk Penjual</h2>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "tambah_berhasil") {
    echo "<p style='color:green;'>Produk berhasil ditambahkan.</p>";
} elseif ($_GET['pesan'] == "tambah_gagal") {
    echo "<p style='color:red;'>Produk gagal ditambahkan.</p>";
} elseif ($_GET['pesan'] == "edit_berhasil") {
    echo "<p style='color:green;'>Produk berhasil diperbarui.</p>";
} elseif ($_GET['pesan'] == "hapus_berhasil") {
    echo "<p style='color:green;'>Produk berhasil dihapus.</p>";
} elseif ($_GET['pesan'] == "hapus_gagal") {
    echo "<p style='color:red;'>Produk gagal dihapus.</p>";
}
    }
    ?>

    <a href="dashboard.php">Kembali ke Dashboard</a> |
    <a href="tambah_produk.php">Tambah Produk</a>

    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Keterangan Nego</th>
            <th>Alamat</th>
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
            <td><?php echo $produk['keterangan_nego']; ?></td>
            <td><?php echo $produk['alamat_produk']; ?></td>
            <td><?php echo $produk['tanggal_upload']; ?></td>

            <td>
                <a href="edit_produk.php?id=<?php echo $produk['id_produk']; ?>">Edit</a> |
                <a href="hapus_produk.php?id=<?php echo $produk['id_produk']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
            </td>
        </tr>

        <?php
            }
        } else {
        ?>

        <tr>
            <td colspan="10">Belum ada produk.</td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>