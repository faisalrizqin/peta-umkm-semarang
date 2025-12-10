<?php
// pages/umkm/tambah.php
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../config/database.php';

$error = '';
$success = '';

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
        
        // Insert data
        $query = "INSERT INTO umkm (
                    nama_umkm, nama_pemilik, alamat_lengkap, rt, rw, 
                    kelurahan, kecamatan, id_kategori, deskripsi, 
                    bahan_baku_utama, alat_produksi_utama, latitude, longitude
                  ) VALUES (
                    '$nama_umkm', '$nama_pemilik', '$alamat_lengkap', '$rt', '$rw',
                    '$kelurahan', '$kecamatan', '$id_kategori', '$deskripsi',
                    '$bahan_baku_utama', '$alat_produksi_utama', '$latitude', '$longitude'
                  )";
        
        if (mysqli_query($conn, $query)) {
            $success = "Data UMKM berhasil ditambahkan! Koordinat otomatis: Lat $latitude, Long $longitude";
            // Reset form
            $_POST = array();
        } else {
            $error = "Gagal menambahkan data UMKM: " . mysqli_error($conn);
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
    <title>Tambah UMKM - Peta UMKM Semarang</title>
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
                <h2>Tambah Data UMKM</h2>
                <p>Tambahkan data UMKM baru di Kota Semarang</p>
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
                    <h3>Form Tambah UMKM</h3>
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
                                           placeholder="Contoh: Warung Makan Pak Budi"
                                           value="<?php echo isset($_POST['nama_umkm']) ? $_POST['nama_umkm'] : ''; ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_pemilik">Nama Pemilik <span class="required">*</span></label>
                                    <input type="text" 
                                           id="nama_pemilik" 
                                           name="nama_pemilik" 
                                           class="form-control" 
                                           placeholder="Contoh: Budi Santoso"
                                           value="<?php echo isset($_POST['nama_pemilik']) ? $_POST['nama_pemilik'] : ''; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="id_kategori">Kategori Usaha <span class="required">*</span></label>
                                <select id="id_kategori" name="id_kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>"
                                            <?php echo (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
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
                                          rows="3"
                                          placeholder="Deskripsi singkat tentang usaha..."><?php echo isset($_POST['deskripsi']) ? $_POST['deskripsi'] : ''; ?></textarea>
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
                                          placeholder="Contoh: Jl. Pandanaran No. 123"
                                          required><?php echo isset($_POST['alamat_lengkap']) ? $_POST['alamat_lengkap'] : ''; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="rt">RT <span class="required">*</span></label>
                                    <input type="text" 
                                           id="rt" 
                                           name="rt" 
                                           class="form-control" 
                                           placeholder="001"
                                           maxlength="3"
                                           value="<?php echo isset($_POST['rt']) ? $_POST['rt'] : ''; ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="rw">RW <span class="required">*</span></label>
                                    <input type="text" 
                                           id="rw" 
                                           name="rw" 
                                           class="form-control" 
                                           placeholder="005"
                                           maxlength="3"
                                           value="<?php echo isset($_POST['rw']) ? $_POST['rw'] : ''; ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="kelurahan">Kelurahan <span class="required">*</span></label>
                                    <input type="text" 
                                           id="kelurahan" 
                                           name="kelurahan" 
                                           class="form-control" 
                                           placeholder="Contoh: Pandanaran"
                                           value="<?php echo isset($_POST['kelurahan']) ? $_POST['kelurahan'] : ''; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="kecamatan">Kecamatan <span class="required">*</span></label>
                                <select id="kecamatan" name="kecamatan" class="form-control" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <?php while ($kec = mysqli_fetch_assoc($kecamatan_list)): ?>
                                    <option value="<?php echo $kec['nama_kecamatan']; ?>"
                                            <?php echo (isset($_POST['kecamatan']) && $_POST['kecamatan'] == $kec['nama_kecamatan']) ? 'selected' : ''; ?>>
                                        <?php echo $kec['nama_kecamatan']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="form-text">
                                    ℹ️ Koordinat peta akan otomatis terisi berdasarkan kecamatan yang dipilih
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
                                           placeholder="Contoh: Tepung terigu, gula, telur"
                                           value="<?php echo isset($_POST['bahan_baku_utama']) ? $_POST['bahan_baku_utama'] : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="alat_produksi_utama">Alat Produksi Utama</label>
                                    <input type="text" 
                                           id="alat_produksi_utama" 
                                           name="alat_produksi_utama" 
                                           class="form-control" 
                                           placeholder="Contoh: Oven, mixer, kompor gas"
                                           value="<?php echo isset($_POST['alat_produksi_utama']) ? $_POST['alat_produksi_utama'] : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="submit" class="btn btn-primary">
                                💾 Simpan Data UMKM
                            </button>
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