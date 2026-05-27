<?php
session_start();
include '../include/koneksi.php';
include '../include/penjual_layout.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$keyword_sql = mysqli_real_escape_string($koneksi, $keyword);

$where_keyword = '';
if ($keyword_sql !== '') {
    $where_keyword = " AND (
        produk.nama_produk LIKE '%$keyword_sql%' OR
        produk.status_produk LIKE '%$keyword_sql%' OR
        produk.keterangan_nego LIKE '%$keyword_sql%' OR
        kategori.nama_kategori LIKE '%$keyword_sql%'
    )";
}

$query = mysqli_query($koneksi, "
    SELECT produk.*, kategori.nama_kategori
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    WHERE produk.id_user = '$id_user'
    AND produk.status_produk != 'Dihapus'
    $where_keyword
    ORDER BY produk.tanggal_upload DESC
");

penjual_page_start('Manajemen Produk', 'produk', $keyword);
?>

<section class="page-hero left">
    <h1>Manajemen Produk</h1>
    <p>Kelola produk yang kamu jual, mulai dari edit informasi, ubah status, sampai hapus produk.</p>
</section>

<?php
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == "tambah_berhasil") echo '<div class="alert success">Produk berhasil ditambahkan.</div>';
    if ($_GET['pesan'] == "tambah_gagal") echo '<div class="alert error">Produk gagal ditambahkan.</div>';
    if ($_GET['pesan'] == "edit_berhasil") echo '<div class="alert success">Produk berhasil diperbarui.</div>';
    if ($_GET['pesan'] == "hapus_berhasil") echo '<div class="alert success">Produk berhasil dihapus.</div>';
    if ($_GET['pesan'] == "hapus_gagal") echo '<div class="alert error">Produk gagal dihapus.</div>';
}
?>

<section class="panel">
    <div class="toolbar">
        <div>
            <h2><?php echo $keyword !== '' ? 'Hasil Pencarian Produk' : 'Produk Saya'; ?></h2>
            <p><?php echo $keyword !== '' ? 'Kata kunci: ' . htmlspecialchars($keyword) : 'Semua produk aktif milik akun penjual ini.'; ?></p>
        </div>
        <a href="tambah_produk.php" class="btn btn-primary">＋ Tambah Produk</a>
    </div>

    <div class="product-grid">
        <?php if (mysqli_num_rows($query) > 0) { ?>
            <?php while ($produk = mysqli_fetch_assoc($query)) {
                $id_produk = $produk['id_produk'];
                $foto_query = mysqli_query($koneksi, "SELECT * FROM produk_foto WHERE id_produk='$id_produk' LIMIT 1");
                $foto = mysqli_fetch_assoc($foto_query);
                $status_class = strtolower($produk['status_produk']);
            ?>
                <article class="product-card">
                    <?php if (!empty($foto['foto'])) { ?>
                        <img src="../uploads/produk/<?php echo htmlspecialchars($foto['foto']); ?>" class="product-img">
                    <?php } else { ?>
                        <div class="no-img">Tidak ada foto</div>
                    <?php } ?>

                    <div class="product-body">
                        <div class="product-topline">
                            <span class="category"><?php echo htmlspecialchars($produk['nama_kategori']); ?></span>
                            <span class="status-pill <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($produk['status_produk']); ?>
                            </span>
                        </div>

                        <h3><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                        <p class="price">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                        <p class="product-desc">
                            <?php echo htmlspecialchars(substr($produk['deskripsi'], 0, 90)); ?>
                            <?php echo strlen($produk['deskripsi']) > 90 ? '...' : ''; ?>
                        </p>
                        <p class="product-meta">Nego: <?php echo htmlspecialchars($produk['keterangan_nego']); ?></p>
                        <p class="product-meta">Alamat: <?php echo htmlspecialchars($produk['alamat_produk']); ?></p>

                        <div class="product-actions">
                            <a href="edit_produk.php?id=<?php echo $produk['id_produk']; ?>" class="action-btn">Edit</a>
                            <a href="#" class="action-btn btn-danger delete-product-btn"data-delete-url="hapus_produk.php?id=<?php echo $produk['id_produk']; ?>"data-product-name="<?php echo htmlspecialchars($produk['nama_produk']); ?>">Hapus</a>
                        </div>
                    </div>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-box">
                <?php echo $keyword !== '' ? 'Produk dengan kata kunci tersebut tidak ditemukan.' : 'Belum ada produk.'; ?>
            </div>
        <?php } ?>
    </div>
</section>

<?php penjual_page_end(); ?>
<div class="delete-modal" id="deleteProductModal">
    <div class="delete-modal-box">
        <div class="delete-modal-icon">!</div>

        <h2>Hapus Produk?</h2>

        <p>
            Produk <strong id="deleteProductName">ini</strong> akan dihapus dari daftar produkmu.
            Tindakan ini tidak bisa dibatalkan.
        </p>

        <div class="delete-modal-actions">
            <button type="button" class="btn btn-soft" id="cancelDeleteBtn">
                Batal
            </button>

            <button type="button" class="btn btn-danger-confirm" id="confirmDeleteBtn">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
const deleteModal = document.getElementById('deleteProductModal');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const deleteProductName = document.getElementById('deleteProductName');

let deleteUrl = '';

document.querySelectorAll('.delete-product-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        deleteUrl = this.dataset.deleteUrl;
        deleteProductName.textContent = this.dataset.productName;

        deleteModal.classList.add('show');
        document.body.classList.add('modal-open');
    });
});

cancelDeleteBtn.addEventListener('click', closeDeleteModal);

deleteModal.addEventListener('click', function (e) {
    if (e.target === deleteModal) {
        closeDeleteModal();
    }
});

confirmDeleteBtn.addEventListener('click', function () {
    if (deleteUrl !== '') {
        window.location.href = deleteUrl;
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

function closeDeleteModal() {
    deleteModal.classList.remove('show');
    document.body.classList.remove('modal-open');
    deleteUrl = '';
}
</script>