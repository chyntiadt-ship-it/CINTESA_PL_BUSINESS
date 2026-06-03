<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manajemen_user.php");
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, $_GET['id']);

$cek = mysqli_query($koneksi, "
    SELECT * FROM user 
    WHERE id_user='$id_user'
");

if (mysqli_num_rows($cek) == 0) {
    echo "User tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($cek);

if ($data['role'] == 'admin') {
    echo "Admin tidak perlu diaktifkan.";
    exit;
}

if (isset($_POST['aktifkan'])) {

    mysqli_query($koneksi, "
        UPDATE user 
        SET status='aktif' 
        WHERE id_user='$id_user'
    ");

    header("Location: detail_user.php?id=$id_user");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktifkan User - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/aktifkan_user.css">
</head>
<body>

<main class="activate-page">

    <div class="popup-wrapper">

        <div class="popup-card">

            <div class="popup-icon">
                ✓
            </div>

            <h2>Aktifkan User?</h2>

            <p>
                Apakah anda yakin ingin mengaktifkan kembali user
                <strong>
                    <?php echo htmlspecialchars($data['nama_lengkap']); ?>
                </strong> ?
            </p>

            <div class="popup-action">

                <a href="detail_user.php?id=<?php echo $id_user; ?>" class="btn-cancel">
                    Batal
                </a>

                <form method="POST">
                    <button type="submit" name="aktifkan" class="btn-confirm">
                        Aktifkan
                    </button>
                </form>

            </div>

        </div>

    </div>

</main>

<footer class="footer-cintesa">
    <p>CINTESA PL BUSINESS - 2026</p>
</footer>

</body>
</html>