<?php
// pages/analisis/detail.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Ambil ID cluster dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$cluster_id = clean_input($_GET['id']);

// Ambil data sentra
$query_sentra = "SELECT * FROM cluster_sentra WHERE id_cluster = '$cluster_id'";
$result_sentra = mysqli_query($conn, $query_sentra);

if (mysqli_num_rows($result_sentra) == 0) {
    header("Location: index.php");
    exit;
}

$sentra = mysqli_fetch_assoc($result_sentra);

// Decode JSON data
$kategori_dominan = json_decode($sentra['kategori_dominan'], true);
$kecamatan_dominan = json_decode($sentra['kecamatan_dominan'], true);
$bahan_baku_umum = json_decode($sentra['bahan_baku_umum'], true);
$alat_produksi_umum = json_decode($sentra['alat_produksi_umum'], true);

// Ambil semua UMKM dalam cluster ini
$query_umkm = "SELECT u.*, k.nama_kategori 
               FROM umkm u 
               LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
               WHERE u.cluster_id = '$cluster_id'
               ORDER BY u.nama_umkm ASC";
$result_umkm = mysqli_query($conn, $query_umkm);

// Ambil statistik tambahan
$query_stats = "
    SELECT 
        COUNT(DISTINCT u.kecamatan) as total_kecamatan,
        COUNT(DISTINCT u.id_kategori) as total_kategori,
        AVG(CHAR_LENGTH(u.bahan_baku_utama)) as avg_bahan_baku
    FROM umkm u
    WHERE u.cluster_id = '$cluster_id'
";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $query_stats));

