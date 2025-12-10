<?php
// includes/header.php
?>
<div class="header">
    <div class="header-left">
        <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
        <div class="breadcrumb">
            <span>Pemetaan UMKM</span>
        </div>
    </div>

    <div class="header-right">
        <div class="user-info">
            <span class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></span>
            <span class="user-role">Staff Ahli Walikota</span>
        </div>
        <div class="user-avatar">
            <span>👤</span>
        </div>
    </div>
</div>