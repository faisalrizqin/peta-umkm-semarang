<?php
// pages/laporan/export_pdf.php
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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan UMKM - PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
        }

        .header h2 {
            margin: 5px 0;
            color: #667eea;
            font-size: 18px;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table th {
            background: #667eea;
            color: white;
            padding: 10px 5px;
            text-align: left;
            border: 1px solid #667eea;
        }

        table td {
            padding: 8px 5px;
            border: 1px solid #e5e7eb;
        }

        table tr:nth-child(even) {
            background: #f8fafc;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .footer p {
            margin: 5px 0;
        }

        @media print {
            body {
                margin: 0;
            }

            @page {
                size: A4 landscape;
                margin: 15mm;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN DATA UMKM</h1>
        <h2>KOTA SEMARANG</h2>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> <?php echo date('d F Y'); ?></p>
        <?php if ($filter_kategori || $filter_kecamatan): ?>
            <p>
                <?php if ($filter_kategori):
                    $kat_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$filter_kategori'"));
                    ?>
                    <strong>Filter Kategori:</strong> <?php echo $kat_name['nama_kategori']; ?>
                <?php endif; ?>

                <?php if ($filter_kecamatan): ?>
                    | <strong>Filter Kecamatan:</strong> <?php echo $filter_kecamatan; ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama UMKM</th>
                <th width="12%">Pemilik</th>
                <th width="10%">Kategori</th>
                <th width="20%">Alamat</th>
                <th width="10%">Kecamatan</th>
                <th width="15%">Bahan Baku</th>
                <th width="15%">Alat Produksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama_umkm']; ?></td>
                        <td><?php echo $row['nama_pemilik']; ?></td>
                        <td><?php echo $row['nama_kategori']; ?></td>
                        <td>
                            <?php echo $row['alamat_lengkap']; ?><br>
                            <small>RT <?php echo $row['rt']; ?>/RW <?php echo $row['rw']; ?>,
                                <?php echo $row['kelurahan']; ?></small>
                        </td>
                        <td><?php echo $row['kecamatan']; ?></td>
                        <td><?php echo $row['bahan_baku_utama']; ?></td>
                        <td><?php echo $row['alat_produksi_utama']; ?></td>
                    </tr>
                    <?php
                endwhile;
            else:
                ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        Tidak ada data UMKM
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Semarang, <?php echo date('d F Y'); ?></p>
        <br><br><br>
        <p><strong><?php echo $_SESSION['nama_lengkap']; ?></strong></p>
        <p>Staff Ahli Walikota Semarang</p>
    </div>

    <script>
        // Auto print saat halaman dibuka
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>