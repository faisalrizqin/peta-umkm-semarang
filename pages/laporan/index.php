<?php
// pages/laporan/index.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Ambil data statistik
$total_umkm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM umkm"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori"))['total'];

// UMKM per kategori
$query_kategori = "SELECT k.nama_kategori, COUNT(u.id_umkm) as total 
                   FROM kategori k 
                   LEFT JOIN umkm u ON k.id_kategori = u.id_kategori 
                   GROUP BY k.id_kategori 
                   ORDER BY total DESC";
$result_kategori = mysqli_query($conn, $query_kategori);

// UMKM per kecamatan
$query_kecamatan = "SELECT kecamatan, COUNT(*) as total 
                    FROM umkm 
                    GROUP BY kecamatan 
                    ORDER BY total DESC";
$result_kecamatan = mysqli_query($conn, $query_kecamatan);

// Ambil filter
$filter_kategori = isset($_GET['kategori']) ? clean_input($_GET['kategori']) : '';
$filter_kecamatan = isset($_GET['kecamatan']) ? clean_input($_GET['kecamatan']) : '';

// Query untuk laporan detail
$where = "WHERE 1=1";
if ($filter_kategori) {
    $where .= " AND u.id_kategori = '$filter_kategori'";
}
if ($filter_kecamatan) {
    $where .= " AND u.kecamatan = '$filter_kecamatan'";
}

$query_detail = "SELECT u.*, k.nama_kategori 
                 FROM umkm u 
                 LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
                 $where
                 ORDER BY u.nama_umkm ASC";
$result_detail = mysqli_query($conn, $query_detail);

