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

$riwayat = mysqli_query($koneksi, "
    SELECT *
    FROM customer_service
    WHERE id_user = '$id_user'
    ORDER BY tanggal_pesan DESC
    LIMIT 5
");

penjual_page_start('Customer Service Penjual', 'cs');
?>

<section class="page-hero service-hero">
    <h1>Customer Service</h1>
    <p>Sampaikan keluhan, kritik, saran, atau laporan kepada admin CINTESA.</p>
</section>

<?php if (isset($_GET['pesan'])) { ?>
    <?php if ($_GET['pesan'] == 'berhasil') { ?>
        <div class="alert success">Pesan berhasil dikirim ke admin.</div>
    <?php } elseif ($_GET['pesan'] == 'kosong') { ?>
        <div class="alert error">Jenis pesan dan isi pesan wajib diisi.</div>
    <?php } elseif ($_GET['pesan'] == 'gagal') { ?>
        <div class="alert error">Pesan gagal dikirim. Silakan coba lagi.</div>
    <?php } ?>
<?php } ?>

<section class="service-card">
    <div class="service-info">
        <div class="service-icon">?</div>

        <h2>Butuh Bantuan?</h2>
        <p>
            Gunakan halaman ini untuk menghubungi admin jika ada kendala terkait produk,
            akun penjual, pesan pembeli, transaksi, atau laporan lainnya.
        </p>

        <div class="service-note">
            <strong>Catatan</strong>
            <p>Admin akan membaca pesanmu melalui halaman manajemen customer service.</p>
        </div>
    </div>

    <form action="proses_customer_service.php" method="POST" class="service-form">
        <div class="field-group">
            <label>Jenis Pesan <span>*</span></label>
            <select name="jenis_pesan" required>
                <option value="">Pilih Jenis Pesan</option>
                <option value="Keluhan">Keluhan</option>
                <option value="Saran">Saran</option>
                <option value="Kritik">Kritik</option>
                <option value="Laporan">Laporan</option>
            </select>
        </div>

        <div class="field-group">
            <label>Isi Pesan <span>*</span></label>
            <textarea 
                name="isi_pesan" 
                rows="8" 
                maxlength="1000"
                placeholder="Tulis keluhan, kritik, saran, atau laporan kamu..."
                required
                id="serviceMessage"
            ></textarea>
            <small><span id="charCount">0</span>/1000 karakter</small>
        </div>

        <div class="form-actions service-actions">
            <button type="submit" name="kirim" class="btn btn-primary">
                Kirim ke Admin
            </button>
        </div>
    </form>
</section>

<section class="service-history-card">
    <div class="section-head">
        <div>
            <h2>Riwayat Pesan</h2>
            <p>Beberapa pesan terakhir yang kamu kirim ke admin.</p>
        </div>
    </div>

    <div class="service-history-list">
        <?php if (mysqli_num_rows($riwayat) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($riwayat)) { ?>
                <article class="service-history-item">
                    <div>
                        <div class="service-history-top">
                            <strong><?php echo htmlspecialchars($row['jenis_pesan']); ?></strong>
                            <span class="<?php echo $row['status_pesan'] == 'Sudah Dibaca' ? 'status-read' : 'status-unread'; ?>">
                                <?php echo htmlspecialchars($row['status_pesan']); ?>
                            </span>
                        </div>

                        <p>
                            <?php 
                            echo htmlspecialchars(substr($row['isi_pesan'], 0, 120)); 
                            echo strlen($row['isi_pesan']) > 120 ? '...' : '';
                            ?>
                        </p>

                        <small>
                            <?php echo date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?>
                        </small>
                    </div>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-box">Belum ada pesan customer service.</div>
        <?php } ?>
    </div>
</section>

<script>
const serviceMessage = document.getElementById('serviceMessage');
const charCount = document.getElementById('charCount');

if (serviceMessage && charCount) {
    serviceMessage.addEventListener('input', function () {
        charCount.textContent = this.value.length;
    });
}
</script>

<?php penjual_page_end(); ?>