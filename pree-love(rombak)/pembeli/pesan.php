<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pembeli') {
    header("Location: ../auth/login.php");
    exit;
}

$id_pembeli = (int) $_SESSION['id_user'];

/*
    Membuat/membuka ruang pesan dari detail produk.
    URL aman:
    pesan.php?id_produk=...
    pesan.php?id=...
*/

if (isset($_GET['id_produk'])) {
    $id_produk = (int) $_GET['id_produk'];

    $query_produk = mysqli_query($koneksi, "
        SELECT * FROM produk
        WHERE id_produk='$id_produk'
        LIMIT 1
    ");

    if (!$query_produk || mysqli_num_rows($query_produk) == 0) {
        echo "Produk tidak ditemukan.";
        exit;
    }

    $produk = mysqli_fetch_assoc($query_produk);
    $id_penjual = (int) $produk['id_user'];

    $cek_room = mysqli_query($koneksi, "
        SELECT * FROM chat
        WHERE id_produk='$id_produk'
        AND id_pembeli='$id_pembeli'
        AND id_penjual='$id_penjual'
        LIMIT 1
    ");

    if ($cek_room && mysqli_num_rows($cek_room) > 0) {
        $room = mysqli_fetch_assoc($cek_room);
        $id_room = (int) $room['id_chat'];
    } else {
        mysqli_query($koneksi, "
            INSERT INTO chat (id_produk, id_pembeli, id_penjual)
            VALUES ('$id_produk', '$id_pembeli', '$id_penjual')
        ");

        $id_room = mysqli_insert_id($koneksi);
    }

    header("Location: pesan.php?id=$id_room");
    exit;
}

$id_room = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_room <= 0) {
    header("Location: dashboard.php");
    exit;
}

$query_room = mysqli_query($koneksi, "
    SELECT 
        chat.*,
        produk.nama_produk,
        produk.harga,
        produk.status_produk,
        user.username AS username_penjual,
        user.nama_lengkap AS nama_penjual
    FROM chat
    JOIN produk ON chat.id_produk = produk.id_produk
    JOIN user ON chat.id_penjual = user.id_user
    WHERE chat.id_chat='$id_room'
    AND chat.id_pembeli='$id_pembeli'
    LIMIT 1
");

if (!$query_room || mysqli_num_rows($query_room) == 0) {
    echo "Pesan tidak ditemukan.";
    exit;
}

$data_room = mysqli_fetch_assoc($query_room);

/*
    Proses kirim pesan langsung di halaman ini.
*/
if (isset($_POST['kirim'])) {
    $isi_pesan = mysqli_real_escape_string($koneksi, trim($_POST['isi_pesan'] ?? ''));
    $foto_pesan = null;
    $file_pesan = null;

    if (isset($_FILES['gambar_kamera']) && $_FILES['gambar_kamera']['error'] === UPLOAD_ERR_OK) {
        $file_pesan = $_FILES['gambar_kamera'];
    } elseif (isset($_FILES['gambar_pesan']) && $_FILES['gambar_pesan']['error'] === UPLOAD_ERR_OK) {
        $file_pesan = $_FILES['gambar_pesan'];
    }

    if ($file_pesan !== null) {
        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];
        $nama_file = $file_pesan['name'];
        $tmp_file = $file_pesan['tmp_name'];
        $ukuran_file = $file_pesan['size'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $ekstensi_valid)) {
            header("Location: pesan.php?id=$id_room&pesan=foto_invalid");
            exit;
        }

        if ($ukuran_file > 5 * 1024 * 1024) {
            header("Location: pesan.php?id=$id_room&pesan=foto_besar");
            exit;
        }

        if (!is_dir('../uploads/pesan')) {
            mkdir('../uploads/pesan', 0777, true);
        }

        $foto_pesan = 'pesan_' . $id_room . '_' . time() . '.' . $ekstensi;
        $lokasi_upload = '../uploads/pesan/' . $foto_pesan;

        if (!move_uploaded_file($tmp_file, $lokasi_upload)) {
            header("Location: pesan.php?id=$id_room&pesan=foto_gagal");
            exit;
        }
    }

    if ($isi_pesan != "" || $foto_pesan != null) {
        if ($isi_pesan != "" && $foto_pesan != null) {
            $tipe_pesan = 'mixed';
        } elseif ($foto_pesan != null) {
            $tipe_pesan = 'image';
        } else {
            $tipe_pesan = 'text';
        }

        mysqli_query($koneksi, "
            INSERT INTO pesan (id_chat, id_pengirim, isi_pesan, foto_pesan, tipe_pesan)
            VALUES ('$id_room', '$id_pembeli', '$isi_pesan', '$foto_pesan', '$tipe_pesan')
        ");
    }

    header("Location: pesan.php?id=$id_room");
    exit;
}

$query_pesan = mysqli_query($koneksi, "
    SELECT 
        pesan.*,
        user.username
    FROM pesan
    JOIN user ON pesan.id_pengirim = user.id_user
    WHERE pesan.id_chat='$id_room'
    ORDER BY pesan.tanggal_pesan ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Pembeli - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/buyer.css?v=2">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="buyer-page pesan-page">

<header class="role-navbar">
    <a href="dashboard.php" class="icon-button back-button" title="Kembali">
        <img 
            src="../assets/icons/penjual/back-arrow.png" 
            class="back-icon" 
            alt="Kembali"
            >
    </a>

    <a href="dashboard.php" class="brand-logo">CINTESA</a>

    <a href="../auth/logout.php" class="icon-button" title="Logout">
        <img src="../assets/icons/logout.png" alt="Logout">
    </a>
</header>

<main class="role-main message-main">
    <section class="message-card">
        <header class="message-header">
            <div class="seller-avatar">
                <?php echo strtoupper(substr($data_room['username_penjual'], 0, 1)); ?>
            </div>

            <div class="message-info">
                <h1><?php echo htmlspecialchars($data_room['nama_penjual'] ?: $data_room['username_penjual']); ?></h1>
                <p>@<?php echo htmlspecialchars($data_room['username_penjual']); ?></p>
                <span>
                    Produk: <?php echo htmlspecialchars($data_room['nama_produk']); ?>
                </span>
            </div>

            <div class="message-price">
                <small>Harga</small>
                <strong>Rp<?php echo number_format($data_room['harga'], 0, ',', '.'); ?></strong>
            </div>
        </header>

        <section class="security-note">
            <strong>Catatan Keamanan</strong>
            <p>
                Gunakan fitur pesan CINTESA untuk membahas stok, kondisi barang, nego harga, dan detail produk.
                Hindari transaksi di luar platform.
            </p>
        </section>

        <section class="message-body" id="pesanBody">
            <?php if ($query_pesan && mysqli_num_rows($query_pesan) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($query_pesan)) { 
                    $is_me = ((int) $row['id_pengirim'] === $id_pembeli);
                ?>
                    <div class="message-row <?php echo $is_me ? 'me' : 'seller'; ?>">
                        <div class="message-bubble">
                            <?php if (!empty($row['foto_pesan'])) { ?>
                                <img 
                                    src="../uploads/pesan/<?php echo htmlspecialchars($row['foto_pesan']); ?>" 
                                    class="message-image"
                                    alt="Foto pesan"
                                >
                            <?php } ?>

                            <?php if (!empty($row['isi_pesan'])) { ?>
                                <p><?php echo nl2br(htmlspecialchars($row['isi_pesan'])); ?></p>
                            <?php } ?>

                            <small><?php echo date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?></small>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-box">
                    Belum ada pesan. Mulai tanyakan produk kepada penjual.
                </div>
            <?php } ?>
        </section>

        <form method="POST" enctype="multipart/form-data" class="message-input-form" id="pesanForm">
            <div class="attach-wrapper">
                <button type="button" class="attach-button" id="attachButton">＋</button>

                <div class="attach-menu" id="attachMenu">
                    <label for="galleryInput">🖼 Pilih dari Galeri</label>
                    <label for="cameraInput">📷 Ambil Foto</label>
                </div>

                <input 
                    type="file" 
                    name="gambar_pesan" 
                    id="galleryInput" 
                    accept="image/*" 
                    hidden
                >

                <input 
                    type="file" 
                    name="gambar_kamera" 
                    id="cameraInput" 
                    accept="image/*" 
                    capture="environment"
                    hidden
                >
            </div>

            <textarea 
                name="isi_pesan" 
                id="replyTextarea" 
                rows="1" 
                placeholder="Tulis pesan..."
            ></textarea>

            <button type="submit" name="kirim" class="send-button">➤</button>

            <div class="file-preview" id="filePreview">
                <span id="fileName"></span>
                <button type="button" id="removeFile">×</button>
            </div>
        </form>
    </section>
</main>

<script>
const pesanBody = document.getElementById('pesanBody');
if (pesanBody) {
    pesanBody.scrollTop = pesanBody.scrollHeight;
}

const attachButton = document.getElementById('attachButton');
const attachMenu = document.getElementById('attachMenu');
const galleryInput = document.getElementById('galleryInput');
const cameraInput = document.getElementById('cameraInput');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');
const removeFile = document.getElementById('removeFile');
const replyTextarea = document.getElementById('replyTextarea');
const pesanForm = document.getElementById('pesanForm');

if (attachButton && attachMenu) {
    attachButton.addEventListener('click', function (e) {
        e.stopPropagation();
        attachMenu.classList.toggle('show');
    });

    document.addEventListener('click', function (e) {
        if (!attachMenu.contains(e.target) && !attachButton.contains(e.target)) {
            attachMenu.classList.remove('show');
        }
    });
}

function showSelectedFile(file) {
    if (!file) return;

    fileName.textContent = file.name;
    filePreview.classList.add('show');
    attachMenu.classList.remove('show');
}

function clearFile() {
    galleryInput.value = '';
    cameraInput.value = '';
    fileName.textContent = '';
    filePreview.classList.remove('show');
}

if (galleryInput) {
    galleryInput.addEventListener('change', function () {
        cameraInput.value = '';
        showSelectedFile(this.files[0]);
    });
}

if (cameraInput) {
    cameraInput.addEventListener('change', function () {
        galleryInput.value = '';
        showSelectedFile(this.files[0]);
    });
}

if (removeFile) {
    removeFile.addEventListener('click', clearFile);
}

if (replyTextarea && pesanForm) {
    replyTextarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();

            const adaPesan = replyTextarea.value.trim() !== '';
            const adaFile = galleryInput.files.length > 0 || cameraInput.files.length > 0;

            if (adaPesan || adaFile) {
                pesanForm.submit();
            }
        }
    });
}
</script>

</body>
</html>