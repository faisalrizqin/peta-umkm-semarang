<?php
// pages/umkm/index.php
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
    $query = "DELETE FROM umkm WHERE id_umkm = '$id'";
    if (mysqli_query($conn, $query)) {
        $success = "Data UMKM berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data UMKM!";
    }
}

// Filter
$where = "";
$filter_kategori = isset($_GET['kategori']) ? clean_input($_GET['kategori']) : '';
$filter_kecamatan = isset($_GET['kecamatan']) ? clean_input($_GET['kecamatan']) : '';
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

if ($filter_kategori) {
    $where .= " AND u.id_kategori = '$filter_kategori'";
}
if ($filter_kecamatan) {
    $where .= " AND u.kecamatan = '$filter_kecamatan'";
}
if ($search) {
    $where .= " AND (u.nama_umkm LIKE '%$search%' OR u.nama_pemilik LIKE '%$search%')";
}

// Ambil data UMKM dengan join kategori
$query = "SELECT u.*, k.nama_kategori 
          FROM umkm u 
          LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
          WHERE 1=1 $where
          ORDER BY u.created_at DESC";
$result = mysqli_query($conn, $query);

// Ambil data kategori untuk filter
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Ambil daftar kecamatan untuk filter
$kecamatan_list = mysqli_query($conn, "SELECT DISTINCT nama_kecamatan FROM ref_kecamatan ORDER BY nama_kecamatan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data UMKM - Peta UMKM Semarang</title>
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
                <h2>Data UMKM</h2>
                <p>Kelola data UMKM di Kota Semarang</p>
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
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <h3>Daftar UMKM</h3>
                        <a href="tambah.php" class="btn btn-primary">
                            ➕ Tambah UMKM
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter dan Search -->
                    <div class="filter-section">
                        <form method="GET" action="" class="filter-form">
                            <div class="filter-row">
                                <div class="filter-item">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="🔍 Cari nama UMKM atau pemilik..."
                                           value="<?php echo $search; ?>">
                                </div>
                                
                                <div class="filter-item">
                                    <select name="kategori" class="form-control">
                                        <option value="">-- Semua Kategori --</option>
                                        <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                        <option value="<?php echo $kat['id_kategori']; ?>" 
                                                <?php echo ($filter_kategori == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                            <?php echo $kat['nama_kategori']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="filter-item">
                                    <select name="kecamatan" class="form-control">
                                        <option value="">-- Semua Kecamatan --</option>
                                        <?php while ($kec = mysqli_fetch_assoc($kecamatan_list)): ?>
                                        <option value="<?php echo $kec['nama_kecamatan']; ?>"
                                                <?php echo ($filter_kecamatan == $kec['nama_kecamatan']) ? 'selected' : ''; ?>>
                                            <?php echo $kec['nama_kecamatan']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        🔍 Filter
                                    </button>
                                    <a href="index.php" class="btn btn-secondary btn-sm">
                                        🔄 Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="15%">Nama UMKM</th>
                                    <th width="12%">Pemilik</th>
                                    <th width="10%">Kategori</th>
                                    <th width="20%">Alamat</th>
                                    <th width="12%">Kecamatan</th>
                                    <th width="15%">Bahan Baku</th>
                                    <th width="8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo $row['nama_umkm']; ?></strong></td>
                                    <td><?php echo $row['nama_pemilik']; ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo $row['nama_kategori']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo $row['alamat_lengkap']; ?><br>
                                            RT <?php echo $row['rt']; ?> / RW <?php echo $row['rw']; ?>, 
                                            <?php echo $row['kelurahan']; ?>
                                        </small>
                                    </td>
                                    <td><?php echo $row['kecamatan']; ?></td>
                                    <td><small><?php echo $row['bahan_baku_utama']; ?></small></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="detail.php?id=<?php echo $row['id_umkm']; ?>" 
                                               class="btn btn-info btn-sm" 
                                               title="Detail">
                                                👁️
                                            </a>
                                            <a href="edit.php?id=<?php echo $row['id_umkm']; ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                ✏️
                                            </a>
                                            <a href="?delete=<?php echo $row['id_umkm']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               title="Hapus"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus UMKM ini?')">
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
                        <div class="empty-icon">🏪</div>
                        <h3>Belum Ada Data UMKM</h3>
                        <p>Silakan tambahkan data UMKM terlebih dahulu</p>
                        <a href="tambah.php" class="btn btn-primary">Tambah UMKM Pertama</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/script.js"></script>
</body>
</html>