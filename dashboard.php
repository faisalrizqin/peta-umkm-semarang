<?php
// dashboard.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once 'config/database.php';

// Hitung statistik
$query_kategori = "SELECT COUNT(*) as total FROM kategori";
$result_kategori = mysqli_query($conn, $query_kategori);
$total_kategori = mysqli_fetch_assoc($result_kategori)['total'];

$query_umkm = "SELECT COUNT(*) as total FROM umkm";
$result_umkm = mysqli_query($conn, $query_umkm);
$total_umkm = mysqli_fetch_assoc($result_umkm)['total'];

// Hitung UMKM per kecamatan
$query_kecamatan = "SELECT kecamatan, COUNT(*) as total FROM umkm GROUP BY kecamatan ORDER BY total DESC LIMIT 5";
$result_kecamatan = mysqli_query($conn, $query_kecamatan);

// Hitung UMKM per kategori
$query_kategori_stat = "SELECT k.nama_kategori, COUNT(u.id_umkm) as total 
                        FROM kategori k 
                        LEFT JOIN umkm u ON k.id_kategori = u.id_kategori 
                        GROUP BY k.id_kategori 
                        ORDER BY total DESC LIMIT 5";
$result_kategori_stat = mysqli_query($conn, $query_kategori_stat);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Peta UMKM Semarang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Content Area -->
        <div class="content">
            <div class="page-header">
                <h2>Dashboard</h2>
                <p>Selamat datang di Sistem Pemetaan UMKM Kota Semarang</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">📊</div>
                    <div class="stat-details">
                        <h3><?php echo $total_kategori; ?></h3>
                        <p>Total Kategori</p>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-details">
                        <h3><?php echo $total_umkm; ?></h3>
                        <p>Total UMKM</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">📍</div>
                    <div class="stat-details">
                        <h3>16</h3>
                        <p>Kecamatan</p>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">👤</div>
                    <div class="stat-details">
                        <h3><?php echo $_SESSION['nama_lengkap']; ?></h3>
                        <p>Pengguna Aktif</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- UMKM per Kecamatan -->
                <div class="card">
                    <div class="card-header">
                        <h3>Top 5 Kecamatan dengan UMKM Terbanyak</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <?php while ($row = mysqli_fetch_assoc($result_kecamatan)): ?>
                                <div class="chart-bar-item">
                                    <div class="chart-label"><?php echo $row['kecamatan']; ?></div>
                                    <div class="chart-bar-wrapper">
                                        <div class="chart-bar"
                                            style="width: <?php echo ($row['total'] / $total_umkm * 100); ?>%">
                                            <span class="chart-value"><?php echo $row['total']; ?> UMKM</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <!-- UMKM per Kategori -->
                <div class="card">
                    <div class="card-header">
                        <h3>Top 5 Kategori UMKM</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <?php while ($row = mysqli_fetch_assoc($result_kategori_stat)): ?>
                                <div class="chart-bar-item">
                                    <div class="chart-label"><?php echo $row['nama_kategori']; ?></div>
                                    <div class="chart-bar-wrapper">
                                        <div class="chart-bar chart-bar-success"
                                            style="width: <?php echo $total_umkm > 0 ? ($row['total'] / $total_umkm * 100) : 0; ?>%">
                                            <span class="chart-value"><?php echo $row['total']; ?> UMKM</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3>Menu Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="pages/kategori/tambah.php" class="quick-action-btn">
                            <div class="quick-action-icon">➕</div>
                            <div class="quick-action-text">Tambah Kategori</div>
                        </a>

                        <a href="pages/umkm/tambah.php" class="quick-action-btn">
                            <div class="quick-action-icon">🏪</div>
                            <div class="quick-action-text">Tambah UMKM</div>
                        </a>

                        <a href="pages/peta/index.php" class="quick-action-btn">
                            <div class="quick-action-icon">🗺️</div>
                            <div class="quick-action-text">Lihat Peta</div>
                        </a>

                        <a href="pages/laporan/index.php" class="quick-action-btn">
                            <div class="quick-action-icon">📄</div>
                            <div class="quick-action-text">Laporan</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo isset($root_path) ? $root_path : '../../'; ?>assets/js/script.js"></script>
</body>

</html>