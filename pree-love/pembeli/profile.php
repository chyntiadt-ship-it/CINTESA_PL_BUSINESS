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

$id_user = (int) $_SESSION['id_user'];

mysqli_query($koneksi, "
    CREATE TABLE IF NOT EXISTS nomor_telepon_log (
        id_log INT(11) NOT NULL AUTO_INCREMENT,
        id_user INT(11) NOT NULL,
        nomor_lama VARCHAR(20) DEFAULT NULL,
        nomor_baru VARCHAR(20) DEFAULT NULL,
        tanggal_ubah DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id_log)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user' LIMIT 1");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: ../auth/logout.php");
    exit;
}

$cek_username_log = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM username_log
    WHERE id_user='$id_user'
    AND MONTH(tanggal_ubah)=MONTH(CURRENT_DATE())
    AND YEAR(tanggal_ubah)=YEAR(CURRENT_DATE())
");

$username_log = mysqli_fetch_assoc($cek_username_log);
$total_edit_username = (int) $username_log['total'];
$sisa_edit_username = max(0, 3 - $total_edit_username);

$cek_telepon_log = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM nomor_telepon_log
    WHERE id_user='$id_user'
");

$telepon_log = mysqli_fetch_assoc($cek_telepon_log);
$sudah_edit_telepon = ((int) $telepon_log['total']) > 0;

