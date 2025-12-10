<?php
// config/database.php

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_umkm_semarang');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");

// Fungsi untuk membersihkan input (mencegah SQL Injection)
function clean_input($data)
{
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Fungsi untuk mendapatkan koordinat berdasarkan kecamatan
function get_koordinat_kecamatan($kecamatan)
{
    global $conn;
    $kecamatan = clean_input($kecamatan);
    $query = "SELECT latitude, longitude FROM ref_kecamatan WHERE nama_kecamatan = '$kecamatan' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    // Default koordinat Semarang jika tidak ditemukan
    return ['latitude' => -6.9667, 'longitude' => 110.4167];
}
?>