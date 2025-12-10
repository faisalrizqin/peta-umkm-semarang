<?php
// pages/umkm/edit.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

$error = '';
$success = '';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = clean_input($_GET['id']);

// Ambil data UMKM
$query = "SELECT * FROM umkm WHERE id_umkm = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($result);

// Proses form
if (isset($_POST['submit'])) {
    $nama_umkm = clean_input($_POST['nama_umkm']);
    $nama_pemilik = clean_input($_POST['nama_pemilik']);
    $alamat_lengkap = clean_input($_POST['alamat_lengkap']);
    $rt = clean_input($_POST['rt']);
    $rw = clean_input($_POST['rw']);
    $kelurahan = clean_input($_POST['kelurahan']);
    $kecamatan = clean_input($_POST['kecamatan']);
    $id_kategori = clean_input($_POST['id_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $bahan_baku_utama = clean_input($_POST['bahan_baku_utama']);
    $alat_produksi_utama = clean_input($_POST['alat_produksi_utama']);
    
    // Validasi
    if (empty($nama_umkm) || empty($nama_pemilik) || empty($alamat_lengkap) || 
        empty($rt) || empty($rw) || empty($kelurahan) || empty($kecamatan) || empty($id_kategori)) {
        $error = "Semua field wajib harus diisi!";
    } else {
        // Ambil koordinat berdasarkan kecamatan
        $koordinat = get_koordinat_kecamatan($kecamatan);
        $latitude = $koordinat['latitude'];
        $longitude = $koordinat['longitude'];
        
        // Update data
        $query = "UPDATE umkm SET 
                  nama_umkm = '$nama_umkm',
                  nama_pemilik = '$nama_pemilik',
                  alamat_lengkap = '$alamat_lengkap',
                  rt = '$rt',
                  rw = '$rw',
                  kelurahan = '$kelurahan',
                  kecamatan = '$kecamatan',
                  id_kategori = '$id_kategori',
                  deskripsi = '$deskripsi',
                  bahan_baku_utama = '$bahan_baku_utama',
                  alat_produksi_utama = '$alat_produksi_utama',
                  latitude = '$latitude',
                  longitude = '$longitude'
                  WHERE id_umkm = '$id'";
        
        if (mysqli_query($conn, $query)) {
            $success = "Data UMKM berhasil diupdate! Koordinat: Lat $latitude, Long $longitude";
            // Refresh data
            $data = array_merge($data, $_POST);
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
        } else {
            $error = "Gagal mengupdate data UMKM: " . mysqli_error($conn);
        }
    }
}

// Ambil data kategori
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Ambil daftar kecamatan
$kecamatan_list = mysqli_query($conn, "SELECT * FROM ref_kecamatan ORDER BY nama_kecamatan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit UMKM - <?php echo $data['nama_umkm']; ?></title>
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
                <h2>Edit Data UMKM</h2>
                <p>Ubah data UMKM di Kota Semarang</p>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Form Edit UMKM</h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="">
                        <!-- Informasi Umum -->
                        <div class="form-section">
                            <h4 class="form-section-title">📋 Informasi Umum</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nama_umkm">Nama UMKM <span class="required">*</span></label>
                                    <input type="text" 
                                           id="nama_umkm" 
                                           name="nama_umkm" 
                                           class="form-control" 
                                           value="<?php echo $data['nama_umkm']; ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_pemilik">Nama Pemilik <span class="required">*</span></label>
                                    <input type="text" 
                                           id="nama_pemilik" 
                                           name="nama_pemilik" 
                                           class="form-control" 
                                           value="<?php echo $data['nama_pemilik']; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="id_kategori">Kategori Usaha <span class="required">*</span></label>
                                <select id="id_kategori" name="id_kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>"
                                            <?php echo ($data['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                        <?php echo $kat['nama_kategori']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Usaha</label>
                                <textarea id="deskripsi" 
                                          name="deskripsi" 
                                          class="form-control" 
                                          rows="3"><?php echo $data['deskripsi']; ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Alamat -->
                        <div class="form-section">
                            <h4 class="form-section-title">📍 Alamat Lengkap</h4>
                            
                            <div class="form-group">
                                <label for="alamat_lengkap">Alamat Lengkap <span class="required">*</span></label>
                                <textarea id="alamat_lengkap" 
                                          name="alamat_lengkap" 
                                          class="form-control" 
                                          rows="2"
                                          required><?php echo $data['alamat_lengkap']; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="rt">RT <span class="required">*</span></label>
                                    <input type="text" 
                                           id="rt" 
                                           name="rt" 
                                           class="form-control" 
                                           maxlength="3"
                                           value="<?php echo $data['rt']; ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="rw">RW <span class="required">*</span></label>
                                    <input type="text" 
                                           id="rw" 
                                           name="rw" 
                                           class="form-control" 
                                           maxlength="3"
                                           value="<?php echo $data['rw']; ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="kelurahan">Kelurahan <span class="required">*</span></label>
                                    <input type="text" 
                                           id="kelurahan" 
                                           name="kelurahan" 
                                           class="form-control" 
                                           value="<?php echo $data['kelurahan']; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="kecamatan">Kecamatan <span class="required">*</span></label>
                                <select id="kecamatan" name="kecamatan" class="form-control" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <?php while ($kec = mysqli_fetch_assoc($kecamatan_list)): ?>
                                    <option value="<?php echo $kec['nama_kecamatan']; ?>"
                                            <?php echo ($data['kecamatan'] == $kec['nama_kecamatan']) ? 'selected' : ''; ?>>
                                        <?php echo $kec['nama_kecamatan']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="form-text">
                                    ℹ️ Koordinat peta akan diupdate otomatis jika kecamatan diubah
                                </small>
                            </div>
                        </div>
                        
                        <!-- Produksi -->
                        <div class="form-section">
                            <h4 class="form-section-title">🏭 Informasi Produksi</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bahan_baku_utama">Bahan Baku Utama</label>
                                    <input type="text" 
                                           id="bahan_baku_utama" 
                                           name="bahan_baku_utama" 
                                           class="form-control" 
                                           value="<?php echo $data['bahan_baku_utama']; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="alat_produksi_utama">Alat Produksi Utama</label>
                                    <input type="text" 
                                           id="alat_produksi_utama" 
                                           name="alat_produksi_utama" 
                                           class="form-control" 
                                           value="<?php echo $data['alat_produksi_utama']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="submit" class="btn btn-primary">
                                💾 Update Data UMKM
                            </button>
                            <a href="detail.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                                👁️ Lihat Detail
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                ↩️ Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/script.js"></script>
</body>
</html>