<?php
// pages/peta/index.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Ambil semua data UMKM untuk ditampilkan di peta
$query = "SELECT u.*, k.nama_kategori 
          FROM umkm u 
          LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
          ORDER BY u.nama_umkm ASC";
$result = mysqli_query($conn, $query);

// Konversi data ke JSON untuk JavaScript
$umkm_data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $umkm_data[] = $row;
}

// Ambil statistik per kecamatan
$query_stats = "SELECT kecamatan, COUNT(*) as total, 
                GROUP_CONCAT(nama_umkm SEPARATOR '|') as umkm_list
                FROM umkm 
                GROUP BY kecamatan";
$result_stats = mysqli_query($conn, $query_stats);
$kecamatan_stats = array();
while ($row = mysqli_fetch_assoc($result_stats)) {
    $kecamatan_stats[$row['kecamatan']] = array(
        'total' => $row['total'],
        'umkm_list' => explode('|', $row['umkm_list'])
    );
}

// Ambil data kategori untuk filter
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta UMKM - Kota Semarang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <style>
        .map-container {
            position: relative;
            height: calc(100vh - 180px);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .map-sidebar {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 320px;
            max-height: calc(100% - 40px);
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .map-sidebar-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            font-weight: 600;
        }

        .map-sidebar-content {
            padding: 15px;
            overflow-y: auto;
            flex: 1;
        }

        .map-filter {
            margin-bottom: 15px;
        }

        .map-filter label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .map-stats {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .map-stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .map-stat-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .map-stat-label {
            font-size: 13px;
            color: #64748b;
        }

        .map-stat-value {
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
        }

        .umkm-list {
            margin-top: 15px;
        }

        .umkm-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .umkm-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
            transform: translateX(5px);
        }

        .umkm-item-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .umkm-item-category {
            font-size: 11px;
            color: white;
            background: #667eea;
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .umkm-item-address {
            font-size: 11px;
            color: #64748b;
        }

        .leaflet-popup-content {
            margin: 15px;
            min-width: 200px;
        }

        .popup-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .popup-category {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .popup-info {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .popup-actions {
            display: flex;
            gap: 8px;
        }

        .popup-btn {
            flex: 1;
            padding: 8px;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .popup-btn-primary {
            background: #667eea;
            color: white;
        }

        .popup-btn-primary:hover {
            background: #5568d3;
        }

        .popup-btn-secondary {
            background: #f1f5f9;
            color: #64748b;
        }

        .popup-btn-secondary:hover {
            background: #e2e8f0;
        }

        .legend {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .legend-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1e293b;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .legend-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .map-sidebar {
                width: 100%;
                max-width: none;
                top: auto;
                bottom: 0;
                right: 0;
                max-height: 50vh;
            }
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
            <div class="page-header">
                <h2>🗺️ Peta UMKM Kota Semarang</h2>
                <p>Visualisasi sebaran UMKM di 16 Kecamatan Kota Semarang</p>
            </div>

            <div class="map-container">
                <div id="map"></div>

                <!-- Sidebar Filter -->
                <div class="map-sidebar">
                    <div class="map-sidebar-header">
                        📊 Statistik & Filter
                    </div>
                    <div class="map-sidebar-content">
                        <!-- Statistik -->
                        <div class="map-stats">
                            <div class="map-stat-item">
                                <span class="map-stat-label">Total UMKM</span>
                                <span class="map-stat-value" id="total-umkm"><?php echo count($umkm_data); ?></span>
                            </div>
                            <div class="map-stat-item">
                                <span class="map-stat-label">Kategori</span>
                                <span class="map-stat-value"><?php echo mysqli_num_rows($kategori_query); ?></span>
                            </div>
                            <div class="map-stat-item">
                                <span class="map-stat-label">Kecamatan</span>
                                <span class="map-stat-value">16</span>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div class="map-filter">
                            <label>Filter Kategori</label>
                            <select id="filter-kategori" class="form-control">
                                <option value="">Semua Kategori</option>
                                <?php
                                mysqli_data_seek($kategori_query, 0);
                                while ($kat = mysqli_fetch_assoc($kategori_query)):
                                    ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>">
                                        <?php echo $kat['nama_kategori']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="map-filter">
                            <label>Cari UMKM</label>
                            <input type="text" id="search-umkm" class="form-control" placeholder="Ketik nama UMKM...">
                        </div>

                        <!-- Daftar UMKM -->
                        <div class="umkm-list" id="umkm-list">
                            <!-- Akan diisi oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script>
        // Data UMKM dari PHP
        const umkmData = <?php echo json_encode($umkm_data); ?>;

        // Inisialisasi peta - centered di Semarang
        const map = L.map('map').setView([-6.9667, 110.4167], 12);

        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Buat marker cluster group
        const markers = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        // Icon kustom berdasarkan kategori
        function getMarkerIcon(kategori) {
            let color = '#667eea';

            // Bisa disesuaikan warna berdasarkan kategori
            switch (kategori) {
                case 'Kuliner':
                    color = '#ef4444';
                    break;
                case 'Fashion':
                    color = '#8b5cf6';
                    break;
                case 'Kerajinan':
                    color = '#f59e0b';
                    break;
                case 'Jasa':
                    color = '#10b981';
                    break;
                default:
                    color = '#667eea';
            }

            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                popupAnchor: [0, -30]
            });
        }

        // Simpan semua marker untuk filter
        let allMarkers = [];

        // Tambahkan marker untuk setiap UMKM
        umkmData.forEach(function (umkm) {
            if (umkm.latitude && umkm.longitude) {
                const marker = L.marker([umkm.latitude, umkm.longitude], {
                    icon: getMarkerIcon(umkm.nama_kategori)
                });

                // Popup content
                const popupContent = `
                    <div class="popup-title">${umkm.nama_umkm}</div>
                    <span class="popup-category">${umkm.nama_kategori}</span>
                    <div class="popup-info">
                        <strong>Pemilik:</strong> ${umkm.nama_pemilik}<br>
                        <strong>Alamat:</strong> ${umkm.alamat_lengkap}<br>
                        <strong>Kecamatan:</strong> ${umkm.kecamatan}
                    </div>
                    <div class="popup-actions">
                        <a href="../umkm/detail.php?id=${umkm.id_umkm}" class="popup-btn popup-btn-primary">
                            Detail
                        </a>
                        <a href="../umkm/edit.php?id=${umkm.id_umkm}" class="popup-btn popup-btn-secondary">
                            Edit
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                marker.umkmData = umkm; // Simpan data untuk filter

                markers.addLayer(marker);
                allMarkers.push(marker);
            }
        });

        map.addLayer(markers);

        // Tambahkan legend
        const legend = L.control({ position: 'bottomleft' });
        legend.onAdd = function (map) {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML = `
                <div class="legend-title">Legenda Kategori</div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #ef4444;"></div>
                    Kuliner
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #8b5cf6;"></div>
                    Fashion
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #f59e0b;"></div>
                    Kerajinan
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #10b981;"></div>
                    Jasa
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #667eea;"></div>
                    Lainnya
                </div>
            `;
            return div;
        };
        legend.addTo(map);

        // Fungsi untuk render daftar UMKM
        function renderUmkmList(data) {
            const listContainer = document.getElementById('umkm-list');
            listContainer.innerHTML = '';

            if (data.length === 0) {
                listContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #64748b;">Tidak ada UMKM</div>';
                return;
            }

            data.forEach(function (umkm) {
                const item = document.createElement('div');
                item.className = 'umkm-item';
                item.innerHTML = `
                    <div class="umkm-item-name">${umkm.nama_umkm}</div>
                    <span class="umkm-item-category">${umkm.nama_kategori}</span>
                    <div class="umkm-item-address">${umkm.kecamatan}</div>
                `;

                item.onclick = function () {
                    // Zoom ke marker yang diklik
                    map.setView([umkm.latitude, umkm.longitude], 16);

                    // Buka popup
                    allMarkers.forEach(function (marker) {
                        if (marker.umkmData.id_umkm === umkm.id_umkm) {
                            marker.openPopup();
                        }
                    });
                };

                listContainer.appendChild(item);
            });

            // Update total
            document.getElementById('total-umkm').textContent = data.length;
        }

        // Initial render
        renderUmkmList(umkmData);

        // Filter kategori
        document.getElementById('filter-kategori').addEventListener('change', function () {
            filterUmkm();
        });

        // Search
        document.getElementById('search-umkm').addEventListener('input', function () {
            filterUmkm();
        });

        // Fungsi filter
        function filterUmkm() {
            const kategoriFilter = document.getElementById('filter-kategori').value;
            const searchText = document.getElementById('search-umkm').value.toLowerCase();

            // Filter data
            let filteredData = umkmData.filter(function (umkm) {
                const matchKategori = !kategoriFilter || umkm.id_kategori == kategoriFilter;
                const matchSearch = !searchText ||
                    umkm.nama_umkm.toLowerCase().includes(searchText) ||
                    umkm.nama_pemilik.toLowerCase().includes(searchText);

                return matchKategori && matchSearch;
            });

            // Update markers
            markers.clearLayers();
            filteredData.forEach(function (umkm) {
                if (umkm.latitude && umkm.longitude) {
                    allMarkers.forEach(function (marker) {
                        if (marker.umkmData.id_umkm === umkm.id_umkm) {
                            markers.addLayer(marker);
                        }
                    });
                }
            });

            // Update list
            renderUmkmList(filteredData);
        }
    </script>
</body>

</html>