// Warna cluster
$cluster_colors = [
    '#667eea',
    '#f59e0b',
    '#10b981',
    '#ef4444',
    '#8b5cf6',
    '#06b6d4',
    '#ec4899',
    '#f97316'
];
$color = $cluster_colors[$cluster_id];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?php echo $sentra['nama_sentra']; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .detail-header-sentra {
            background:
                <?php echo $color; ?>
            ;
            color: white;
            padding: 40px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .detail-header-sentra h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            color: white;
        }

        .detail-header-sentra p {
            margin: 0;
            opacity: 0.95;
            font-size: 16px;
            color: white;
        }

        .detail-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .detail-stat-item {
            background: rgba(255, 255, 255, 0.25);
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .detail-stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: white;
        }

        .detail-stat-label {
            font-size: 13px;
            opacity: 0.95;
            color: white;
        }

        .characteristic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .characteristic-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .characteristic-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .characteristic-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .characteristic-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .characteristic-list li:last-child {
            border-bottom: none;
        }

        .char-name {
            color: #475569;
            font-size: 14px;
        }

        .char-count {
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color:
                <?php echo $color; ?>
            ;
        }

        .map-detail-sentra {
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
        }

        .umkm-table-container {
            max-height: 600px;
            overflow-y: auto;
        }

        .filter-umkm {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .highlight-row {
            background:
                <?php echo $color; ?>
                15 !important;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
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
            <!-- Breadcrumb -->
            <div style="margin-bottom: 20px;">
                <a href="index.php" style="color: #64748b; text-decoration: none;">← Kembali ke Analisis ML</a>
            </div>

            <!-- Detail Header -->
            <div class="detail-header-sentra">
                <h1><?php echo $sentra['nama_sentra']; ?></h1>
                <p>Cluster ID: <?php echo $cluster_id; ?> | Analisis berbasis Machine Learning K-Means Clustering</p>

                <div class="detail-stats">
                    <div class="detail-stat-item">
                        <div class="detail-stat-value"><?php echo $sentra['jumlah_umkm']; ?></div>
                        <div class="detail-stat-label">Total UMKM</div>
                    </div>
                    <div class="detail-stat-item">
                        <div class="detail-stat-value"><?php echo $stats['total_kecamatan']; ?></div>
                        <div class="detail-stat-label">Kecamatan</div>
                    </div>
                    <div class="detail-stat-item">
                        <div class="detail-stat-value"><?php echo $stats['total_kategori']; ?></div>
                        <div class="detail-stat-label">Kategori Usaha</div>
                    </div>
                    <div class="detail-stat-item">
                        <div class="detail-stat-value"><?php echo round($sentra['centroid_lat'], 4); ?>,
                            <?php echo round($sentra['centroid_lon'], 4); ?>
                        </div>
                        <div class="detail-stat-label">Centroid Geografis</div>
                    </div>
                </div>
            </div>

            <!-- Karakteristik Sentra -->
            <div class="characteristic-grid">
                <!-- Kategori Dominan -->
                <div class="characteristic-card">
                    <div class="characteristic-title">
                        📊 Kategori Dominan
                    </div>
                    <ul class="characteristic-list">
                        <?php
                        $count = 0;
                        foreach ($kategori_dominan as $kategori => $jumlah):
                            if ($count >= 5)
                                break;
                            ?>
                            <li>
                                <span class="char-name"><?php echo $kategori; ?></span>
                                <span class="char-count"><?php echo $jumlah; ?> UMKM
                                    (<?php echo round($jumlah / $sentra['jumlah_umkm'] * 100, 1); ?>%)</span>
                            </li>
                            <?php
                            $count++;
                        endforeach;
                        ?>
                    </ul>
                </div>

                <!-- Kecamatan Dominan -->
                <div class="characteristic-card">
                    <div class="characteristic-title">
                        📍 Kecamatan Dominan
                    </div>
                    <ul class="characteristic-list">
                        <?php
                        $count = 0;
                        foreach ($kecamatan_dominan as $kecamatan => $jumlah):
                            if ($count >= 5)
                                break;
                            ?>
                            <li>
                                <span class="char-name"><?php echo $kecamatan; ?></span>
                                <span class="char-count"><?php echo $jumlah; ?> UMKM
                                    (<?php echo round($jumlah / $sentra['jumlah_umkm'] * 100, 1); ?>%)</span>
                            </li>
                            <?php
                            $count++;
                        endforeach;
                        ?>
                    </ul>
                </div>

                <!-- Bahan Baku Umum -->
                <div class="characteristic-card">
                    <div class="characteristic-title">
                        🧰 Bahan Baku Umum
                    </div>
                    <ul class="characteristic-list">
                        <?php
                        $count = 0;
                        foreach ($bahan_baku_umum as $item):
                            if ($count >= 5)
                                break;
                            ?>
                            <li>
                                <span class="char-name"><?php echo $item['nama']; ?></span>
                                <span class="char-count"><?php echo $item['frekuensi']; ?>x</span>
                            </li>
                            <?php
                            $count++;
                        endforeach;
                        ?>
                    </ul>
                </div>

                <!-- Alat Produksi Umum -->
                <div class="characteristic-card">
                    <div class="characteristic-title">
                        🔧 Alat Produksi Umum
                    </div>
                    <ul class="characteristic-list">
                        <?php
                        $count = 0;
                        foreach ($alat_produksi_umum as $item):
                            if ($count >= 5)
                                break;
                            ?>
                            <li>
                                <span class="char-name"><?php echo $item['nama']; ?></span>
                                <span class="char-count"><?php echo $item['frekuensi']; ?>x</span>
                            </li>
                            <?php
                            $count++;
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>

            <!-- Peta Sentra -->
            <div class="card" style="margin-bottom: 30px;">
                <div class="card-header">
                    <h3>🗺️ Sebaran Geografis UMKM dalam Sentra</h3>
                </div>
                <div class="card-body">
                    <div id="map" class="map-detail-sentra"></div>
                </div>
            </div>

            <!-- Daftar UMKM -->
            <div class="card">
                <div class="card-header">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <h3>🏪 Daftar UMKM dalam Sentra (<?php echo $sentra['jumlah_umkm']; ?> UMKM)</h3>
                        <div class="export-buttons">
                            <a href="export_sentra.php?id=<?php echo $cluster_id; ?>&type=excel"
                                class="btn btn-success btn-sm">
                                📗 Export Excel
                            </a>
                            <a href="export_sentra.php?id=<?php echo $cluster_id; ?>&type=pdf"
                                class="btn btn-danger btn-sm" target="_blank">
                                📕 Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="filter-umkm">
                        <input type="text" id="searchUMKM" class="form-control"
                            placeholder="🔍 Cari nama UMKM atau pemilik..." style="max-width: 300px;">
                        <select id="filterKecamatan" class="form-control" style="max-width: 200px;">
                            <option value="">Semua Kecamatan</option>
                            <?php foreach ($kecamatan_dominan as $kec => $jml): ?>
                                <option value="<?php echo $kec; ?>"><?php echo $kec; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button onclick="resetFilter()" class="btn btn-secondary btn-sm">🔄 Reset</button>
                    </div>

                    <div class="umkm-table-container">
                        <table class="table" id="umkmTable">
                            <thead>
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="20%">Nama UMKM</th>
                                    <th width="15%">Pemilik</th>
                                    <th width="12%">Kategori</th>
                                    <th width="15%">Kecamatan</th>
                                    <th width="20%">Bahan Baku</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($umkm = mysqli_fetch_assoc($result_umkm)):
                                    ?>
                                    <tr class="umkm-row" data-kecamatan="<?php echo $umkm['kecamatan']; ?>">
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo $umkm['nama_umkm']; ?></strong></td>
                                        <td><?php echo $umkm['nama_pemilik']; ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $umkm['nama_kategori']; ?></span>
                                        </td>
                                        <td><?php echo $umkm['kecamatan']; ?></td>
                                        <td><small><?php echo substr($umkm['bahan_baku_utama'], 0, 50); ?>...</small></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="../umkm/detail.php?id=<?php echo $umkm['id_umkm']; ?>"
                                                    class="btn btn-info btn-sm" title="Detail">
                                                    👁️
                                                </a>
                                                <button
                                                    onclick="focusOnMap(<?php echo $umkm['latitude']; ?>, <?php echo $umkm['longitude']; ?>, '<?php echo $umkm['nama_umkm']; ?>')"
                                                    class="btn btn-primary btn-sm" title="Lihat di Peta">
                                                    🗺️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script>
        // Inisialisasi peta
        const map = L.map('map').setView([<?php echo $sentra['centroid_lat']; ?>, <?php echo $sentra['centroid_lon']; ?>], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Data UMKM
        const umkmData = [
            <?php
            mysqli_data_seek($result_umkm, 0);
            while ($umkm = mysqli_fetch_assoc($result_umkm)):
                ?>
                            {
                    id: <?php echo $umkm['id_umkm']; ?>,
                    nama: "<?php echo addslashes($umkm['nama_umkm']); ?>",
                    pemilik: "<?php echo addslashes($umkm['nama_pemilik']); ?>",
                    kategori: "<?php echo $umkm['nama_kategori']; ?>",
                    kecamatan: "<?php echo $umkm['kecamatan']; ?>",
                    lat: <?php echo $umkm['latitude']; ?>,
                    lon: <?php echo $umkm['longitude']; ?>
                },
            <?php endwhile; ?>
        ];

        // Marker untuk centroid
        const centroidIcon = L.divIcon({
            className: 'centroid-marker',
            html: `<div style="background-color: <?php echo $color; ?>; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 5px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.3); font-size: 18px;">⭐</div>`,
            iconSize: [50, 50],
            iconAnchor: [25, 25]
        });

        L.marker([<?php echo $sentra['centroid_lat']; ?>, <?php echo $sentra['centroid_lon']; ?>], { icon: centroidIcon })
            .addTo(map)
            .bindPopup('<strong>Pusat Sentra</strong><br><?php echo $sentra['nama_sentra']; ?>');

        // Marker untuk setiap UMKM
        const markers = [];
        umkmData.forEach(function (umkm) {
            const icon = L.divIcon({
                className: 'umkm-marker',
                html: `<div style="background-color: <?php echo $color; ?>; width: 25px; height: 25px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [25, 25],
                iconAnchor: [12.5, 25]
            });

            const marker = L.marker([umkm.lat, umkm.lon], { icon: icon });

            const popup = `
                <div style="min-width: 180px;">
                    <strong>${umkm.nama}</strong><br>
                    <small>${umkm.kategori} | ${umkm.kecamatan}</small><br>
                    <a href="../umkm/detail.php?id=${umkm.id}" style="font-size: 11px;">Detail →</a>
                </div>
            `;

            marker.bindPopup(popup);
            marker.addTo(map);
            markers.push({ marker: marker, data: umkm });
        });

        // Fungsi untuk fokus ke UMKM di peta
        function focusOnMap(lat, lon, nama) {
            map.setView([lat, lon], 16);

            // Buka popup marker yang sesuai
            markers.forEach(function (item) {
                if (item.data.lat == lat && item.data.lon == lon) {
                    item.marker.openPopup();
                }
            });

            // Scroll ke peta
            document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Filter UMKM
        const searchInput = document.getElementById('searchUMKM');
        const filterKecamatan = document.getElementById('filterKecamatan');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const kecamatan = filterKecamatan.value;
            const rows = document.querySelectorAll('.umkm-row');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                const rowKecamatan = row.getAttribute('data-kecamatan');

                const matchSearch = text.includes(searchTerm);
                const matchKecamatan = !kecamatan || rowKecamatan === kecamatan;

                if (matchSearch && matchKecamatan) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTable);
        filterKecamatan.addEventListener('change', filterTable);

        function resetFilter() {
            searchInput.value = '';
            filterKecamatan.value = '';
            filterTable();
        }
    </script>
</body>

</html>