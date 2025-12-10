<?php
// pages/kategori/edit.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

$error = '';
$success = '';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = clean_input($_GET['id']);

// Ambil data kategori
$query = "SELECT * FROM kategori WHERE id_kategori = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($result);

// Proses form
if (isset($_POST['submit'])) {
    $nama_kategori = clean_input($_POST['nama_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);

    // Validasi
    if (empty($nama_kategori)) {
        $error = "Nama kategori harus diisi!";
    } else {
        // Cek duplikasi (kecuali data sendiri)
        $check = mysqli_query($conn, "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori' AND id_kategori != '$id'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Kategori dengan nama tersebut sudah ada!";
        } else {
            // Update data
            $query = "UPDATE kategori SET 
                      nama_kategori = '$nama_kategori',
                      deskripsi = '$deskripsi'
                      WHERE id_kategori = '$id'";

            if (mysqli_query($conn, $query)) {
                $success = "Kategori berhasil diupdate!";
                // Refresh data
                $data['nama_kategori'] = $nama_kategori;
                $data['deskripsi'] = $deskripsi;
            } else {
                $error = "Gagal mengupdate kategori: " . mysqli_error($conn);
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
    <title>Edit Kategori - Peta UMKM Semarang</title>
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
                <h2>Edit Kategori UMKM</h2>
                <p>Ubah data kategori jenis usaha UMKM</p>
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
                    <h3>Form Edit Kategori</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori <span class="required">*</span></label>
                            <input type="text" id="nama_kategori" name="nama_kategori" class="form-control"
                                value="<?php echo $data['nama_kategori']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control"
                                rows="4"><?php echo $data['deskripsi']; ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="submit" class="btn btn-primary">
                                💾 Update Kategori
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                ↩️ Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo isset($root_path) ? $root_path : '../../'; ?>assets/js/script.js"></script>
</body>

</html>