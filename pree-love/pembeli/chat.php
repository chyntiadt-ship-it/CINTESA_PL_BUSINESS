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

if (isset($_GET['id_produk'])) {
    $id_produk = mysqli_real_escape_string($koneksi, $_GET['id_produk']);

    $query_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_produk'");

    if (mysqli_num_rows($query_produk) == 0) {
        echo "Produk tidak ditemukan.";
        exit;
    }

    $produk = mysqli_fetch_assoc($query_produk);
    $id_penjual = $produk['id_user'];

    $cek_chat = mysqli_query($koneksi, "SELECT * FROM chat 
        WHERE id_produk='$id_produk' 
        AND id_pembeli='$id_pembeli' 
        AND id_penjual='$id_penjual'
    ");

    if (mysqli_num_rows($cek_chat) > 0) {
        $data_chat = mysqli_fetch_assoc($cek_chat);
        $id_chat = $data_chat['id_chat'];
    } else {
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
    <title>Riwayat Chat - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/chat_pembeli.css?v=99">
    <script src="../assets/js/chat_pembeli.js?v=99"></script>
</head>
<body>
<header class="chat-top-navbar">
    <div class="chat-top-left">
        <button type="button" class="chat-menu-btn">☰</button>
        <a href="dashboard.php" class="chat-brand">CINTESA</a>
    </div>

    <form class="chat-search" action="cari_produk.php" method="GET">
        <span>⌕</span>
        <input type="text" name="keyword" placeholder="Cari produk saya...">
    </form>
</header>
<main class="chat-history-page">
    <header class="history-header">
        <a href="dashboard.php" class="back-btn">‹</a>
        <h1>Riwayat Chat</h1>
    </header>

    <section class="history-list">
        <?php if (mysqli_num_rows($daftar_chat) > 0) { ?>
            <?php while ($chat = mysqli_fetch_assoc($daftar_chat)) { ?>
                <a href="chat.php?id_chat=<?php echo $chat['id_chat']; ?>" class="history-card">
                    <div class="seller-avatar">
                        <?php echo strtoupper(substr($chat['username_penjual'], 0, 1)); ?>
                    </div>

                    <div class="history-content">
                        <h3><?php echo htmlspecialchars($chat['username_penjual']); ?></h3>
                        <p><?php echo htmlspecialchars($chat['nama_produk']); ?></p>
                        <small><?php echo htmlspecialchars($chat['tanggal_chat']); ?></small>
                    </div>

                    <span class="open-icon">›</span>
                </a>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-chat">
                Belum ada riwayat chat.
            </div>
        <?php } ?>
    </section>
</main>

</body>
</html>

<?php
exit;
}

$id_chat = mysqli_real_escape_string($koneksi, $_GET['id_chat']);

$sql_chat = "
    SELECT 
        chat.*, 
        produk.nama_produk, 
        produk.status_produk, 
        produk.harga,
        user.username AS username_penjual, 
        user.nama_lengkap AS nama_penjual
    FROM chat
    JOIN produk ON chat.id_produk = produk.id_produk
    JOIN user ON chat.id_penjual = user.id_user
    WHERE chat.id_chat = '$id_chat'
    AND chat.id_pembeli = '$id_pembeli'
";

$query_chat = mysqli_query($koneksi, $sql_chat);

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
    <title>Chat Pembeli - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/chat_pembeli.css?v=120">
</head>
<body>
<header class="chat-global-navbar">
    <div class="chat-nav-left">
        <button type="button" class="chat-menu-btn">☰</button>
        <a href="dashboard.php" class="chat-brand">CINTESA</a>
    </div>

    <form action="cari_produk.php" method="GET" class="chat-nav-search">
        <span>⌕</span>
        <input type="text" name="keyword" placeholder="Cari produk saya...">
    </form>

    <div class="chat-nav-actions">
        <a href="chat.php" class="chat-nav-btn active">◈</a>
        <a href="chat.php" class="chat-nav-btn">▣</a>
    </div>
</header>
<main class="chat-page">

    <header class="chat-header">
    <a href="chat.php" class="back-btn">‹</a>

    <div class="seller-avatar">
        <?php echo strtoupper(substr($data_chat['username_penjual'], 0, 1)); ?>
    </div>

    <div class="seller-head">
        <strong><?php echo htmlspecialchars($data_chat['nama_penjual'] ?? $data_chat['username_penjual']); ?></strong>
        <p>@<?php echo htmlspecialchars($data_chat['username_penjual']); ?></p>
        <span class="product-pill">
            Produk: <?php echo htmlspecialchars($data_chat['nama_produk']); ?>
        </span>
    </div>

    <div class="chat-product-price">
        <small>Harga Produk</small>
        <strong>
            Rp<?php echo number_format($data_chat['harga'] ?? 0, 0, ',', '.'); ?>
        </strong>
    </div>
</header>


    <section class="info-box">
    <div>
        <strong>Catatan Keamanan</strong>
        <p>
            Gunakan chat CINTESA untuk membahas stok, kondisi barang, nego harga,
            dan detail produk. Hindari transaksi di luar platform.
        </p>
    </div>
</section>

    <section class="chat-body" id="chatBody">
        <?php if (mysqli_num_rows($pesan) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($pesan)) { 
                $is_me = ($row['id_pengirim'] == $id_pembeli);
            ?>
                <div class="message-row <?php echo $is_me ? 'me' : 'seller'; ?>">
                    <div class="message-bubble">
                        <p><?php echo htmlspecialchars($row['isi_pesan']); ?></p>
                        <small><?php echo htmlspecialchars($row['tanggal_pesan']); ?></small>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-chat">
                Belum ada pesan. Mulai chat dengan penjual.
            </div>
        <?php } ?>
    </section>

    <section class="quick-replies">
        <button type="button">Halo, produk ini masih tersedia?</button>
        <button type="button">Bisa dikirim hari ini?</button>
        <button type="button">Terima kasih!</button>
    </section>
<form action="proses_kirim_pesan.php" method="POST" class="chat-input-bar" enctype="multipart/form-data">
    <input type="hidden" name="id_chat" value="<?php echo $id_chat; ?>">

    <div class="attach-wrap">
        <button type="button" class="plus-btn" id="attachToggle">＋</button>

        <div class="attach-menu" id="attachMenu">
            <button type="button" id="chooseGallery">
                <span>🖼️</span>
                Pilih dari Galeri
            </button>

            <button type="button" id="openCamera">
                <span>📷</span>
                Ambil Langsung
            </button>
        </div>
    </div>

    <textarea name="isi_pesan" rows="1" placeholder="Tulis Pesan..." required></textarea>

    <button type="submit" name="kirim" class="send-btn">➤</button>

    <input type="file" id="galleryInput" name="gambar_pesan" accept="image/*" hidden>
    <input type="file" id="cameraInput" name="gambar_kamera" accept="image/*" capture="environment" hidden>
</form>

</main>

<script src="../assets/js/chat_pembeli.js?v=120"></script>
</body>
</html>
