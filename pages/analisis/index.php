<?php
// pages/analisis/index.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Ambil data sentra dari database
$query_sentra = "SELECT * FROM cluster_sentra ORDER BY id_cluster ASC";
$result_sentra = mysqli_query($conn, $query_sentra);

// Ambil statistik umum
$total_umkm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM umkm"))['total'];
$total_sentra = mysqli_num_rows($result_sentra);

// Ambil data untuk chart
$sentra_data = [];
while ($row = mysqli_fetch_assoc($result_sentra)) {
    $sentra_data[] = $row;
}
mysqli_data_seek($result_sentra, 0);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Sentra Produksi - ML</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .ml-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .ml-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .ml-header p {
            margin: 0;
            opacity: 0.9;
        }

        .ml-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            margin-top: 15px;
        }

        .sentra-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .sentra-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid;
        }

        .sentra-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .sentra-card.cluster-0 {
            border-left-color: #667eea;
        }

        .sentra-card.cluster-1 {
            border-left-color: #f59e0b;
        }

        .sentra-card.cluster-2 {
            border-left-color: #10b981;
        }

        .sentra-card.cluster-3 {
            border-left-color: #ef4444;
        }

        .sentra-card.cluster-4 {
            border-left-color: #8b5cf6;
        }

        .sentra-card.cluster-5 {
            border-left-color: #06b6d4;
        }

        .sentra-card.cluster-6 {
            border-left-color: #ec4899;
        }

        .sentra-card.cluster-7 {
            border-left-color: #f97316;
        }

        .sentra-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .sentra-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .sentra-count {
            background: #f1f5f9;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #667eea;
        }

        .sentra-info {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sentra-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sentra-value {
            font-size: 14px;
            color: #1e293b;
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .tag {
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            color: #64748b;
        }

        .sentra-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .map-container-ml {
            height: 600px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .insight-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 5px solid #f59e0b;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .insight-box h3 {
            margin: 0 0 10px 0;
            color: #92400e;
        }

        .insight-box p {
            margin: 0;
            color: #78350f;
            line-height: 1.6;
        }
    </style>
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
            <!-- ML Header -->
            <div class="ml-header">
                <h1>🤖 Analisis Sentra Produksi dengan Machine Learning</h1>
                <p>Identifikasi pola dan pengelompokan UMKM berdasarkan Bahan Baku & Alat Produksi menggunakan K-Means
                    Clustering</p>
                <span class="ml-badge">🎯 <?php echo $total_sentra; ?> Sentra Teridentifikasi</span>
                <span class="ml-badge">🏪 <?php echo number_format($total_umkm); ?> UMKM Dianalisis</span>
            </div>

            <!-- Insight Box -->
            <div class="insight-box">
                <h3>💡 Insight Analisis</h3>
                <p>
                    Sistem menggunakan algoritma <strong>K-Means Clustering</strong> dan <strong>TF-IDF
                        Vectorization</strong>
                    untuk mengelompokkan <?php echo number_format($total_umkm); ?> UMKM berdasarkan kesamaan bahan baku
                    dan alat produksi.
                    Hasil analisis menunjukkan <?php echo $total_sentra; ?> sentra produksi alami yang terbentuk di Kota
                    Semarang,
                    yang dapat membantu pemerintah dalam:
                </p>
                <ul style="margin: 10px 0 0 20px; color: #78350f;">
                    <li>Pengembangan kawasan industri terpadu</li>
                    <li>Program bantuan bahan baku dan alat produksi yang tepat sasaran</li>
                    <li>Kolaborasi antar UMKM dalam satu sentra</li>
                    <li>Pelatihan dan pembinaan berbasis sentra</li>
                </ul>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-details">
                        <h3><?php echo $total_sentra; ?></h3>
                        <p>Sentra Produksi</p>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-details">
                        <h3><?php echo number_format($total_umkm); ?></h3>
                        <p>Total UMKM</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">📊</div>
                    <div class="stat-details">
                        <h3><?php echo number_format($total_umkm / $total_sentra, 0); ?></h3>
                        <p>Rata-rata per Sentra</p>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">🤖</div>
                    <div class="stat-details">
                        <h3>K-Means</h3>
                        <p>Algoritma ML</p>
                    </div>
                </div>
            </div>

            <!-- Sentra Cards Grid -->
            <div class="card" style="margin-bottom: 30px;">
                <div class="card-header">
                    <h3>📍 Daftar Sentra Produksi Teridentifikasi</h3>
                </div>
                <div class="card-body">
                    <div class="sentra-grid">
                        <?php while ($sentra = mysqli_fetch_assoc($result_sentra)):
                            $kategori_dom = json_decode($sentra['kategori_dominan'], true);
                            $kecamatan_dom = json_decode($sentra['kecamatan_dominan'], true);
                            $bahan_baku = json_decode($sentra['bahan_baku_umum'], true);
                            $alat_prod = json_decode($sentra['alat_produksi_umum'], true);
                            ?>
                            <div class="sentra-card cluster-<?php echo $sentra['id_cluster']; ?>">
                                <div class="sentra-header">
                                    <div class="sentra-title">
                                        <?php echo $sentra['nama_sentra']; ?>
                                    </div>
                                    <div class="sentra-count">
                                        <?php echo $sentra['jumlah_umkm']; ?> UMKM
                                    </div>
                                </div>

                                <div class="sentra-info">
                                    <div class="sentra-label">📊 Kategori Dominan</div>
                                    <div class="tag-list">
                                        <?php
                                        $count = 0;
                                        foreach ($kategori_dom as $kat => $jml):
                                            if ($count >= 3)
                                                break;
                                            ?>
                                            <span class="tag"><?php echo $kat; ?> (<?php echo $jml; ?>)</span>
                                            <?php
                                            $count++;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>

                                <div class="sentra-info">
                                    <div class="sentra-label">📍 Kecamatan Dominan</div>
                                    <div class="tag-list">
                                        <?php
                                        $count = 0;
                                        foreach ($kecamatan_dom as $kec => $jml):
                                            if ($count >= 3)
                                                break;
                                            ?>
                                            <span class="tag"><?php echo $kec; ?> (<?php echo $jml; ?>)</span>
                                            <?php
                                            $count++;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>

                                <div class="sentra-info">
                                    <div class="sentra-label">🧰 Bahan Baku Umum</div>
                                    <div class="tag-list">
                                        <?php
                                        $count = 0;
                                        foreach ($bahan_baku as $item):
                                            if ($count >= 3)
                                                break;
                                            ?>
                                            <span class="tag"><?php echo $item['nama']; ?></span>
                                            <?php
                                            $count++;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>

                                <div class="sentra-info" style="border: none;">
                                    <div class="sentra-label">🔧 Alat Produksi Umum</div>
                                    <div class="tag-list">
                                        <?php
                                        $count = 0;
                                        foreach ($alat_prod as $item):
                                            if ($count >= 3)
                                                break;
                                            ?>
                                            <span class="tag"><?php echo $item['nama']; ?></span>
                                            <?php
                                            $count++;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>

                                <div class="sentra-actions">
                                    <a href="detail.php?id=<?php echo $sentra['id_cluster']; ?>"
                                        class="btn btn-primary btn-sm" style="flex: 1;">
                                        👁️ Detail Sentra
                                    </a>
                                    <button
                                        onclick="zoomToSentra(<?php echo $sentra['centroid_lat']; ?>, <?php echo $sentra['centroid_lon']; ?>)"
                                        class="btn btn-secondary btn-sm">
                                        🗺️
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Peta Sentra -->
            <div class="card">
                <div class="card-header">
                    <h3>🗺️ Visualisasi Geografis Sentra Produksi</h3>
                </div>
                <div class="card-body">
                    <div id="map" class="map-container-ml"></div>
                </div>
            </div>

            <!-- Chart Distribusi -->
            <div class="charts-grid" style="margin-top: 30px;">
                <div class="card">
                    <div class="card-header">
                        <h3>📊 Distribusi UMKM per Sentra</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartSentra" height="250"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>🎯 Karakteristik Sentra</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartKarakteristik" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script>
        // Data sentra dari PHP
        const sentraData = <?php echo json_encode($sentra_data); ?>;

        // Inisialisasi peta
        const map = L.map('map').setView([-7.0051, 110.4108], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Warna untuk setiap cluster
        const clusterColors = [
            '#667eea', '#f59e0b', '#10b981', '#ef4444',
            '#8b5cf6', '#06b6d4', '#ec4899', '#f97316'
        ];

        // Tambahkan marker untuk setiap sentra
        sentraData.forEach(function (sentra) {
            const color = clusterColors[sentra.id_cluster];

            // Custom icon
            const icon = L.divIcon({
                className: 'custom-cluster-marker',
                html: `<div style="background-color: ${color}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">${sentra.jumlah_umkm}</div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            const marker = L.marker([sentra.centroid_lat, sentra.centroid_lon], { icon: icon });

            const popupContent = `
                <div style="min-width: 200px;">
                    <h4 style="margin: 0 0 10px 0; color: ${color};">${sentra.nama_sentra}</h4>
                    <p style="margin: 5px 0;"><strong>Jumlah UMKM:</strong> ${sentra.jumlah_umkm}</p>
                    <a href="detail.php?id=${sentra.id_cluster}" style="display: inline-block; margin-top: 10px; padding: 5px 15px; background: ${color}; color: white; text-decoration: none; border-radius: 5px; font-size: 12px;">Detail Sentra →</a>
                </div>
            `;

            marker.bindPopup(popupContent);
            marker.addTo(map);
        });

        // Fungsi zoom ke sentra
        function zoomToSentra(lat, lon) {
            map.setView([lat, lon], 15);
        }

        // Chart distribusi UMKM per sentra
        const ctxSentra = document.getElementById('chartSentra').getContext('2d');
        new Chart(ctxSentra, {
            type: 'bar',
            data: {
                labels: sentraData.map(s => s.nama_sentra),
                datasets: [{
                    label: 'Jumlah UMKM',
                    data: sentraData.map(s => s.jumlah_umkm),
                    backgroundColor: clusterColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 100 }
                    }
                }
            }
        });

        // Chart karakteristik (doughnut)
        const ctxKarakteristik = document.getElementById('chartKarakteristik').getContext('2d');
        new Chart(ctxKarakteristik, {
            type: 'doughnut',
            data: {
                labels: sentraData.map(s => s.nama_sentra.split(' ').slice(1, 2).join(' ')),
                datasets: [{
                    data: sentraData.map(s => s.jumlah_umkm),
                    backgroundColor: clusterColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 15, padding: 10 }
                    }
                }
            }
        });
    </script>
</body>

</html>