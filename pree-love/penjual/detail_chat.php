<?php
session_start();

include __DIR__ . '/../include/koneksi.php';
include __DIR__ . '/../include/penjual_layout.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_penjual = (int) $_SESSION['id_user'];

if (!isset($_GET['id_chat'])) {
    header("Location: pesan.php");
    exit;
}

$id_chat = (int) $_GET['id_chat'];

$query_chat = mysqli_query($koneksi, "
    SELECT 
        chat.*,
        produk.nama_produk,
        produk.status_produk,
        produk.harga,
        user.username AS username_pembeli,
        user.nama_lengkap AS nama_pembeli,
        user.foto_profile AS foto_pembeli
    FROM chat
    JOIN produk ON chat.id_produk = produk.id_produk
    JOIN user ON chat.id_pembeli = user.id_user
    WHERE chat.id_chat = '$id_chat'
    AND chat.id_penjual = '$id_penjual'
    LIMIT 1
");

if (mysqli_num_rows($query_chat) == 0) {
    penjual_page_start('Chat Tidak Ditemukan', 'pesan');
    echo '<div class="alert error">Chat tidak ditemukan atau bukan milik akun penjual ini.</div>';
    echo '<a href="pesan.php" class="btn btn-primary">Kembali ke Pesan</a>';
    penjual_page_end();
    exit;
}

$data_chat = mysqli_fetch_assoc($query_chat);

/* Kirim balasan langsung dari halaman ini */
if (isset($_POST['kirim'])) {
    $isi_pesan = mysqli_real_escape_string($koneksi, trim($_POST['isi_pesan']));

    if ($isi_pesan != '') {
        mysqli_query($koneksi, "
            INSERT INTO pesan (id_chat, id_pengirim, isi_pesan)
            VALUES ('$id_chat', '$id_penjual', '$isi_pesan')
        ");
    }

    header("Location: detail_chat.php?id_chat=$id_chat");
    exit;
}

$query_pesan = mysqli_query($koneksi, "
    SELECT 
        pesan.*,
        user.username,
        user.role
    FROM pesan
    JOIN user ON pesan.id_pengirim = user.id_user
    WHERE pesan.id_chat = '$id_chat'
    ORDER BY pesan.tanggal_pesan ASC
");

$foto_path = '../uploads/profile/' . $data_chat['foto_pembeli'];
$ada_foto = !empty($data_chat['foto_pembeli']) && file_exists($foto_path);

penjual_page_start('Buka Chat', 'pesan');
?>

<section class="seller-chat-page">
    <div class="seller-chat-card">

        <header class="seller-chat-header">
            <a href="pesan.php" class="chat-back-btn">←</a>

            <div class="buyer-avatar-large">
                <?php if ($ada_foto) { ?>
                    <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Pembeli">
                <?php } else { ?>
                    <?php echo strtoupper(substr($data_chat['username_pembeli'], 0, 1)); ?>
                <?php } ?>
            </div>

            <div class="seller-chat-info">
                <h1><?php echo htmlspecialchars($data_chat['nama_pembeli'] ?: $data_chat['username_pembeli']); ?></h1>
                <p>@<?php echo htmlspecialchars($data_chat['username_pembeli']); ?></p>
                <div class="chat-product-pill">
                    Produk: <?php echo htmlspecialchars($data_chat['nama_produk']); ?>
                </div>
            </div>

            <div class="chat-product-price">
                <span>Harga Produk</span>
                <strong>Rp<?php echo number_format($data_chat['harga'], 0, ',', '.'); ?></strong>
            </div>
        </header>

        <section class="seller-chat-warning">
            <strong>Catatan Keamanan</strong>
            <p>Gunakan chat CINTESA untuk membahas stok, kondisi barang, nego harga, dan detail produk. Hindari transaksi di luar platform.</p>
        </section>

        <section class="seller-chat-body" id="sellerChatBody">
            <?php if (mysqli_num_rows($query_pesan) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($query_pesan)) { 
                    $is_me = ((int) $row['id_pengirim'] === $id_penjual);
                ?>
                    <div class="seller-message-row <?php echo $is_me ? 'me' : 'buyer'; ?>">
                        <div class="seller-message-bubble">
                            <p><?php echo nl2br(htmlspecialchars($row['isi_pesan'])); ?></p>
                            <small>
                                <?php echo $is_me ? 'Saya' : htmlspecialchars($row['username']); ?> •
                                <?php echo date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?>
                            </small>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-box">
                    Belum ada isi pesan. Mulai balas pembeli dari kolom pesan di bawah.
                </div>
            <?php } ?>
        </section>

        <section class="seller-quick-replies">
            <button type="button" data-seller-reply="Halo, produk ini masih tersedia ya.">Produk tersedia</button>
            <button type="button" data-seller-reply="Harga masih bisa dinegosiasikan.">Bisa nego</button>
            <button type="button" data-seller-reply="Terima kasih sudah menghubungi saya.">Terima kasih</button>
        </section>

        <form method="POST" class="seller-chat-input" id="sellerChatForm">
            <textarea 
            name="isi_pesan" 
            id="sellerReplyTextarea"
            rows="1" 
            placeholder="Tulis balasan untuk pembeli..." 
            required
            ></textarea>
            
            <button type="submit" name="kirim" id="sellerSendButton" class="seller-send-btn">➤</button>
        </form>

    </div>
</section>

<script>
const sellerChatBody = document.getElementById('sellerChatBody');
const sellerReplyTextarea = document.getElementById('sellerReplyTextarea');
const sellerChatForm = document.getElementById('sellerChatForm');
const sellerSendButton = document.getElementById('sellerSendButton');

if (sellerChatBody) {
    sellerChatBody.scrollTop = sellerChatBody.scrollHeight;
}

function autoResizeTextarea() {
    sellerReplyTextarea.style.height = 'auto';
    sellerReplyTextarea.style.height = Math.min(sellerReplyTextarea.scrollHeight, 120) + 'px';
}

if (sellerReplyTextarea && sellerChatForm && sellerSendButton) {
    sellerReplyTextarea.addEventListener('input', autoResizeTextarea);

    sellerReplyTextarea.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();

            const pesan = sellerReplyTextarea.value.trim();

            if (pesan !== '') {
                sellerChatForm.requestSubmit(sellerSendButton);
            }
        }
    });
}

document.querySelectorAll('[data-seller-reply]').forEach(button => {
    button.addEventListener('click', function () {
        sellerReplyTextarea.value = this.dataset.sellerReply;
        sellerReplyTextarea.focus();
        autoResizeTextarea();
    });
});
</script>

<?php penjual_page_end(); ?>