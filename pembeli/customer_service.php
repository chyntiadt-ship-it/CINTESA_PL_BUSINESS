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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Customer Service - Pree Love</title>
</head>
<body>

    <h2>Customer Service Pembeli</h2>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "berhasil") {
            echo "<p style='color:green;'>Pesan berhasil dikirim ke admin.</p>";
        } elseif ($_GET['pesan'] == "gagal") {
            echo "<p style='color:red;'>Pesan gagal dikirim.</p>";
        } elseif ($_GET['pesan'] == "kosong") {
            echo "<p style='color:red;'>Jenis pesan dan isi pesan wajib diisi.</p>";
        }
    }
    ?>

    <form action="proses_customer_service.php" method="POST">
        <label>Jenis Pesan</label><br>
        <select name="jenis_pesan" required>
            <option value="">-- Pilih Jenis Pesan --</option>
            <option value="Keluhan">Keluhan</option>
            <option value="Saran">Saran</option>
            <option value="Kritik">Kritik</option>
            <option value="Laporan">Laporan</option>
        </select><br><br>

        <label>Isi Pesan</label><br>
        <textarea name="isi_pesan" rows="6" placeholder="Tulis keluhan, kritik, saran, atau laporan kamu..." required></textarea><br><br>

        <button type="submit" name="kirim">Kirim ke Admin</button>
    </form>

</body>
</html>