// Data untuk filter
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kecamatan_list = mysqli_query($conn, "SELECT DISTINCT nama_kecamatan FROM ref_kecamatan ORDER BY nama_kecamatan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan UMKM - Peta UMKM Semarang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h2>📊 Laporan Data UMKM</h2>
                <p>Laporan dan analisis data UMKM Kota Semarang</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-details">
                        <h3><?php echo $total_umkm; ?></h3>
                        <p>Total UMKM</p>
                    </div>
                </div>
                
                <div class="stat-card stat-success">
                    <div class="stat-icon">📁</div>
                    <div class="stat-details">
                        <h3><?php echo $total_kategori; ?></h3>
                        <p>Kategori Usaha</p>
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
                    <div class="stat-icon">📈</div>
                    <div class="stat-details">
                        <h3><?php echo $total_umkm > 0 ? number_format($total_umkm / 16, 1) : 0; ?></h3>
                        <p>Rata-rata per Kecamatan</p>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-grid" style="margin-bottom: 30px;">
                <!-- Chart Kategori -->
                <div class="card">
                    <div class="card-header">
                        <h3>UMKM Berdasarkan Kategori</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartKategori" height="250"></canvas>
                    </div>
                </div>
                
                <!-- Chart Kecamatan -->
                <div class="card">
                    <div class="card-header">
                        <h3>UMKM Berdasarkan Kecamatan</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartKecamatan" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Filter dan Export -->
            <div class="card">
                <div class="card-header">
                    <h3>Laporan Detail UMKM</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" action="" class="filter-form">
                            <div class="filter-row">
                                <div class="filter-item">
                                    <label>Filter Kategori</label>
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
                                    <label>Filter Kecamatan</label>
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
                    
                    <!-- Export Buttons -->
                    <div style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="printLaporan()" class="btn btn-primary">
                            🖨️ Cetak Laporan
                        </button>
                        <a href="export_excel.php?kategori=<?php echo $filter_kategori; ?>&kecamatan=<?php echo $filter_kecamatan; ?>" 
                           class="btn btn-success">
                            📗 Export Excel
                        </a>
                        <a href="export_pdf.php?kategori=<?php echo $filter_kategori; ?>&kecamatan=<?php echo $filter_kecamatan; ?>" 
                           class="btn btn-danger" target="_blank">
                            📕 Export PDF
                        </a>
                    </div>
                    
                    <!-- Table Laporan -->
                    <div class="table-responsive" id="printable-area">
                        <div style="text-align: center; margin-bottom: 20px;" class="print-only">
                            <h2>LAPORAN DATA UMKM</h2>
                            <h3>KOTA SEMARANG</h3>
                            <p>Tanggal Cetak: <?php echo date('d F Y'); ?></p>
                            <?php if ($filter_kategori || $filter_kecamatan): ?>
                            <p style="font-size: 14px;">
                                <?php if ($filter_kategori): 
                                    $kat_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$filter_kategori'"));
                                ?>
                                Filter Kategori: <strong><?php echo $kat_name['nama_kategori']; ?></strong>
                                <?php endif; ?>
                                <?php if ($filter_kecamatan): ?>
                                | Filter Kecamatan: <strong><?php echo $filter_kecamatan; ?></strong>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                            <hr>
                        </div>
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama UMKM</th>
                                    <th>Pemilik</th>
                                    <th>Kategori</th>
                                    <th>Alamat</th>
                                    <th>Kecamatan</th>
                                    <th>Bahan Baku</th>
                                    <th>Alat Produksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if (mysqli_num_rows($result_detail) > 0):
                                    while ($row = mysqli_fetch_assoc($result_detail)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_umkm']; ?></td>
                                    <td><?php echo $row['nama_pemilik']; ?></td>
                                    <td><?php echo $row['nama_kategori']; ?></td>
                                    <td style="font-size: 12px;">
                                        <?php echo $row['alamat_lengkap']; ?><br>
                                        RT <?php echo $row['rt']; ?>/RW <?php echo $row['rw']; ?>, <?php echo $row['kelurahan']; ?>
                                    </td>
                                    <td><?php echo $row['kecamatan']; ?></td>
                                    <td style="font-size: 12px;"><?php echo $row['bahan_baku_utama']; ?></td>
                                    <td style="font-size: 12px;"><?php echo $row['alat_produksi_utama']; ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 30px;">
                                        Tidak ada data UMKM
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 30px; text-align: right;" class="print-only">
                            <p>Semarang, <?php echo date('d F Y'); ?></p>
                            <br><br><br>
                            <p><strong><?php echo $_SESSION['nama_lengkap']; ?></strong></p>
                            <p>Staff Ahli Walikota Semarang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/script.js"></script>
    <script>
        // Data untuk chart kategori
        const kategoriData = {
            labels: [
                <?php 
                mysqli_data_seek($result_kategori, 0);
                while ($row = mysqli_fetch_assoc($result_kategori)) {
                    echo "'" . $row['nama_kategori'] . "',";
                }
                ?>
            ],
            datasets: [{
                label: 'Jumlah UMKM',
                data: [
                    <?php 
                    mysqli_data_seek($result_kategori, 0);
                    while ($row = mysqli_fetch_assoc($result_kategori)) {
                        echo $row['total'] . ",";
                    }
                    ?>
                ],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f59e0b',
                    '#10b981',
                    '#ef4444',
                    '#8b5cf6',
                    '#06b6d4',
                    '#ec4899'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };
        
        // Chart Kategori
        const ctxKategori = document.getElementById('chartKategori').getContext('2d');
        new Chart(ctxKategori, {
            type: 'doughnut',
            data: kategoriData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
        
        // Data untuk chart kecamatan
        const kecamatanData = {
            labels: [
                <?php 
                mysqli_data_seek($result_kecamatan, 0);
                while ($row = mysqli_fetch_assoc($result_kecamatan)) {
                    echo "'" . $row['kecamatan'] . "',";
                }
                ?>
            ],
            datasets: [{
                label: 'Jumlah UMKM',
                data: [
                    <?php 
                    mysqli_data_seek($result_kecamatan, 0);
                    while ($row = mysqli_fetch_assoc($result_kecamatan)) {
                        echo $row['total'] . ",";
                    }
                    ?>
                ],
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 2
            }]
        };
        
        // Chart Kecamatan
        const ctxKecamatan = document.getElementById('chartKecamatan').getContext('2d');
        new Chart(ctxKecamatan, {
            type: 'bar',
            data: kecamatanData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Fungsi print
        function printLaporan() {
            window.print();
        }
    </script>
    
    <style>
        @media print {
            .sidebar, .header, .page-header, .stats-grid, .charts-grid, 
            .filter-section, .btn, .no-print {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            .table {
                font-size: 11px;
            }
            
            .table th, .table td {
                padding: 8px 5px;
            }
        }
        
        .print-only {
            display: none;
        }
    </style>
</body>
</html>