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

$daftar_chat = mysqli_query($koneksi, "SELECT chat.*, produk.nama_produk, user.username AS username_pembeli
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
    <title>Pesan Penjual - Pree Love</title>
</head>
<body>

    <h2>Pesan Masuk Penjual</h2>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Pembeli</th>
            <th>Tanggal Chat</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        if (mysqli_num_rows($daftar_chat) > 0) {
            while ($chat = mysqli_fetch_assoc($daftar_chat)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $chat['nama_produk']; ?></td>
            <td><?php echo $chat['username_pembeli']; ?></td>
            <td><?php echo $chat['tanggal_chat']; ?></td>
            <td>
                <a href="detail_chat.php?id_chat=<?php echo $chat['id_chat']; ?>">Buka Chat</a>
            </td>
        </tr>

        <?php
            }
        } else {
        ?>

        <tr>
            <td colspan="5">Belum ada pesan masuk.</td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>