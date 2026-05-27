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

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'] ?? 'Penjual';

$total_produk = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM produk 
    WHERE id_user='$id_user' 
    AND status_produk != 'Dihapus'
");
$data_produk = mysqli_fetch_assoc($total_produk);

$total_tersedia = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM produk 
    WHERE id_user='$id_user' 
    AND status_produk='Tersedia'
");
$data_tersedia = mysqli_fetch_assoc($total_tersedia);

$total_terjual = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM produk 
    WHERE id_user='$id_user' 
    AND status_produk='Terjual'
");
$data_terjual = mysqli_fetch_assoc($total_terjual);

$total_pesan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM chat 
    WHERE id_penjual='$id_user'
");
$data_pesan = mysqli_fetch_assoc($total_pesan);

penjual_page_start('Dashboard Penjual', 'dashboard');
?>

<section class="hero-dashboard">
    <h1>Halo, <?php echo htmlspecialchars($username); ?></h1>
    <p>Kelola produk dan pantau aktivitas tokomu di CINTESA.</p>
</section>

<section class="stats-grid dashboard-only">
    <article class="stat-card">
        <span>▦</span>
        <h3>Total Produk</h3>
        <strong><?php echo $data_produk['total']; ?></strong>
    </article>

    <article class="stat-card">
        <span>✓</span>
        <h3>Produk Tersedia</h3>
        <strong><?php echo $data_tersedia['total']; ?></strong>
    </article>

    <article class="stat-card">
        <span>◎</span>
        <h3>Produk Terjual</h3>
        <strong><?php echo $data_terjual['total']; ?></strong>
    </article>

    <article class="stat-card">
        <span>▣</span>
        <h3>Ruang Chat</h3>
        <strong><?php echo $data_pesan['total']; ?></strong>
    </article>
</section>

<?php penjual_page_end(); ?>