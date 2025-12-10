<?php
// pages/laporan/export_excel.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

// Ambil filter
$filter_kategori = isset($_GET['kategori']) ? clean_input($_GET['kategori']) : '';
$filter_kecamatan = isset($_GET['kecamatan']) ? clean_input($_GET['kecamatan']) : '';

// Query
$where = "WHERE 1=1";
if ($filter_kategori) {
    $where .= " AND u.id_kategori = '$filter_kategori'";
}
if ($filter_kecamatan) {
    $where .= " AND u.kecamatan = '$filter_kecamatan'";
}

$query = "SELECT u.*, k.nama_kategori 
          FROM umkm u 
          LEFT JOIN kategori k ON u.id_kategori = k.id_kategori 
          $where
          ORDER BY u.nama_umkm ASC";
$result = mysqli_query($conn, $query);

// Set header untuk download Excel
$filename = "Laporan_UMKM_Semarang_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Output Excel
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<xml>';
echo '<x:ExcelWorkbook>';
echo '<x:ExcelWorksheets>';
echo '<x:ExcelWorksheet>';
echo '<x:Name>Laporan UMKM</x:Name>';
echo '<x:WorksheetOptions>';
echo '<x:Print>';
echo '<x:ValidPrinterInfo/>';
echo '</x:Print>';
echo '</x:WorksheetOptions>';
echo '</x:ExcelWorksheet>';
echo '</x:ExcelWorksheets>';
echo '</x:ExcelWorkbook>';
echo '</xml>';
echo '</head>';
echo '<body>';

echo '<h2 style="text-align: center;">LAPORAN DATA UMKM KOTA SEMARANG</h2>';
echo '<p style="text-align: center;">Tanggal Export: ' . date('d F Y') . '</p>';

if ($filter_kategori || $filter_kecamatan) {
    echo '<p style="text-align: center;">';
    if ($filter_kategori) {
        $kat_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$filter_kategori'"));
        echo 'Filter Kategori: <strong>' . $kat_name['nama_kategori'] . '</strong> ';
    }
    if ($filter_kecamatan) {
        echo 'Filter Kecamatan: <strong>' . $filter_kecamatan . '</strong>';
    }
    echo '</p>';
}

echo '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
echo '<thead>';
echo '<tr style="background-color: #667eea; color: white; font-weight: bold;">';
echo '<th>No</th>';
echo '<th>Nama UMKM</th>';
echo '<th>Nama Pemilik</th>';
echo '<th>Kategori</th>';
echo '<th>Alamat Lengkap</th>';
echo '<th>RT</th>';
echo '<th>RW</th>';
echo '<th>Kelurahan</th>';
echo '<th>Kecamatan</th>';
echo '<th>Deskripsi</th>';
echo '<th>Bahan Baku Utama</th>';
echo '<th>Alat Produksi Utama</th>';
echo '<th>Latitude</th>';
echo '<th>Longitude</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $no++ . '</td>';
    echo '<td>' . $row['nama_umkm'] . '</td>';
    echo '<td>' . $row['nama_pemilik'] . '</td>';
    echo '<td>' . $row['nama_kategori'] . '</td>';
    echo '<td>' . $row['alamat_lengkap'] . '</td>';
    echo '<td>' . $row['rt'] . '</td>';
    echo '<td>' . $row['rw'] . '</td>';
    echo '<td>' . $row['kelurahan'] . '</td>';
    echo '<td>' . $row['kecamatan'] . '</td>';
    echo '<td>' . $row['deskripsi'] . '</td>';
    echo '<td>' . $row['bahan_baku_utama'] . '</td>';
    echo '<td>' . $row['alat_produksi_utama'] . '</td>';
    echo '<td>' . $row['latitude'] . '</td>';
    echo '<td>' . $row['longitude'] . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

echo '<br><br>';
echo '<p style="text-align: right;">';
echo 'Semarang, ' . date('d F Y') . '<br><br><br><br>';
echo '<strong>' . $_SESSION['nama_lengkap'] . '</strong><br>';
echo 'Staff Ahli Walikota Semarang';
echo '</p>';

echo '</body>';
echo '</html>';
?>