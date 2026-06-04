<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

$id_penjual = (int) $_SESSION['id_user'];
$error = "";

$query_kategori = mysqli_query($koneksi, "
    SELECT * FROM kategori
    ORDER BY nama_kategori ASC
");

if (isset($_POST['posting'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, trim($_POST['nama_produk']));
    $id_kategori = (int) $_POST['id_kategori'];
    $harga = (int) $_POST['harga'];
    $status_produk = mysqli_real_escape_string($koneksi, trim($_POST['status_produk']));
    $deskripsi = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));

    if ($nama_produk == "" || $id_kategori <= 0 || $harga <= 0 || $status_produk == "" || $deskripsi == "") {
        $error = "Semua data produk wajib diisi.";
    } else {
        $insert_produk = mysqli_query($koneksi, "
            INSERT INTO produk (id_user, id_kategori, nama_produk, harga, status_produk, deskripsi)
            VALUES ('$id_penjual', '$id_kategori', '$nama_produk', '$harga', '$status_produk', '$deskripsi')
        ");

        if ($insert_produk) {
            $id_produk = mysqli_insert_id($koneksi);

            if (!is_dir('../uploads/produk')) {
                mkdir('../uploads/produk', 0777, true);
            }

            if (!empty($_FILES['foto_produk']['name'][0])) {
                $jumlah_foto = count($_FILES['foto_produk']['name']);
                $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];

                for ($i = 0; $i < $jumlah_foto; $i++) {
                    if ($_FILES['foto_produk']['error'][$i] === UPLOAD_ERR_OK) {
                        $nama_file = $_FILES['foto_produk']['name'][$i];
                        $tmp_file = $_FILES['foto_produk']['tmp_name'][$i];
                        $ukuran_file = $_FILES['foto_produk']['size'][$i];
                        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

                        if (in_array($ekstensi, $ekstensi_valid) && $ukuran_file <= 2 * 1024 * 1024) {
                            $nama_baru = 'produk_' . $id_produk . '_' . time() . '_' . $i . '.' . $ekstensi;
                            $lokasi = '../uploads/produk/' . $nama_baru;

                            if (move_uploaded_file($tmp_file, $lokasi)) {
                                mysqli_query($koneksi, "
                                    INSERT INTO produk_foto (id_produk, foto)
                                    VALUES ('$id_produk', '$nama_baru')
                                ");
                            }
                        }
                    }
                }
            }

            header("Location: dashboard.php?pesan=produk_ditambah");
            exit;
        } else {
            $error = "Produk gagal ditambahkan: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/seller.css?v=2">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="seller-page">

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

<main class="role-main">
    <section class="page-heading center">
        <h1>Tambah Produk</h1>
        <p>Isi data produk yang ingin kamu jual di CINTESA.</p>
    </section>

    <?php if ($error != "") { ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <section class="product-form-card">
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <div class="upload-panel">
                <label for="foto_produk" class="upload-box" id="uploadBox">
                    <div class="upload-icon">＋</div>
                    <strong>Upload Foto Produk</strong>
                    <small>Pilih maksimal beberapa foto produk. Format: JPG, PNG, WEBP.</small>
                </label>

                <input 
                    type="file" 
                    name="foto_produk[]" 
                    id="foto_produk" 
                    accept="image/*" 
                    multiple 
                    hidden
                >

                <div class="preview-grid" id="previewGrid">
                    <div class="preview-empty">Preview foto akan muncul di sini.</div>
                </div>
            </div>

            <div class="form-panel">
                <div class="form-grid">
                    <div class="field-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" required>
                    </div>

                    <div class="field-group">
                        <label>Kategori</label>
                        <select name="id_kategori" required>
                            <option value="">Pilih kategori</option>
                            <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>
                                <option value="<?php echo $kategori['id_kategori']; ?>">
                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Harga</label>
                        <input type="number" name="harga" min="1" required>
                    </div>

                    <div class="field-group">
                        <label>Status Produk</label>
                        <select name="status_produk" required>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Terjual">Terjual</option>
                        </select>
                    </div>

                    <div class="field-group full">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="7" required></textarea>
                    </div>
                </div>

                <div class="form-actions end">
                    <button type="submit" name="posting" class="main-button">
                        Posting
                    </button>
                </div>
            </div>
        </form>
    </section>
</main>

<script>
const inputFoto = document.getElementById('foto_produk');
const previewGrid = document.getElementById('previewGrid');
const uploadBox = document.getElementById('uploadBox');

if (inputFoto && previewGrid) {
    inputFoto.addEventListener('change', function () {
        previewGrid.innerHTML = '';

        const files = Array.from(this.files);

        if (files.length === 0) {
            previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
            return;
        }

        files.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <span>Foto ${index + 1}</span>
                `;
                previewGrid.appendChild(item);
            };

            reader.readAsDataURL(file);
        });
    });
}

if (uploadBox && inputFoto) {
    uploadBox.addEventListener('dragover', function (e) {
        e.preventDefault();
        uploadBox.classList.add('drag-active');
    });

    uploadBox.addEventListener('dragleave', function () {
        uploadBox.classList.remove('drag-active');
    });

    uploadBox.addEventListener('drop', function (e) {
        e.preventDefault();
        uploadBox.classList.remove('drag-active');

        const dataTransfer = new DataTransfer();

        Array.from(e.dataTransfer.files).forEach(file => {
            dataTransfer.items.add(file);
        });

        inputFoto.files = dataTransfer.files;
        inputFoto.dispatchEvent(new Event('change'));
    });
}
</script>

</body>
</html>