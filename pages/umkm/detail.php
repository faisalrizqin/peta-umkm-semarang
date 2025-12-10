<?php
// pages/umkm/detail.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = clean_input($_GET['id']);

// Ambil data UMKM dengan join kategori
$query = "SELECT u.*, k.nama_kategori 
          FROM umkm u 
          LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
          WHERE u.id_umkm = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail UMKM - <?php echo $data['nama_umkm']; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
                <h2>Detail UMKM</h2>
                <p>Informasi lengkap UMKM</p>
            </div>

            <div class="detail-container">
                <!-- Info Card -->
                <div class="detail-card">
                    <div class="detail-header">
                        <div>
                            <h2><?php echo $data['nama_umkm']; ?></h2>
                            <span class="badge badge-info" style="font-size: 14px;">
                                <?php echo $data['nama_kategori']; ?>
                            </span>
                        </div>
                        <div class="detail-actions">
                            <a href="edit.php?id=<?php echo $data['id_umkm']; ?>" class="btn btn-warning">
                                ✏️ Edit
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                ↩️ Kembali
                            </a>
                        </div>
                    </div>

                    <div class="detail-body">
                        <div class="detail-section">
                            <h3 class="detail-section-title">👤 Informasi Pemilik</h3>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Nama Pemilik</div>
                                    <div class="detail-value"><?php echo $data['nama_pemilik']; ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h3 class="detail-section-title">📍 Alamat Lengkap</h3>
                            <div class="detail-grid">
                                <div class="detail-item full-width">
                                    <div class="detail-label">Alamat</div>
                                    <div class="detail-value"><?php echo $data['alamat_lengkap']; ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">RT / RW</div>
                                    <div class="detail-value"><?php echo $data['rt']; ?> / <?php echo $data['rw']; ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Kelurahan</div>
                                    <div class="detail-value"><?php echo $data['kelurahan']; ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Kecamatan</div>
                                    <div class="detail-value"><?php echo $data['kecamatan']; ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Koordinat</div>
                                    <div class="detail-value">
                                        Lat: <?php echo $data['latitude']; ?>, Long: <?php echo $data['longitude']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h3 class="detail-section-title">🏭 Informasi Produksi</h3>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Bahan Baku Utama</div>
                                    <div class="detail-value">
                                        <?php echo $data['bahan_baku_utama'] ? $data['bahan_baku_utama'] : '<em>Tidak ada data</em>'; ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Alat Produksi Utama</div>
                                    <div class="detail-value">
                                        <?php echo $data['alat_produksi_utama'] ? $data['alat_produksi_utama'] : '<em>Tidak ada data</em>'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($data['deskripsi']): ?>
                            <div class="detail-section">
                                <h3 class="detail-section-title">📋 Deskripsi Usaha</h3>
                                <div class="detail-description">
                                    <?php echo nl2br($data['deskripsi']); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="detail-section">
                            <h3 class="detail-section-title">🗺️ Lokasi Peta</h3>
                            <div id="map" style="height: 400px; border-radius: 10px; overflow: hidden;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([<?php echo $data['latitude']; ?>, <?php echo $data['longitude']; ?>], 15);

        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Tambahkan marker
        var marker = L.marker([<?php echo $data['latitude']; ?>, <?php echo $data['longitude']; ?>]).addTo(map);
        marker.bindPopup("<b><?php echo $data['nama_umkm']; ?></b><br><?php echo $data['alamat_lengkap']; ?>").openPopup();
    </script>
</body>

</html>