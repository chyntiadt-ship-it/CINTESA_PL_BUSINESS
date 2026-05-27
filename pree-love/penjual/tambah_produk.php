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

$kategori_query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

if (isset($_POST['simpan'])) {
    $id_kategori = (int) $_POST['id_kategori'];
    $nama_produk = mysqli_real_escape_string($koneksi, trim($_POST['nama_produk']));
    $deskripsi = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $kondisi_barang = mysqli_real_escape_string($koneksi, trim($_POST['kondisi_barang']));
    $ukuran = mysqli_real_escape_string($koneksi, trim($_POST['ukuran']));
    $merek = mysqli_real_escape_string($koneksi, trim($_POST['merek']));
    $alamat_produk = mysqli_real_escape_string($koneksi, trim($_POST['alamat_produk']));
    $harga = (int) $_POST['harga'];
    $keterangan_nego = mysqli_real_escape_string($koneksi, trim($_POST['keterangan_nego']));
    $status_produk = mysqli_real_escape_string($koneksi, trim($_POST['status_produk']));

    if (
        $id_kategori <= 0 ||
        $nama_produk == '' ||
        $deskripsi == '' ||
        $kondisi_barang == '' ||
        $alamat_produk == '' ||
        $harga <= 0 ||
        $keterangan_nego == '' ||
        $status_produk == ''
    ) {
        header("Location: tambah_produk.php?pesan=kosong");
        exit;
    }

    if (!isset($_FILES['foto_produk']) || empty($_FILES['foto_produk']['name'][0])) {
        header("Location: tambah_produk.php?pesan=foto_kosong");
        exit;
    }

    $jumlah_foto = count(array_filter($_FILES['foto_produk']['name']));

    if ($jumlah_foto > 3) {
        header("Location: tambah_produk.php?pesan=foto_maksimal");
        exit;
    }

    if (!is_dir('../uploads/produk')) {
        mkdir('../uploads/produk', 0777, true);
    }

    mysqli_begin_transaction($koneksi);

    try {
        $insert_produk = mysqli_query($koneksi, "
            INSERT INTO produk (
                id_user,
                id_kategori,
                nama_produk,
                deskripsi,
                kondisi_barang,
                ukuran,
                merek,
                alamat_produk,
                harga,
                keterangan_nego,
                status_produk,
                tanggal_upload
            ) VALUES (
                '$id_user',
                '$id_kategori',
                '$nama_produk',
                '$deskripsi',
                '$kondisi_barang',
                '$ukuran',
                '$merek',
                '$alamat_produk',
                '$harga',
                '$keterangan_nego',
                '$status_produk',
                NOW()
            )
        ");

        if (!$insert_produk) {
            throw new Exception('Produk gagal disimpan.');
        }

        $id_produk = mysqli_insert_id($koneksi);
        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];

        for ($i = 0; $i < count($_FILES['foto_produk']['name']); $i++) {
            if ($_FILES['foto_produk']['name'][$i] == '') {
                continue;
            }

            $nama_file = $_FILES['foto_produk']['name'][$i];
            $tmp_file = $_FILES['foto_produk']['tmp_name'][$i];
            $ukuran_file = $_FILES['foto_produk']['size'][$i];
            $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

            if (!in_array($ekstensi, $ekstensi_valid)) {
                throw new Exception('Format foto tidak valid.');
            }

            if ($ukuran_file > 2 * 1024 * 1024) {
                throw new Exception('Ukuran foto terlalu besar.');
            }

            $nama_baru = 'produk_' . $id_produk . '_' . time() . '_' . $i . '.' . $ekstensi;
            $lokasi_upload = '../uploads/produk/' . $nama_baru;

            if (!move_uploaded_file($tmp_file, $lokasi_upload)) {
                throw new Exception('Foto gagal diupload.');
            }

            $insert_foto = mysqli_query($koneksi, "
                INSERT INTO produk_foto (id_produk, foto)
                VALUES ('$id_produk', '$nama_baru')
            ");

            if (!$insert_foto) {
                throw new Exception('Foto gagal disimpan ke database.');
            }
        }

        mysqli_commit($koneksi);
        header("Location: produk.php?pesan=tambah_berhasil");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("Location: tambah_produk.php?pesan=gagal");
        exit;
    }
}

penjual_page_start('Tambah Produk', 'produk');
?>

<section class="page-hero add-product-hero">
    <h1>Tambah Produk</h1>
    <p>Lengkapi informasi produk pre-love yang ingin kamu jual di CINTESA.</p>
</section>

<?php if (isset($_GET['pesan'])) { ?>
    <?php if ($_GET['pesan'] == 'kosong') { ?>
        <div class="alert error">Mohon lengkapi semua data wajib.</div>
    <?php } elseif ($_GET['pesan'] == 'foto_kosong') { ?>
        <div class="alert error">Minimal unggah 1 foto produk.</div>
    <?php } elseif ($_GET['pesan'] == 'foto_maksimal') { ?>
        <div class="alert error">Foto produk maksimal 3 gambar.</div>
    <?php } elseif ($_GET['pesan'] == 'gagal') { ?>
        <div class="alert error">Produk gagal disimpan. Periksa kembali data produk.</div>
    <?php } ?>
<?php } ?>

<form method="POST" enctype="multipart/form-data" class="add-product-card">
    <div class="upload-panel">
        <label for="foto_produk" class="upload-box" id="uploadBox">
            <span class="upload-icon">＋</span>
            <strong>Upload Foto Produk</strong>
            <small>Klik atau drag foto ke sini. Maksimal 3 foto.</small>
            <em>Format JPG, PNG, JPEG, atau WEBP.</em>
        </label>

        <input 
            type="file" 
            name="foto_produk[]" 
            id="foto_produk" 
            accept="image/png, image/jpeg, image/jpg, image/webp"
            multiple
            hidden
        >

        <div class="preview-grid" id="previewGrid">
            <div class="preview-empty">Preview foto akan muncul di sini.</div>
        </div>
    </div>

    <div class="product-form-panel">
        <div class="form-grid">
            <div class="field-group">
                <label>Kategori Produk <span>*</span></label>
                <select name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php while ($kategori = mysqli_fetch_assoc($kategori_query)) { ?>
                        <option value="<?php echo $kategori['id_kategori']; ?>">
                            <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="field-group">
                <label>Nama Produk <span>*</span></label>
                <input type="text" name="nama_produk" placeholder="Contoh: Kemeja Oversize Putih" required>
            </div>

            <div class="field-group full">
                <label>Deskripsi Produk <span>*</span></label>
                <textarea name="deskripsi" rows="5" placeholder="Jelaskan kondisi, bahan, warna, dan detail produk." required></textarea>
            </div>

            <div class="field-group">
                <label>Kondisi Barang <span>*</span></label>
                <select name="kondisi_barang" required>
                    <option value="">Pilih Kondisi Barang</option>
                    <option value="Baru">Baru</option>
                    <option value="Sangat Baik">Sangat Baik</option>
                    <option value="Baik">Baik</option>
                    <option value="Cukup">Cukup</option>
                    <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                </select>
            </div>

            <div class="field-group">
                <label>Ukuran</label>
                <input type="text" name="ukuran" placeholder="Contoh: S / M / L / XL / 42">
            </div>

            <div class="field-group">
                <label>Merek</label>
                <input type="text" name="merek" placeholder="Contoh: Uniqlo, Samsung, Nike">
            </div>

            <div class="field-group">
                <label>Harga Produk <span>*</span></label>
                <input type="number" name="harga" min="1" placeholder="Contoh: 150000" required>
            </div>

            <div class="field-group">
                <label>Keterangan Nego <span>*</span></label>
                <select name="keterangan_nego" required>
                    <option value="">Pilih Keterangan</option>
                    <option value="Bisa Nego">Bisa Nego</option>
                    <option value="Tidak Bisa Nego">Tidak Bisa Nego</option>
                </select>
            </div>

            <div class="field-group">
                <label>Status Produk <span>*</span></label>
                <select name="status_produk" required>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Terjual">Terjual</option>
                </select>
            </div>

            <div class="field-group full">
                <label>Alamat Produk <span>*</span></label>
                <textarea name="alamat_produk" rows="4" placeholder="Masukkan alamat atau lokasi produk." required></textarea>
            </div>
        </div>

        <div class="form-actions add-actions">
            <a href="produk.php" class="btn btn-soft">Kembali</a>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Produk</button>
        </div>
    </div>
</form>

<script>
const fotoInput = document.getElementById('foto_produk');
const previewGrid = document.getElementById('previewGrid');
const uploadBox = document.getElementById('uploadBox');

function showPreview(files) {
    previewGrid.innerHTML = '';

    const fileList = Array.from(files);
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    if (fileList.length > 3) {
        alert('Foto produk maksimal 3 gambar.');
        fotoInput.value = '';
        previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
        return;
    }

    if (fileList.length === 0) {
        previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
        return;
    }

    for (const file of fileList) {
        if (!allowedTypes.includes(file.type)) {
            alert('Format foto harus JPG, JPEG, PNG, atau WEBP.');
            fotoInput.value = '';
            previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran setiap foto maksimal 2MB.');
            fotoInput.value = '';
            previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
            return;
        }
    }

    fileList.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function (e) {
            const item = document.createElement('div');
            item.className = 'preview-item';
            item.innerHTML = `
                <img src="${e.target.result}" alt="Preview Produk ${index + 1}">
                <span>Foto ${index + 1}</span>
            `;
            previewGrid.appendChild(item);
        };

        reader.readAsDataURL(file);
    });
}

fotoInput.addEventListener('change', function () {
    showPreview(this.files);
});

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

    const droppedFiles = e.dataTransfer.files;

    if (droppedFiles.length > 0) {
        const dataTransfer = new DataTransfer();

        Array.from(droppedFiles).forEach(file => {
            dataTransfer.items.add(file);
        });

        fotoInput.files = dataTransfer.files;
        showPreview(fotoInput.files);
    }
});
</script>

<?php penjual_page_end(); ?>