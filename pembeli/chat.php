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

$id_pembeli = $_SESSION['id_user'];

// Jika pembeli klik tombol Chat Penjual dari detail produk
if (isset($_GET['id_produk'])) {
    $id_produk = mysqli_real_escape_string($koneksi, $_GET['id_produk']);

    // Ambil data produk dan penjual
    $query_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_produk'");

    if (mysqli_num_rows($query_produk) == 0) {
        echo "Produk tidak ditemukan.";
        exit;
    }

    $produk = mysqli_fetch_assoc($query_produk);
    $id_penjual = $produk['id_user'];

    // Cek apakah chat sudah ada
    $cek_chat = mysqli_query($koneksi, "SELECT * FROM chat 
        WHERE id_produk='$id_produk' 
        AND id_pembeli='$id_pembeli' 
        AND id_penjual='$id_penjual'
    ");

    if (mysqli_num_rows($cek_chat) > 0) {
        $data_chat = mysqli_fetch_assoc($cek_chat);
        $id_chat = $data_chat['id_chat'];
    } else {
        // Buat chat baru
        mysqli_query($koneksi, "INSERT INTO chat 
            (id_produk, id_pembeli, id_penjual)
            VALUES 
            ('$id_produk', '$id_pembeli', '$id_penjual')
        ");

        $id_chat = mysqli_insert_id($koneksi);
    }

    header("Location: chat.php?id_chat=$id_chat");
    exit;
}

// Jika tidak ada id_chat, tampilkan daftar riwayat chat pembeli
if (!isset($_GET['id_chat'])) {
    $daftar_chat = mysqli_query($koneksi, "SELECT chat.*, produk.nama_produk, user.username AS username_penjual
        FROM chat
        JOIN produk ON chat.id_produk = produk.id_produk
        JOIN user ON chat.id_penjual = user.id_user
        WHERE chat.id_pembeli='$id_pembeli'
        ORDER BY chat.tanggal_chat DESC
    ");
    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Riwayat Chat - Pree Love</title>
    </head>
    <body>

        <h2>Riwayat Chat Pembeli</h2>

        <a href="dashboard.php">Kembali ke Dashboard</a> |
        <a href="cari_produk.php">Cari Produk</a>

        <hr>

        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Penjual</th>
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
                <td><?php echo $chat['username_penjual']; ?></td>
                <td><?php echo $chat['tanggal_chat']; ?></td>
                <td>
                    <a href="chat.php?id_chat=<?php echo $chat['id_chat']; ?>">Buka Chat</a>
                </td>
            </tr>

            <?php
                }
            } else {
            ?>

            <tr>
                <td colspan="5">Belum ada riwayat chat.</td>
            </tr>

            <?php } ?>

        </table>

    </body>
    </html>

    <?php
    exit;
}

// Jika ada id_chat, tampilkan isi chat
$id_chat = mysqli_real_escape_string($koneksi, $_GET['id_chat']);

$query_chat = mysqli_query($koneksi, "SELECT chat.*, produk.nama_produk, user.username AS username_penjual
    FROM chat
    JOIN produk ON chat.id_produk = produk.id_produk
    JOIN user ON chat.id_penjual = user.id_user
    WHERE chat.id_chat='$id_chat'
    AND chat.id_pembeli='$id_pembeli'
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
    <title>Chat Pembeli - Pree Love</title>
</head>
<body>

    <h2>Chat dengan Penjual</h2>

    <p><strong>Produk:</strong> <?php echo $data_chat['nama_produk']; ?></p>
    <p><strong>Penjual:</strong> <?php echo $data_chat['username_penjual']; ?></p>

    <a href="chat.php">Kembali ke Riwayat Chat</a>

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
        echo "<p>Belum ada pesan. Mulai chat dengan penjual.</p>";
    }
    ?>

    <form action="proses_kirim_pesan.php" method="POST">
        <input type="hidden" name="id_chat" value="<?php echo $id_chat; ?>">

        <label>Tulis Pesan</label><br>
        <textarea name="isi_pesan" rows="4" required></textarea><br><br>

        <button type="submit" name="kirim">Kirim Pesan</button>
    </form>

</body>
</html>