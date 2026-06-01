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

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'");

if (mysqli_num_rows($query) == 0) {
    echo "User tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

if ($data['role'] == 'admin') {
    echo "Admin tidak boleh dinonaktifkan.";
    exit;
}

if (isset($_POST['nonaktifkan'])) {

    mysqli_query($koneksi, "UPDATE user 
        SET status='nonaktif' 
        WHERE id_user='$id_user'
    ");

    header("Location: detail_user.php?id=$id_user&pesan=nonaktif");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nonaktifkan User - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/nonaktifkan_user.css">
</head>
<body>

<div class="confirm-wrapper">

    <div class="confirm-modal">

        <div class="confirm-icon">
            !
        </div>

        <h2>Nonaktifkan User</h2>

        <p>
            Apakah kamu yakin ingin menonaktifkan user
            <span class="confirm-user">
                <?php echo htmlspecialchars($data['username']); ?>
            </span> ?
        </p>

        <form method="POST">

            <div class="confirm-actions">

                <a href="detail_user.php?id=<?php echo $id_user; ?>" class="cancel-btn">
                    Batal
                </a>

                <button type="submit" name="nonaktifkan" class="danger-btn">
                    Ya, Nonaktifkan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>