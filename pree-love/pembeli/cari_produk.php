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

// Ambil data kategori untuk filter
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Ambil keyword dan kategori dari form pencarian
$keyword = "";
$id_kategori = "";

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
}

if (isset($_GET['id_kategori'])) {
    $id_kategori = mysqli_real_escape_string($koneksi, $_GET['id_kategori']);
}

// Query dasar produk
$query_produk = "SELECT produk.*, kategori.nama_kategori, user.username 
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.status_produk = 'Tersedia'
";

// Filter keyword
if (!empty($keyword)) {
    $query_produk .= " AND produk.nama_produk LIKE '%$keyword%'";
}

// Filter kategori
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
    <title>Cari Produk - Pree Love</title>
</head>
<body>

    <h2>Cari Produk</h2>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <form action="" method="GET">
        <label>Cari Nama Produk</label><br>
        <input type="text" name="keyword" placeholder="Contoh: hoodie, dress, celana" value="<?php echo $keyword; ?>">
        <br><br>

        <label>Pilih Kategori</label><br>
        <select name="id_kategori">
            <option value="">Semua Kategori</option>

            <?php while ($row = mysqli_fetch_assoc($kategori)) { ?>
                <option value="<?php echo $row['id_kategori']; ?>"
                    <?php if ($id_kategori == $row['id_kategori']) echo "selected"; ?>>
                    <?php echo $row['nama_kategori']; ?>
                </option>
            <?php } ?>

        </select>
        <br><br>

        <button type="submit">Cari Produk</button>
        <a href="cari_produk.php">Reset</a>
    </form>

    <hr>

    <h3>Daftar Produk</h3>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Lokasi</th>
            <th>Penjual</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        if (mysqli_num_rows($produk) > 0) {
            while ($row = mysqli_fetch_assoc($produk)) {
                $id_produk = $row['id_produk'];

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
                    <img src="../uploads/produk/<?php echo $foto['foto']; ?>" width="90">
                <?php } else { ?>
                    Tidak ada foto
                <?php } ?>
            </td>

            <td><?php echo $row['nama_produk']; ?></td>
            <td><?php echo $row['nama_kategori']; ?></td>
            <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
            <td><?php echo $row['alamat_produk']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td>
                <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>">Lihat Detail</a>
            </td>
        </tr>

        <?php
            }
        } else {
        ?>

        <tr>
            <td colspan="8">Produk tidak ditemukan.</td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>