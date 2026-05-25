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

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data produk dan pastikan produk milik penjual yang login
$query_produk = mysqli_query($koneksi, "SELECT * FROM produk 
    WHERE id_produk='$id_produk' 
    AND id_user='$id_user'
");

if (mysqli_num_rows($query_produk) == 0) {
    echo "Produk tidak ditemukan atau bukan milik Anda.";
    exit;
}

$produk = mysqli_fetch_assoc($query_produk);

// Ambil semua kategori
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Ambil foto produk
$foto_produk = mysqli_query($koneksi, "SELECT * FROM produk_foto WHERE id_produk='$id_produk'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - Pree Love</title>
</head>
<body>

    <h2>Edit Produk</h2>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "foto_lebih") {
            echo "<p style='color:red;'>Foto produk maksimal 3.</p>";
        } elseif ($_GET['pesan'] == "foto_invalid") {
            echo "<p style='color:red;'>Foto harus JPG, JPEG, atau PNG.</p>";
        } elseif ($_GET['pesan'] == "data_kosong") {
            echo "<p style='color:red;'>Data wajib diisi lengkap.</p>";
        } elseif ($_GET['pesan'] == "gagal") {
            echo "<p style='color:red;'>Produk gagal diperbarui.</p>";
        }
    }
    ?>

    <h3>Foto Produk Saat Ini</h3>

    <?php
    if (mysqli_num_rows($foto_produk) > 0) {
        while ($foto = mysqli_fetch_assoc($foto_produk)) {
    ?>
            <img src="../uploads/produk/<?php echo $foto['foto']; ?>" width="100" style="margin-right:10px;">
    <?php
        }
    } else {
        echo "<p>Belum ada foto produk.</p>";
    }
    ?>

    <br><br>

    <form action="proses_edit_produk.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_produk" value="<?php echo $produk['id_produk']; ?>">

        <label>Ganti Foto Produk Maksimal 3</label><br>
        <small>Jika memilih foto baru, foto lama akan diganti.</small><br>
        <input type="file" name="foto_produk[]" multiple><br><br>

        <label>Kategori Produk</label><br>
        <select name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>

            <?php while ($row = mysqli_fetch_assoc($kategori)) { ?>
                <option value="<?php echo $row['id_kategori']; ?>"
                    <?php if ($row['id_kategori'] == $produk['id_kategori']) echo "selected"; ?>>
                    <?php echo $row['nama_kategori']; ?>
                </option>
            <?php } ?>

        </select><br><br>

        <label>Nama Produk</label><br>
        <input type="text" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required><br><br>

        <label>Deskripsi Produk</label><br>
        <textarea name="deskripsi" rows="5" required><?php echo $produk['deskripsi']; ?></textarea><br><br>
        <label>Kondisi Barang</label><br>
<select name="kondisi_barang" required>
    <option value="">-- Pilih Kondisi Barang --</option>
    <option value="Baru" <?php if ($produk['kondisi_barang'] == 'Baru') echo "selected"; ?>>Baru</option>
    <option value="Bekas Like New" <?php if ($produk['kondisi_barang'] == 'Bekas Like New') echo "selected"; ?>>Bekas Like New</option>
    <option value="Bekas Baik" <?php if ($produk['kondisi_barang'] == 'Bekas Baik') echo "selected"; ?>>Bekas Baik</option>
    <option value="Bekas Normal" <?php if ($produk['kondisi_barang'] == 'Bekas Normal') echo "selected"; ?>>Bekas Normal</option>
    <option value="Bekas Minus" <?php if ($produk['kondisi_barang'] == 'Bekas Minus') echo "selected"; ?>>Bekas Minus</option>
</select><br><br>

<label>Ukuran</label><br>
<input type="text" name="ukuran" value="<?php echo htmlspecialchars($produk['ukuran']); ?>" required><br><br>

<label>Merek</label><br>
<input type="text" name="merek" value="<?php echo htmlspecialchars($produk['merek']); ?>" required><br><br>
        <label>Alamat Produk</label><br>
        <textarea name="alamat_produk" rows="3" required><?php echo $produk['alamat_produk']; ?></textarea><br><br>

        <label>Harga Produk</label><br>
        <input type="number" name="harga" value="<?php echo $produk['harga']; ?>" required><br><br>

        <label>Keterangan Nego</label><br>
        <select name="keterangan_nego" required>
            <option value="Bisa Nego" <?php if ($produk['keterangan_nego'] == 'Bisa Nego') echo "selected"; ?>>
                Bisa Nego
            </option>
            <option value="Tidak Bisa Nego" <?php if ($produk['keterangan_nego'] == 'Tidak Bisa Nego') echo "selected"; ?>>
                Tidak Bisa Nego
            </option>
        </select><br><br>

        <label>Status Produk</label><br>
        <select name="status_produk" required>
            <option value="Tersedia" <?php if ($produk['status_produk'] == 'Tersedia') echo "selected"; ?>>
                Tersedia
            </option>
            <option value="Terjual" <?php if ($produk['status_produk'] == 'Terjual') echo "selected"; ?>>
                Terjual
            </option>
        </select><br><br>

        <button type="submit" name="update">Update Produk</button>
    </form>

    <br>
    <a href="produk.php">Kembali ke Manajemen Produk</a>

</body>
</html>