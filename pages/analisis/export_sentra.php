<?php
// pages/analisis/export_sentra.php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

$cluster_id = clean_input($_GET['id']);
$type = clean_input($_GET['type']);

// Ambil data sentra
$query_sentra = "SELECT * FROM cluster_sentra WHERE id_cluster = '$cluster_id'";
$sentra = mysqli_fetch_assoc(mysqli_query($conn, $query_sentra));

// Ambil UMKM dalam cluster
$query_umkm = "SELECT u.*, k.nama_kategori 
               FROM umkm u 
               LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
               WHERE u.cluster_id = '$cluster_id'
               ORDER BY u.nama_umkm ASC";
$result = mysqli_query($conn, $query_umkm);

if ($type == 'excel') {
    // Export Excel
    $filename = "Sentra_" . str_replace(' ', '_', $sentra['nama_sentra']) . "_" . date('Y-m-d') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>';
    echo '<body>';

    echo '<h2>DAFTAR UMKM - ' . strtoupper($sentra['nama_sentra']) . '</h2>';
    echo '<p>Total UMKM: ' . $sentra['jumlah_umkm'] . '</p>';
    echo '<p>Tanggal Export: ' . date('d F Y') . '</p>';
    echo '<br>';

    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<thead><tr style="background-color: #667eea; color: white;">';
    echo '<th>No</th><th>Nama UMKM</th><th>Pemilik</th><th>Kategori</th>';
    echo '<th>Alamat</th><th>RT/RW</th><th>Kelurahan</th><th>Kecamatan</th>';
    echo '<th>Bahan Baku</th><th>Alat Produksi</th><th>Koordinat</th>';
    echo '</tr></thead><tbody>';

    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . $row['nama_umkm'] . '</td>';
        echo '<td>' . $row['nama_pemilik'] . '</td>';
        echo '<td>' . $row['nama_kategori'] . '</td>';
        echo '<td>' . $row['alamat_lengkap'] . '</td>';
        echo '<td>' . $row['rt'] . '/' . $row['rw'] . '</td>';
        echo '<td>' . $row['kelurahan'] . '</td>';
        echo '<td>' . $row['kecamatan'] . '</td>';
        echo '<td>' . $row['bahan_baku_utama'] . '</td>';
        echo '<td>' . $row['alat_produksi_utama'] . '</td>';
        echo '<td>' . $row['latitude'] . ', ' . $row['longitude'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</body></html>';

} else {
    // Export PDF (HTML)
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>Laporan Sentra - PDF</title>
        <style>
            body {
                font-family: Arial;
                margin: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px solid #667eea;
                padding-bottom: 15px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
            }

            th {
                background: #667eea;
                color: white;
                padding: 8px;
                text-align: left;
            }

            td {
                padding: 6px;
                border: 1px solid #ddd;
            }

            tr:nth-child(even) {
                background: #f8fafc;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h2>DAFTAR UMKM - <?php echo strtoupper($sentra['nama_sentra']); ?></h2>
            <p>Total UMKM: <?php echo $sentra['jumlah_umkm']; ?> | Tanggal: <?php echo date('d F Y'); ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="18%">Nama UMKM</th>
                    <th width="15%">Pemilik</th>
                    <th width="10%">Kategori</th>
                    <th width="12%">Kecamatan</th>
                    <th width="20%">Bahan Baku</th>
                    <th width="20%">Alat Produksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_umkm']; ?></td>
                        <td><?php echo $row['nama_pemilik']; ?></td>
                        <td><?php echo $row['nama_kategori']; ?></td>
                        <td><?php echo $row['kecamatan']; ?></td>
                        <td><?php echo $row['bahan_baku_utama']; ?></td>
                        <td><?php echo $row['alat_produksi_utama']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <script>window.onload = function () { window.print(); }</script>
    </body>

    </html>
    <?php
}
?>