if (isset($_POST['update'])) {
    $username_baru = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $nomor_telepon_baru = mysqli_real_escape_string($koneksi, trim($_POST['nomor_telepon']));
    $foto_profile = $data['foto_profile'];

    $username_lama = $data['username'];
    $nomor_telepon_lama = $data['nomor_telepon'];

    $username_berubah = $username_baru !== $username_lama;
    $telepon_berubah = $nomor_telepon_baru !== $nomor_telepon_lama;
    $foto_berubah = !empty($_FILES['foto_profile']['name']);

    if ($username_baru === '') {
        header("Location: profile.php?pesan=username_kosong");
        exit;
    }

    if (!$username_berubah && !$telepon_berubah && !$foto_berubah) {
        header("Location: profile.php?pesan=tidak_ada_perubahan");
        exit;
    }

    if ($username_berubah) {
        if ($total_edit_username >= 3) {
            header("Location: profile.php?pesan=username_limit");
            exit;
        }

        $cek_username = mysqli_query($koneksi, "
            SELECT id_user 
            FROM user
            WHERE username='$username_baru'
            AND id_user != '$id_user'
            LIMIT 1
        ");

        if (mysqli_num_rows($cek_username) > 0) {
            header("Location: profile.php?pesan=username_ada");
            exit;
        }
    }

    if ($telepon_berubah && $sudah_edit_telepon) {
        header("Location: profile.php?pesan=telepon_limit");
        exit;
    }

    if ($foto_berubah) {
        $nama_file = $_FILES['foto_profile']['name'];
        $tmp_file = $_FILES['foto_profile']['tmp_name'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ekstensi, $ekstensi_valid)) {
            header("Location: profile.php?pesan=foto_invalid");
            exit;
        }

        if ($_FILES['foto_profile']['size'] > 2 * 1024 * 1024) {
            header("Location: profile.php?pesan=foto_besar");
            exit;
        }

        if (!is_dir('../uploads/profile')) {
            mkdir('../uploads/profile', 0777, true);
        }

        $nama_baru = 'profile_' . $id_user . '_' . time() . '.' . $ekstensi;
        $folder_upload = '../uploads/profile/' . $nama_baru;

        if (move_uploaded_file($tmp_file, $folder_upload)) {
            $foto_profile = $nama_baru;
        }
    }

    mysqli_begin_transaction($koneksi);

    try {
        if ($username_berubah) {
            mysqli_query($koneksi, "
                INSERT INTO username_log (id_user, username_lama, username_baru)
                VALUES ('$id_user', '$username_lama', '$username_baru')
            ");
        }

        if ($telepon_berubah) {
            mysqli_query($koneksi, "
                INSERT INTO nomor_telepon_log (id_user, nomor_lama, nomor_baru)
                VALUES ('$id_user', '$nomor_telepon_lama', '$nomor_telepon_baru')
            ");
        }

        $update = mysqli_query($koneksi, "
            UPDATE user SET
                username='$username_baru',
                nomor_telepon='$nomor_telepon_baru',
                foto_profile='$foto_profile'
            WHERE id_user='$id_user'
        ");

        if (!$update) {
            throw new Exception('Update gagal');
        }

        mysqli_commit($koneksi);

        $_SESSION['username'] = $username_baru;
        header("Location: profile.php?pesan=berhasil");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("Location: profile.php?pesan=gagal");
        exit;
    }
}

$foto_path = '../uploads/profile/' . $data['foto_profile'];
$ada_foto = !empty($data['foto_profile']) && file_exists($foto_path);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pembeli - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/profile_pembeli.css?v=1">
</head>
<body>

<main class="profile-page">

    <header class="profile-topbar">
    <a href="dashboard.php" class="back-btn">‹</a>

     <div class="brand-logo-wrap">
        <div class="brand-logo">
            CINTESA
        </div>
    </div>
</header>

    <section class="profile-page-head">
        <h2>Profil Pembeli</h2>
        <p>Kelola informasi akun pembelimu di CINTESA.</p>
    </section>

    <?php if (isset($_GET['pesan'])) { ?>
        <div class="alert <?php echo $_GET['pesan'] == 'berhasil' ? 'success' : 'error'; ?>">
            <?php
            if ($_GET['pesan'] == 'berhasil') echo "Profil berhasil diperbarui.";
            elseif ($_GET['pesan'] == 'username_limit') echo "Username hanya bisa diganti maksimal 3x dalam sebulan.";
            elseif ($_GET['pesan'] == 'telepon_limit') echo "Nomor telepon hanya bisa diganti 1x selamanya.";
            elseif ($_GET['pesan'] == 'username_ada') echo "Username sudah digunakan pengguna lain.";
            elseif ($_GET['pesan'] == 'foto_invalid') echo "Foto harus berformat JPG, JPEG, PNG, atau WEBP.";
            elseif ($_GET['pesan'] == 'foto_besar') echo "Ukuran foto maksimal 2MB.";
            elseif ($_GET['pesan'] == 'tidak_ada_perubahan') echo "Tidak ada perubahan yang disimpan.";
            else echo "Profil gagal diperbarui.";
            ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data" id="profileForm" class="profile-card">

        <div class="profile-photo-side">
            <div class="photo-wrapper">
                <?php if ($ada_foto) { ?>
                    <img src="<?php echo htmlspecialchars($foto_path); ?>" class="user-photo" id="previewPhoto">
                <?php } else { ?>
                    <div class="user-photo placeholder-photo" id="previewPlaceholder">
                        <?php echo strtoupper(substr($data['username'], 0, 1)); ?>
                    </div>
                    <img src="" class="user-photo hidden" id="previewPhoto">
                <?php } ?>

                <label for="foto_profile" class="edit-photo-btn">✎</label>
                <input type="file" name="foto_profile" id="foto_profile" accept="image/png, image/jpeg, image/jpg, image/webp" hidden>
            </div>

            <h2><?php echo htmlspecialchars($data['username']); ?></h2>
            <p><?php echo htmlspecialchars($data['email']); ?></p>
        </div>

        <div class="profile-form-side">
            <div class="edit-note">
                <strong>Catatan Edit</strong>
                <p>Username masih bisa diganti <?php echo $sisa_edit_username; ?>x bulan ini. Nomor telepon hanya bisa diganti 1x selamanya.</p>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" data-watch>
                </div>

                <div class="field-group">
                    <label>Nomor Telepon</label>
                    <input 
                        type="text" 
                        name="nomor_telepon" 
                        value="<?php echo htmlspecialchars($data['nomor_telepon']); ?>"
                        data-watch
                        <?php echo $sudah_edit_telepon ? 'readonly class="readonly-input"' : ''; ?>
                    >
                </div>

                <div class="field-group locked-field">
                    <label>Nama Lengkap</label>
                    <input type="text" value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" readonly>
                    <span>🚫</span>
                </div>

                <div class="field-group locked-field">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($data['email']); ?>" readonly>
                    <span>🚫</span>
                </div>

                <div class="field-group locked-field">
                    <label>Role</label>
                    <input type="text" value="<?php echo htmlspecialchars(ucfirst($data['role'])); ?>" readonly>
                    <span>🚫</span>
                </div>

                <div class="field-group locked-field">
                    <label>Tanggal Bergabung</label>
                    <input type="text" value="<?php echo date('d-m-Y H:i', strtotime($data['tanggal_bergabung'])); ?>" readonly>
                    <span>🚫</span>
                </div>
            </div>

            <div class="save-area" id="saveArea">
                <button type="submit" name="update" class="save-btn">Simpan Perubahan</button>
            </div>
        </div>

    </form>

</main>

<script src="../assets/js/profile_pembeli.js?v=1"></script>
</body>
</html>
