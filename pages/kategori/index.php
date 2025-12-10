<?php
// pages/kategori/index.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Proses hapus jika ada
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);

    // Cek apakah kategori masih digunakan
    $check = mysqli_query($conn, "SELECT COUNT(*) as total FROM umkm WHERE id_kategori = '$id'");
    $data_check = mysqli_fetch_assoc($check);

    if ($data_check['total'] > 0) {
        $error = "Kategori tidak dapat dihapus karena masih digunakan oleh " . $data_check['total'] . " UMKM!";
    } else {
        $query = "DELETE FROM kategori WHERE id_kategori = '$id'";
        if (mysqli_query($conn, $query)) {
            $success = "Kategori berhasil dihapus!";
        } else {
            $error = "Gagal menghapus kategori!";
        }
    }
}

// Ambil data kategori
$query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori UMKM - Peta UMKM Semarang</title>
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
                <h2>Kategori UMKM</h2>
                <p>Kelola kategori jenis usaha UMKM di Kota Semarang</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3>Daftar Kategori</h3>
                        <a href="tambah.php" class="btn btn-primary">
                            ➕ Tambah Kategori
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama Kategori</th>
                                        <th width="45%">Deskripsi</th>
                                        <th width="10%">Jumlah UMKM</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)):
                                        // Hitung jumlah UMKM per kategori
                                        $id_kat = $row['id_kategori'];
                                        $count_umkm = mysqli_query($conn, "SELECT COUNT(*) as total FROM umkm WHERE id_kategori = '$id_kat'");
                                        $total_umkm = mysqli_fetch_assoc($count_umkm)['total'];
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><strong><?php echo $row['nama_kategori']; ?></strong></td>
                                            <td><?php echo $row['deskripsi'] ? $row['deskripsi'] : '<em>Tidak ada deskripsi</em>'; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?php echo $total_umkm; ?> UMKM</span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="edit.php?id=<?php echo $row['id_kategori']; ?>"
                                                        class="btn btn-warning btn-sm" title="Edit">
                                                        ✏️
                                                    </a>
                                                    <a href="?delete=<?php echo $row['id_kategori']; ?>"
                                                        class="btn btn-danger btn-sm" title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                                        🗑️
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">📁</div>
                            <h3>Belum Ada Kategori</h3>
                            <p>Silakan tambahkan kategori UMKM terlebih dahulu</p>
                            <a href="tambah.php" class="btn btn-primary">Tambah Kategori Pertama</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo isset($root_path) ? $root_path : '../../'; ?>assets/js/script.js"></script>
</body>

</html>