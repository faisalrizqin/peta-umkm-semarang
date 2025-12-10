<?php
// includes/sidebar.php

// Deteksi lokasi file saat ini untuk menentukan path yang benar
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Tentukan root path berdasarkan lokasi file
if ($current_file == 'dashboard.php') {
    // Jika di root (dashboard.php)
    $root_path = '';
    $assets_path = 'assets/';
} else {
    // Jika di dalam folder pages
    $root_path = '../../';
    $assets_path = '../../assets/';
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>🗺️ UMKM Semarang</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo $root_path; ?>dashboard.php"
            class="nav-item <?php echo ($current_file == 'dashboard.php') ? 'active' : ''; ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <a href="<?php echo $root_path; ?>pages/kategori/index.php"
            class="nav-item <?php echo ($current_dir == 'kategori') ? 'active' : ''; ?>">
            <span class="nav-icon">📁</span>
            <span class="nav-text">Kategori UMKM</span>
        </a>

        <a href="<?php echo $root_path; ?>pages/umkm/index.php"
            class="nav-item <?php echo ($current_dir == 'umkm') ? 'active' : ''; ?>">
            <span class="nav-icon">🏪</span>
            <span class="nav-text">Data UMKM</span>
        </a>

        <a href="<?php echo $root_path; ?>pages/peta/index.php"
            class="nav-item <?php echo ($current_dir == 'peta') ? 'active' : ''; ?>">
            <span class="nav-icon">🗺️</span>
            <span class="nav-text">Peta UMKM</span>
        </a>

        <a href="<?php echo $root_path; ?>pages/laporan/index.php"
            class="nav-item <?php echo ($current_dir == 'laporan') ? 'active' : ''; ?>">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Laporan</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?php echo $root_path; ?>logout.php" class="nav-item nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>