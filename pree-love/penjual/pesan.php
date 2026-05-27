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

$id_user = (int) $_SESSION['id_user'];

$query_pesan = mysqli_query($koneksi, "
    SELECT 
        c.*,
        p.nama_produk,
        u.username AS nama_pembeli
    FROM chat c
    JOIN produk p ON c.id_produk = p.id_produk
    JOIN user u ON c.id_pembeli = u.id_user
    WHERE c.id_penjual = '$id_user'
    AND c.id_chat IN (
        SELECT MAX(id_chat)
        FROM chat
        WHERE id_penjual = '$id_user'
        GROUP BY id_produk, id_pembeli
    )
    ORDER BY c.tanggal_chat DESC
");

penjual_page_start('Pesan Penjual', 'pesan');
?>

<section class="page-hero messages-hero">
    <h1>Pesan Masuk Penjual</h1>
    <p>Lihat dan balas pesan dari pembeli yang tertarik dengan produkmu.</p>
</section>

<section class="messages-card">
    <div class="messages-head">
        <div>
            <h2>Daftar Pesan</h2>
            <p>Pesan terbaru dari pembeli akan tampil di bagian atas.</p>
        </div>
    </div>

    <div class="messages-list">
        <?php if (mysqli_num_rows($query_pesan) > 0) { ?>
            <?php while ($pesan = mysqli_fetch_assoc($query_pesan)) { ?>
                <article class="message-item">
                    <div class="message-avatar">
                        <?php echo strtoupper(substr($pesan['nama_pembeli'], 0, 1)); ?>
                    </div>

                    <div class="message-content">
                        <div class="message-top">
                            <h3><?php echo htmlspecialchars($pesan['nama_pembeli']); ?></h3>
                            <span>
                                <?php echo date('d M Y, H:i', strtotime($pesan['tanggal_chat'])); ?>
                            </span>
                        </div>

                        <p class="message-product">
                            Produk: <?php echo htmlspecialchars($pesan['nama_produk']); ?>
                        </p>

                        <p class="message-preview">
                            <?php 
                            if (isset($pesan['pesan'])) {
                                echo htmlspecialchars(substr($pesan['pesan'], 0, 90));
                                echo strlen($pesan['pesan']) > 90 ? '...' : '';
                            } else {
                                echo 'Ada pesan masuk dari pembeli.';
                            }
                            ?>
                        </p>
                    </div>

                    <a 
                    href="detail_chat.php?id_chat=<?php echo $pesan['id_chat']; ?>" 
                    class="btn btn-primary message-btn"
                    >
                    Buka Chat
                </a>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-box">
                Belum ada pesan masuk dari pembeli.
            </div>
        <?php } ?>
    </div>
</section>

<?php penjual_page_end(); ?>