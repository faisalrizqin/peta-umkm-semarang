<?php
// pages/kategori/tambah.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

$error = '';
$success = '';

// Proses form
if (isset($_POST['submit'])) {
    $nama_kategori = clean_input($_POST['nama_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);

    // Validasi
    if (empty($nama_kategori)) {
        $error = "Nama kategori harus diisi!";
    } else {
        // Cek duplikasi
        $check = mysqli_query($conn, "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Kategori dengan nama tersebut sudah ada!";
        } else {
            // Insert data
            $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";

            if (mysqli_query($conn, $query)) {
                $success = "Kategori berhasil ditambahkan!";
                // Reset form
                $_POST = array();
            } else {
                $error = "Gagal menambahkan kategori: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - Peta UMKM Semarang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include '../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include '../../includes/header.php'; ?>

        <!-- Content Area -->
        <div class="content">
            <div class="page-header">
                <h2>Tambah Kategori UMKM</h2>
                <p>Tambahkan kategori jenis usaha UMKM baru</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Form Tambah Kategori</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori <span class="required">*</span></label>
                            <input type="text" id="nama_kategori" name="nama_kategori" class="form-control"
                                placeholder="Contoh: Kuliner, Fashion, Kerajinan"
                                value="<?php echo isset($_POST['nama_kategori']) ? $_POST['nama_kategori'] : ''; ?>"
                                required>
                            <small class="form-text">Masukkan nama kategori usaha</small>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"
                                placeholder="Masukkan deskripsi kategori (opsional)"><?php echo isset($_POST['deskripsi']) ? $_POST['deskripsi'] : ''; ?></textarea>
                            <small class="form-text">Deskripsi singkat tentang kategori ini</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="submit" class="btn btn-primary">
                                💾 Simpan Kategori
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                ↩️ Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>ℹ️ Informasi</h3>
                </div>
                <div class="card-body">
                    <p><strong>Contoh Kategori UMKM:</strong></p>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>🍜 Kuliner (makanan dan minuman)</li>
                        <li>👕 Fashion (pakaian dan aksesoris)</li>
                        <li>🎨 Kerajinan (handmade dan seni)</li>
                        <li>🛠️ Jasa (service dan repair)</li>
                        <li>🏠 Furniture (mebel dan interior)</li>
                        <li>🌾 Pertanian (olahan hasil tani)</li>
                        <li>💄 Kecantikan (kosmetik dan perawatan)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo isset($root_path) ? $root_path : '../../'; ?>assets/js/script.js"></script>
</body>

</html>