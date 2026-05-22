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

$id_penjual = $_SESSION['id_user'];

if (!isset($_GET['id_chat'])) {
    header("Location: pesan.php");
    exit;
}

$id_chat = mysqli_real_escape_string($koneksi, $_GET['id_chat']);

$query_chat = mysqli_query($koneksi, "SELECT chat.*, produk.nama_produk, user.username AS username_pembeli
    FROM chat
    JOIN produk ON chat.id_produk = produk.id_produk
    JOIN user ON chat.id_pembeli = user.id_user
    WHERE chat.id_chat='$id_chat'
    AND chat.id_penjual='$id_penjual'
");

if (mysqli_num_rows($query_chat) == 0) {
    echo "Chat tidak ditemukan.";
    exit;
}

$data_chat = mysqli_fetch_assoc($query_chat);

$pesan = mysqli_query($koneksi, "SELECT pesan.*, user.username 
    FROM pesan
    JOIN user ON pesan.id_pengirim = user.id_user
    WHERE pesan.id_chat='$id_chat'
    ORDER BY pesan.tanggal_pesan ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Chat Penjual - Pree Love</title>
</head>
<body>

    <h2>Detail Chat</h2>

    <p><strong>Produk:</strong> <?php echo $data_chat['nama_produk']; ?></p>
    <p><strong>Pembeli:</strong> <?php echo $data_chat['username_pembeli']; ?></p>

    <a href="pesan.php">Kembali ke Pesan</a>

    <hr>

    <h3>Isi Chat</h3>

    <?php
    if (mysqli_num_rows($pesan) > 0) {
        while ($row = mysqli_fetch_assoc($pesan)) {
    ?>

        <p>
            <strong><?php echo $row['username']; ?>:</strong>
            <?php echo $row['isi_pesan']; ?><br>
            <small><?php echo $row['tanggal_pesan']; ?></small>
        </p>
        <hr>

    <?php
        }
    } else {
        echo "<p>Belum ada pesan.</p>";
    }
    ?>

    <form action="proses_balas_pesan.php" method="POST">
        <input type="hidden" name="id_chat" value="<?php echo $id_chat; ?>">

        <label>Balas Pesan</label><br>
        <textarea name="isi_pesan" rows="4" required></textarea><br><br>

        <button type="submit" name="balas">Kirim Balasan</button>
    </form>

</body>
</html>