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

$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - CINTESA</title>
</head>
<body>

    <h2>Tambah Produk</h2>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "foto_lebih") {
            echo "<p style='color:red;'>Foto produk maksimal 3.</p>";
        } elseif ($_GET['pesan'] == "foto_invalid") {
            echo "<p style='color:red;'>Foto harus JPG, JPEG, atau PNG.</p>";
        } elseif ($_GET['pesan'] == "data_kosong") {
            echo "<p style='color:red;'>Data wajib diisi lengkap.</p>";
        } elseif ($_GET['pesan'] == "gagal") {
            echo "<p style='color:red;'>Produk gagal ditambahkan.</p>";
        }
    }
    ?>

    <form action="proses_tambah_produk.php" method="POST" enctype="multipart/form-data">

        <label>Foto Produk Maksimal 3</label><br>
        <input type="file" name="foto_produk[]" multiple required>
        <br><br>

        <label>Kategori Produk</label><br>
        <select name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>

            <?php while ($row = mysqli_fetch_assoc($kategori)) { ?>
                <option value="<?php echo $row['id_kategori']; ?>">
                    <?php echo htmlspecialchars($row['nama_kategori']); ?>
                </option>
            <?php } ?>

        </select>
        <br><br>

        <label>Nama Produk</label><br>
        <input type="text" name="nama_produk" required>
        <br><br>

        <label>Deskripsi Produk</label><br>
        <textarea name="deskripsi" rows="5" required></textarea>
        <br><br>

        <label>Kondisi Barang</label><br>
        <select name="kondisi_barang" required>
            <option value="">-- Pilih Kondisi Barang --</option>
            <option value="Baru">Baru</option>
            <option value="Bekas Like New">Bekas Like New</option>
            <option value="Bekas Baik">Bekas Baik</option>
            <option value="Bekas Normal">Bekas Normal</option>
            <option value="Bekas Minus">Bekas Minus</option>
        </select>
        <br><br>

        <label>Ukuran</label><br>
        <input
            type="text"
            name="ukuran"
            placeholder="Contoh: S / M / L / XL / 42 / 128GB"
            required
        >
        <br><br>

        <label>Merek</label><br>
        <input
            type="text"
            name="merek"
            placeholder="Contoh: Uniqlo, Samsung, IKEA"
            required
        >
        <br><br>

        <label>Alamat Produk</label><br>
        <textarea name="alamat_produk" rows="3" required></textarea>
        <br><br>

        <label>Harga Produk</label><br>
        <input type="number" name="harga" required>
        <br><br>

        <label>Keterangan Nego</label><br>
        <select name="keterangan_nego" required>
            <option value="">-- Pilih Keterangan --</option>
            <option value="Bisa Nego">Bisa Nego</option>
            <option value="Tidak Bisa Nego">Tidak Bisa Nego</option>
        </select>
        <br><br>

        <label>Status Produk</label><br>
        <select name="status_produk" required>
            <option value="Tersedia">Tersedia</option>
            <option value="Terjual">Terjual</option>
        </select>
        <br><br>

        <button type="submit" name="simpan">Simpan Produk</button>

    </form>

    <br>

    <a href="produk.php">Kembali ke Manajemen Produk</a>

</body>
</html>