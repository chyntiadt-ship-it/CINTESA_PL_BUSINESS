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

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id_produk = (int) $_GET['id'];

function selected_option($value, $current) {
    return $value == $current ? 'selected' : '';
}

$produk_query = mysqli_query($koneksi, "
    SELECT *
    FROM produk
    WHERE id_produk = '$id_produk'
    AND id_user = '$id_user'
    LIMIT 1
");

if (mysqli_num_rows($produk_query) == 0) {
    penjual_page_start('Produk Tidak Ditemukan', 'produk');
    echo '<div class="alert error">Produk tidak ditemukan atau bukan milik akun ini.</div>';
    echo '<a href="produk.php" class="btn btn-primary">Kembali ke Produk</a>';
    penjual_page_end();
    exit;
}

$produk = mysqli_fetch_assoc($produk_query);

$kategori_query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$foto_query = mysqli_query($koneksi, "
    SELECT *
    FROM produk_foto
    WHERE id_produk = '$id_produk'
");

if (isset($_POST['update'])) {
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
        header("Location: edit_produk.php?id=$id_produk&pesan=kosong");
        exit;
    }

    $ada_foto_baru = isset($_FILES['foto_produk']) && !empty($_FILES['foto_produk']['name'][0]);
    $jumlah_foto = $ada_foto_baru ? count(array_filter($_FILES['foto_produk']['name'])) : 0;

    if ($jumlah_foto > 3) {
        header("Location: edit_produk.php?id=$id_produk&pesan=foto_maksimal");
        exit;
    }

    if (!is_dir('../uploads/produk')) {
        mkdir('../uploads/produk', 0777, true);
    }

    mysqli_begin_transaction($koneksi);

    try {
        $update_produk = mysqli_query($koneksi, "
            UPDATE produk SET
                id_kategori = '$id_kategori',
                nama_produk = '$nama_produk',
                deskripsi = '$deskripsi',
                kondisi_barang = '$kondisi_barang',
                ukuran = '$ukuran',
                merek = '$merek',
                alamat_produk = '$alamat_produk',
                harga = '$harga',
                keterangan_nego = '$keterangan_nego',
                status_produk = '$status_produk'
            WHERE id_produk = '$id_produk'
            AND id_user = '$id_user'
        ");

        if (!$update_produk) {
            throw new Exception('Produk gagal diperbarui: ' . mysqli_error($koneksi));
        }

        if ($ada_foto_baru) {
            $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];

            $foto_lama = mysqli_query($koneksi, "
                SELECT foto
                FROM produk_foto
                WHERE id_produk = '$id_produk'
            ");

            while ($foto = mysqli_fetch_assoc($foto_lama)) {
                $path_foto = '../uploads/produk/' . $foto['foto'];

                if (!empty($foto['foto']) && file_exists($path_foto)) {
                    unlink($path_foto);
                }
            }

            mysqli_query($koneksi, "
                DELETE FROM produk_foto
                WHERE id_produk = '$id_produk'
            ");

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
                    throw new Exception('Foto gagal disimpan.');
                }
            }
        }

        mysqli_commit($koneksi);
        header("Location: produk.php?pesan=edit_berhasil");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("Location: edit_produk.php?id=$id_produk&pesan=gagal");
        exit;
    }
}

penjual_page_start('Edit Produk', 'produk');
?>

<section class="page-hero add-product-hero">
    <h1>Edit Produk</h1>
    <p>Perbarui informasi produk yang kamu jual di CINTESA.</p>
</section>

<?php if (isset($_GET['pesan'])) { ?>
    <?php if ($_GET['pesan'] == 'kosong') { ?>
        <div class="alert error">Mohon lengkapi semua data wajib.</div>
    <?php } elseif ($_GET['pesan'] == 'foto_maksimal') { ?>
        <div class="alert error">Foto produk maksimal 3 gambar.</div>
    <?php } elseif ($_GET['pesan'] == 'gagal') { ?>
        <div class="alert error">Produk gagal diperbarui. Periksa kembali data produk.</div>
    <?php } ?>
<?php } ?>

<form method="POST" enctype="multipart/form-data" class="add-product-card edit-product-card">
    <div class="upload-panel">
        <label for="foto_produk" class="upload-box" id="uploadBoxEdit">
            <span class="upload-icon">＋</span>
            <strong>Ganti Foto Produk</strong>
            <small>Klik atau drag foto ke sini. Maksimal 3 foto.</small>
            <em>Kosongkan jika tidak ingin mengganti foto.</em>
        </label>

        <input 
            type="file" 
            name="foto_produk[]" 
            id="foto_produk" 
            accept="image/png, image/jpeg, image/jpg, image/webp"
            multiple
            hidden
        >

        <div class="current-photo-title">Foto Produk Saat Ini</div>

        <div class="preview-grid" id="previewGrid">
            <?php if (mysqli_num_rows($foto_query) > 0) { ?>
                <?php while ($foto = mysqli_fetch_assoc($foto_query)) { ?>
                    <div class="preview-item">
                        <img src="../uploads/produk/<?php echo htmlspecialchars($foto['foto']); ?>" alt="Foto Produk">
                        <span>Foto lama</span>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="preview-empty">Belum ada foto produk.</div>
            <?php } ?>
        </div>
    </div>

    <div class="product-form-panel">
        <div class="form-grid">
            <div class="field-group">
                <label>Kategori Produk <span>*</span></label>
                <select name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php while ($kategori = mysqli_fetch_assoc($kategori_query)) { ?>
                        <option 
                            value="<?php echo $kategori['id_kategori']; ?>"
                            <?php echo selected_option($kategori['id_kategori'], $produk['id_kategori']); ?>
                        >
                            <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="field-group">
                <label>Nama Produk <span>*</span></label>
                <input 
                    type="text" 
                    name="nama_produk" 
                    value="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
                    required
                >
            </div>

            <div class="field-group full">
                <label>Deskripsi Produk <span>*</span></label>
                <textarea name="deskripsi" rows="5" required><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
            </div>

            <div class="field-group">
                <label>Kondisi Barang <span>*</span></label>
                <select name="kondisi_barang" required>
                    <option value="">Pilih Kondisi Barang</option>
                    <option value="Baru" <?php echo selected_option('Baru', $produk['kondisi_barang']); ?>>Baru</option>
                    <option value="Sangat Baik" <?php echo selected_option('Sangat Baik', $produk['kondisi_barang']); ?>>Sangat Baik</option>
                    <option value="Baik" <?php echo selected_option('Baik', $produk['kondisi_barang']); ?>>Baik</option>
                    <option value="Cukup" <?php echo selected_option('Cukup', $produk['kondisi_barang']); ?>>Cukup</option>
                    <option value="Perlu Perbaikan" <?php echo selected_option('Perlu Perbaikan', $produk['kondisi_barang']); ?>>Perlu Perbaikan</option>
                </select>
            </div>

            <div class="field-group">
                <label>Ukuran</label>
                <input 
                    type="text" 
                    name="ukuran" 
                    value="<?php echo htmlspecialchars($produk['ukuran']); ?>"
                    placeholder="Contoh: S / M / L / XL / 42"
                >
            </div>

            <div class="field-group">
                <label>Merek</label>
                <input 
                    type="text" 
                    name="merek" 
                    value="<?php echo htmlspecialchars($produk['merek']); ?>"
                    placeholder="Contoh: Uniqlo, Samsung, Nike"
                >
            </div>

            <div class="field-group">
                <label>Harga Produk <span>*</span></label>
                <input 
                    type="number" 
                    name="harga" 
                    min="1"
                    value="<?php echo htmlspecialchars($produk['harga']); ?>"
                    required
                >
            </div>

            <div class="field-group">
                <label>Keterangan Nego <span>*</span></label>
                <select name="keterangan_nego" required>
                    <option value="">Pilih Keterangan</option>
                    <option value="Bisa Nego" <?php echo selected_option('Bisa Nego', $produk['keterangan_nego']); ?>>Bisa Nego</option>
                    <option value="Tidak Bisa Nego" <?php echo selected_option('Tidak Bisa Nego', $produk['keterangan_nego']); ?>>Tidak Bisa Nego</option>
                </select>
            </div>

            <div class="field-group">
                <label>Status Produk <span>*</span></label>
                <select name="status_produk" required>
                    <option value="Tersedia" <?php echo selected_option('Tersedia', $produk['status_produk']); ?>>Tersedia</option>
                    <option value="Terjual" <?php echo selected_option('Terjual', $produk['status_produk']); ?>>Terjual</option>
                </select>
            </div>

            <div class="field-group full">
                <label>Alamat Produk <span>*</span></label>
                <textarea name="alamat_produk" rows="4" required><?php echo htmlspecialchars($produk['alamat_produk']); ?></textarea>
            </div>
        </div>

        <div class="form-actions add-actions">
            <a href="produk.php" class="btn btn-soft">Kembali</a>
            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </div>
</form>

<script>
const fotoInput = document.getElementById('foto_produk');
const previewGrid = document.getElementById('previewGrid');
const uploadBoxEdit = document.getElementById('uploadBoxEdit');

function showEditPreview(files) {
    previewGrid.innerHTML = '';

    const fileList = Array.from(files);
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    if (fileList.length > 3) {
        alert('Foto produk maksimal 3 gambar.');
        fotoInput.value = '';
        return;
    }

    if (fileList.length === 0) {
        return;
    }

    for (const file of fileList) {
        if (!allowedTypes.includes(file.type)) {
            alert('Format foto harus JPG, JPEG, PNG, atau WEBP.');
            fotoInput.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran setiap foto maksimal 2MB.');
            fotoInput.value = '';
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
                <span>Foto baru ${index + 1}</span>
            `;
            previewGrid.appendChild(item);
        };

        reader.readAsDataURL(file);
    });
}

fotoInput.addEventListener('change', function () {
    showEditPreview(this.files);
});

uploadBoxEdit.addEventListener('dragover', function (e) {
    e.preventDefault();
    uploadBoxEdit.classList.add('drag-active');
});

uploadBoxEdit.addEventListener('dragleave', function () {
    uploadBoxEdit.classList.remove('drag-active');
});

uploadBoxEdit.addEventListener('drop', function (e) {
    e.preventDefault();
    uploadBoxEdit.classList.remove('drag-active');

    const droppedFiles = e.dataTransfer.files;

    if (droppedFiles.length > 0) {
        const dataTransfer = new DataTransfer();

        Array.from(droppedFiles).forEach(file => {
            dataTransfer.items.add(file);
        });

        fotoInput.files = dataTransfer.files;
        showEditPreview(fotoInput.files);
    }
});
</script>

<?php penjual_page_end(); ?>