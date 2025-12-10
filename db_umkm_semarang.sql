-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 12:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_umkm_semarang`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Kuliner', 'Usaha makanan dan minuman', '2025-12-10 23:15:55'),
(2, 'Fashion', 'Usaha pakaian dan aksesoris', '2025-12-10 23:15:55'),
(3, 'Kerajinan', 'Usaha kerajinan tangan dan seni', '2025-12-10 23:15:55'),
(4, 'Jasa', 'Usaha jasa dan layanan', '2025-12-10 23:15:55'),
(5, 'Furniture', 'Usaha mebel dan interior', '2025-12-10 23:15:55'),
(6, 'Pertanian', 'Usaha olahan hasil pertanian', '2025-12-10 23:15:55'),
(7, 'Kecantikan', 'Usaha kosmetik dan perawatan', '2025-12-10 23:15:55'),
(8, 'Teknologi', 'Usaha IT dan digital', '2025-12-10 23:15:55');

-- --------------------------------------------------------

--
-- Table structure for table `ref_kecamatan`
--

CREATE TABLE `ref_kecamatan` (
  `id` int(11) NOT NULL,
  `nama_kecamatan` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ref_kecamatan`
--

INSERT INTO `ref_kecamatan` (`id`, `nama_kecamatan`, `latitude`, `longitude`) VALUES
(1, 'Semarang Tengah', -6.98330000, 110.41670000),
(2, 'Semarang Utara', -6.95330000, 110.41670000),
(3, 'Semarang Timur', -6.99000000, 110.44170000),
(4, 'Semarang Selatan', -7.01670000, 110.42500000),
(5, 'Semarang Barat', -6.98330000, 110.38330000),
(6, 'Gayamsari', -6.96670000, 110.45000000),
(7, 'Genuk', -6.93330000, 110.48330000),
(8, 'Pedurungan', -7.01670000, 110.45830000),
(9, 'Semarang Candisari', -7.01670000, 110.40830000),
(10, 'Gajahmungkur', -7.04170000, 110.40000000),
(11, 'Tembalang', -7.05000000, 110.44170000),
(12, 'Banyumanik', -7.06670000, 110.43330000),
(13, 'Gunungpati', -7.08330000, 110.37500000),
(14, 'Mijen', -7.05830000, 110.31670000),
(15, 'Ngaliyan', -7.03330000, 110.35000000),
(16, 'Tugu', -6.91670000, 110.36670000);

-- --------------------------------------------------------

--
-- Table structure for table `umkm`
--

CREATE TABLE `umkm` (
  `id_umkm` int(11) NOT NULL,
  `nama_umkm` varchar(200) NOT NULL,
  `nama_pemilik` varchar(150) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `rt` varchar(5) NOT NULL,
  `rw` varchar(5) NOT NULL,
  `kelurahan` varchar(100) NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `bahan_baku_utama` varchar(200) DEFAULT NULL,
  `alat_produksi_utama` varchar(200) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `umkm`
--

INSERT INTO `umkm` (`id_umkm`, `nama_umkm`, `nama_pemilik`, `alamat_lengkap`, `rt`, `rw`, `kelurahan`, `kecamatan`, `id_kategori`, `deskripsi`, `bahan_baku_utama`, `alat_produksi_utama`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Warung Makan Sederhana', 'Budi Santoso', 'Jl. Pandanaran No. 45', '001', '003', 'Pandanaran', 'Semarang Tengah', 1, 'Warung makan prasmanan dengan menu lengkap', 'Beras, sayuran, daging ayam', 'Kompor gas, rice cooker, panci besar', -6.98330000, 110.41670000, '2025-12-10 23:16:20'),
(2, 'Bakso Sapi Pak Kumis', 'Ahmad Yani', 'Jl. Pemuda No. 123', '002', '005', 'Sekayu', 'Semarang Tengah', 1, 'Bakso sapi asli dengan kuah gurih', 'Daging sapi, tepung tapioka', 'Mesin giling daging, kompor gas', -6.98500000, 110.42000000, '2025-12-10 23:16:20'),
(3, 'Es Teh Poci Legendaris', 'Siti Nurhaliza', 'Jl. Gajahmada No. 78', '003', '002', 'Kembangsari', 'Semarang Tengah', 1, 'Minuman teh poci khas Semarang', 'Teh hitam, gula pasir', 'Poci tanah liat, kompor', -6.98000000, 110.41500000, '2025-12-10 23:16:20'),
(4, 'Soto Ayam Lamongan Bu Sri', 'Sri Rahayu', 'Jl. MT Haryono No. 234', '004', '008', 'Lamper Kidul', 'Semarang Selatan', 1, 'Soto ayam bumbu khas Lamongan', 'Ayam kampung, bumbu rempah', 'Kompor gas, panci besar', -7.01670000, 110.42500000, '2025-12-10 23:16:20'),
(5, 'Lumpia Semarang Gang Lombok', 'Tan Kie Lok', 'Jl. Gang Lombok No. 11', '005', '001', 'Purwodinatan', 'Semarang Tengah', 1, 'Lumpia basah dan goreng khas Semarang', 'Rebung, telur, udang', 'Wajan, penggorengan', -6.98800000, 110.41800000, '2025-12-10 23:16:20'),
(6, 'Wingko Babat Cap Kereta Api', 'Wahyu Budiono', 'Jl. Yos Sudarso No. 456', '001', '004', 'Tanjung Mas', 'Semarang Utara', 1, 'Wingko babat kelapa asli', 'Kelapa parut, tepung ketan', 'Oven tradisional, cetakan', -6.95330000, 110.41670000, '2025-12-10 23:16:20'),
(7, 'Nasi Goreng Kambing Kebon Siri', 'Hendra Gunawan', 'Jl. Kaligawe No. 89', '006', '003', 'Kaligawe', 'Gayamsari', 1, 'Nasi goreng kambing spesial', 'Daging kambing, nasi putih', 'Wajan besar, kompor gas', -6.96670000, 110.45000000, '2025-12-10 23:16:20'),
(8, 'Kue Kering Ibu Ratna', 'Ratna Wijaya', 'Jl. Veteran No. 67', '002', '006', 'Sekayu', 'Semarang Tengah', 1, 'Aneka kue kering untuk lebaran', 'Tepung terigu, mentega, gula', 'Oven, mixer', -6.98400000, 110.41900000, '2025-12-10 23:16:20'),
(9, 'Pecel Lele Lela Lapar', 'Andi Prasetyo', 'Jl. Majapahit No. 321', '007', '002', 'Kembangsari', 'Semarang Tengah', 1, 'Pecel lele sambal cobek', 'Ikan lele, sambal, lalapan', 'Penggorengan, kompor', -6.98100000, 110.41600000, '2025-12-10 23:16:20'),
(10, 'Roti Bakar Eddy', 'Eddy Susanto', 'Jl. Pandanaran No. 111', '003', '007', 'Mugassari', 'Semarang Selatan', 1, 'Roti bakar dengan berbagai topping', 'Roti tawar, mentega, coklat', 'Pemanggang roti, kompor', -7.01800000, 110.42300000, '2025-12-10 23:16:20'),
(11, 'Dawet Ayu Telasih', 'Suparmi', 'Jl. Erlangga No. 55', '008', '004', 'Wonosari', 'Ngaliyan', 1, 'Es dawet dengan santan kelapa', 'Tepung beras, gula merah, kelapa', 'Panci, cetakan dawet', -7.03330000, 110.35000000, '2025-12-10 23:16:20'),
(12, 'Tahu Gimbal Pak Jamal', 'Jamal Mubarok', 'Jl. Sultan Agung No. 77', '001', '002', 'Karangrejo', 'Gajahmungkur', 1, 'Tahu gimbal khas Semarang', 'Tahu putih, kol, udang', 'Penggorengan, wajan', -7.04170000, 110.40000000, '2025-12-10 23:16:20'),
(13, 'Bandeng Presto Bu Winda', 'Winda Kartika', 'Jl. Dr Cipto No. 90', '004', '001', 'Sampangan', 'Gajahmungkur', 1, 'Bandeng presto tanpa duri', 'Ikan bandeng segar', 'Panci presto, kompor gas', -7.04000000, 110.40200000, '2025-12-10 23:16:20'),
(14, 'Martabak Manis Terang Bulan', 'Liem Tjung', 'Jl. Gajah Mada No. 44', '002', '003', 'Kranggan', 'Semarang Tengah', 1, 'Martabak manis dengan topping lengkap', 'Tepung terigu, telur, gula', 'Wajan martabak, kompor', -6.98200000, 110.41750000, '2025-12-10 23:16:20'),
(15, 'Lotek Tahu Petis Pak Broto', 'Broto Suwarno', 'Jl. Siliwangi No. 22', '005', '005', 'Pleburan', 'Semarang Selatan', 1, 'Lotek sayur dengan petis udang', 'Sayuran segar, tahu, petis', 'Cobek batu, panci kukus', -7.01500000, 110.42700000, '2025-12-10 23:16:20'),
(16, 'Batik Sutra Indah', 'Dewi Kusuma', 'Jl. Pemuda No. 200', '003', '004', 'Bugangan', 'Semarang Timur', 2, 'Batik tulis dan cap khas Semarang', 'Kain sutra, malam batik, pewarna', 'Canting, kompor malam', -6.99000000, 110.44170000, '2025-12-10 23:16:20'),
(17, 'Konveksi Baju Anak Ceria', 'Rina Anggraini', 'Jl. Kaligarang No. 33', '006', '002', 'Krobokan', 'Semarang Barat', 2, 'Produksi baju anak usia 1-10 tahun', 'Kain katun, benang jahit', 'Mesin jahit, obras', -6.98330000, 110.38330000, '2025-12-10 23:16:20'),
(18, 'Tas Kulit Handmade', 'Agus Hermawan', 'Jl. Menoreh No. 88', '002', '008', 'Sambirejo', 'Gayamsari', 2, 'Tas kulit asli buatan tangan', 'Kulit sapi, benang kulit', 'Mesin jahit kulit, alat potong', -6.96800000, 110.44800000, '2025-12-10 23:16:20'),
(19, 'Hijab Syari Berkah', 'Fatimah Azzahra', 'Jl. Sendangguwo No. 45', '007', '006', 'Tembalang', 'Tembalang', 2, 'Hijab dan gamis syari modern', 'Kain wolfis, kain jersey', 'Mesin jahit, cutting table', -7.05000000, 110.44170000, '2025-12-10 23:16:20'),
(20, 'Sepatu Rajut Kreatif', 'Maya Anggun', 'Jl. Kelud No. 123', '004', '003', 'Petompon', 'Gajahmungkur', 2, 'Sepatu rajut untuk bayi dan anak', 'Benang rajut, sol sepatu', 'Jarum rajut, lem sepatu', -7.04300000, 110.40100000, '2025-12-10 23:16:20'),
(21, 'Kaos Sablon Custom', 'Bima Sakti', 'Jl. Durian No. 77', '001', '007', 'Tandang', 'Tembalang', 2, 'Sablon kaos dengan desain custom', 'Kaos polos, tinta sablon', 'Mesin sablon, screen', -7.05200000, 110.44000000, '2025-12-10 23:16:20'),
(22, 'Aksesoris Wanita Cantik', 'Diah Permata', 'Jl. Mataram No. 56', '008', '001', 'Purwosari', 'Semarang Utara', 2, 'Bros, kalung, gelang handmade', 'Manik-manik, kawat', 'Tang, gunting, lem tembak', -6.95500000, 110.41800000, '2025-12-10 23:16:20'),
(23, 'Pakaian Adat Jawa', 'Pak Sumardi', 'Jl. Hasanudin No. 99', '003', '005', 'Kauman', 'Semarang Tengah', 2, 'Kebaya dan beskap untuk acara adat', 'Kain tradisional, payet', 'Mesin jahit, mesin bordir', -6.98600000, 110.41400000, '2025-12-10 23:16:20'),
(24, 'Mukena Bordir Anggun', 'Laila Sari', 'Jl. Cendrawasih No. 34', '005', '004', 'Tandang', 'Tembalang', 2, 'Mukena dengan bordir tangan', 'Kain katun jepang, benang sulam', 'Mesin bordir, pembidangan', -7.05100000, 110.43900000, '2025-12-10 23:16:20'),
(25, 'Daster Kencana', 'Yuni Rahayu', 'Jl. Brigjend Katamso No. 12', '002', '002', 'Wates', 'Ngaliyan', 2, 'Daster batik dan polos untuk rumahan', 'Kain katun, kancing', 'Mesin jahit portable', -7.03400000, 110.35200000, '2025-12-10 23:16:20'),
(26, 'Keramik Hias Artistik', 'Joko Susilo', 'Jl. Plombokan No. 66', '006', '008', 'Plombokan', 'Semarang Utara', 3, 'Vas bunga dan hiasan keramik', 'Tanah liat, glasur', 'Mesin putar, tungku pembakaran', -6.95400000, 110.41500000, '2025-12-10 23:16:20'),
(27, 'Anyaman Bambu Sejahtera', 'Sutrisno', 'Jl. Raya Mijen No. 111', '001', '003', 'Mijen', 'Mijen', 3, 'Keranjang, tampah, dan dekorasi bambu', 'Bambu apus, rotan', 'Pisau anyam, gergaji', -7.05830000, 110.31670000, '2025-12-10 23:16:20'),
(28, 'Souvenir Pernikahan Indah', 'Linda Wijayanti', 'Jl. Ngesrep No. 88', '007', '007', 'Ngesrep', 'Banyumanik', 3, 'Souvenir unik untuk acara pernikahan', 'Kardus, pita, aksesoris', 'Gunting, lem, cutter', -7.06670000, 110.43330000, '2025-12-10 23:16:20'),
(29, 'Patung Kayu Jati', 'Karyono', 'Jl. Gunungpati No. 45', '004', '002', 'Sadeng', 'Gunungpati', 3, 'Patung kayu jati ukiran tangan', 'Kayu jati, pernis', 'Pahat, mesin amplas', -7.08330000, 110.37500000, '2025-12-10 23:16:20'),
(30, 'Lilin Aromaterapi', 'Mega Pratiwi', 'Jl. Jangli No. 23', '002', '006', 'Jangli', 'Tembalang', 3, 'Lilin aromaterapi dengan wangi alami', 'Parafin, minyak esensial', 'Cetakan, kompor', -7.05300000, 110.43800000, '2025-12-10 23:16:20'),
(31, 'Miniatur Kapal Layar', 'Hadi Purnomo', 'Jl. Kaligawe Raya No. 234', '008', '005', 'Terboyo Kulon', 'Genuk', 3, 'Miniatur kapal dari kayu', 'Kayu meranti, cat', 'Gergaji mini, kuas', -6.93330000, 110.48330000, '2025-12-10 23:16:20'),
(32, 'Rajutan Selimut Hangat', 'Ibu Sariyem', 'Jl. Wolter Monginsidi No. 67', '003', '001', 'Pedalangan', 'Banyumanik', 3, 'Selimut dan boneka rajutan', 'Benang wol, dakron', 'Jarum rajut, hakken', -7.06400000, 110.43100000, '2025-12-10 23:16:20'),
(33, 'Lampu Hias Unik', 'Eko Prasetyo', 'Jl. Bulusan No. 99', '005', '003', 'Bulusan', 'Tembalang', 3, 'Lampu hias dari bahan daur ulang', 'Bambu, kawat, lampu LED', 'Tang, bor, lem tembak', -7.05400000, 110.43700000, '2025-12-10 23:16:20'),
(34, 'Kerajinan Batu Alam', 'Bambang Setiawan', 'Jl. Simongan No. 77', '001', '004', 'Simongan', 'Semarang Barat', 3, 'Hiasan dari batu kali dan batu alam', 'Batu kali, batu andesit', 'Gerinda, lem kuat', -6.98500000, 110.38100000, '2025-12-10 23:16:20'),
(35, 'Boneka Flanel Lucu', 'Tuti Handayani', 'Jl. Puri Anjasmoro No. 12', '006', '006', 'Tawangsari', 'Semarang Barat', 3, 'Boneka karakter dari kain flanel', 'Kain flanel, dakron', 'Jarum, benang, lem', -6.98600000, 110.37900000, '2025-12-10 23:16:20'),
(36, 'Jasa Cuci Motor Bersih', 'Rudi Hartono', 'Jl. Setiabudi No. 55', '007', '002', 'Srondol Kulon', 'Banyumanik', 4, 'Cuci motor dan mobil profesional', 'Sabun cuci, wax, semir ban', 'Kompresor, selang air', -7.06200000, 110.42900000, '2025-12-10 23:16:20'),
(37, 'Salon Kecantikan Anggun', 'Tika Damayanti', 'Jl. Pattimura No. 34', '004', '007', 'Petompon', 'Gajahmungkur', 4, 'Potong rambut, creambath, smoothing', 'Shampoo, vitamin rambut, cat', 'Gunting, catok, hair dryer', -7.04400000, 110.39900000, '2025-12-10 23:16:20'),
(38, 'Bengkel Las Jaya Abadi', 'Sugiarto', 'Jl. Setiabudi Selatan No. 88', '002', '004', 'Gedawang', 'Banyumanik', 4, 'Las listrik dan fabrikasi besi', 'Elektroda las, besi', 'Mesin las, gerinda', -7.06300000, 110.42700000, '2025-12-10 23:16:20'),
(39, 'Fotocopy dan Print Digital', 'Anita Sari', 'Jl. Prof Sudarto No. 123', '008', '008', 'Tembalang', 'Tembalang', 4, 'Jasa fotocopy, print, jilid', 'Kertas, tinta printer', 'Mesin fotocopy, printer', -7.04900000, 110.43500000, '2025-12-10 23:16:20'),
(40, 'Service Elektronik Cepat', 'Teguh Santoso', 'Jl. Kelud Raya No. 45', '003', '003', 'Sambirejo', 'Gayamsari', 4, 'Service TV, kulkas, mesin cuci', 'Komponen elektronik, solder', 'Multitester, solder, toolkit', -6.96900000, 110.44700000, '2025-12-10 23:16:20'),
(41, 'Les Privat Matematika', 'Dra. Wulandari', 'Jl. Nginden No. 22', '001', '005', 'Srondol Wetan', 'Banyumanik', 4, 'Bimbingan belajar SD-SMA', 'Buku pelajaran, alat tulis', 'Papan tulis, proyektor', -7.06100000, 110.43100000, '2025-12-10 23:16:20'),
(42, 'Catering Prasmanan Lezat', 'Hj. Aminah', 'Jl. Pamularsih No. 66', '005', '001', 'Manyaran', 'Semarang Barat', 4, 'Katering untuk acara dan harian', 'Bahan makanan segar', 'Kompor gas, panci besar, rice cooker', -6.98700000, 110.37700000, '2025-12-10 23:16:20'),
(43, 'Desain Grafis Creative', 'Yoga Pratama', 'Jl. Prof Hamka No. 99', '006', '004', 'Ngaliyan', 'Ngaliyan', 4, 'Jasa desain logo, banner, undangan', 'Lisensi software desain', 'Laptop, tablet grafis', -7.03200000, 110.35100000, '2025-12-10 23:16:20'),
(44, 'Mebel Jati Makmur', 'Pak Mulyono', 'Jl. Raya Boja No. 77', '002', '007', 'Nongkosawit', 'Gunungpati', 5, 'Furniture kayu jati berkualitas', 'Kayu jati, pernis', 'Gergaji, ketam, amplas', -7.08500000, 110.37300000, '2025-12-10 23:16:20'),
(45, 'Kursi Rotan Modern', 'Sunarto', 'Jl. Kedungmundu No. 44', '007', '003', 'Kedungmundu', 'Tembalang', 5, 'Kursi dan meja rotan sintetis', 'Rotan sintetis, besi hollow', 'Mesin potong, las', -7.05600000, 110.43300000, '2025-12-10 23:16:20'),
(46, 'Lemari Custom Design', 'Riyanto', 'Jl. Sendangmulyo No. 33', '004', '005', 'Sendangmulyo', 'Tembalang', 5, 'Lemari dan kitchen set custom', 'Kayu MDF, HPL', 'Mesin profil, bor', -7.05700000, 110.43200000, '2025-12-10 23:16:20'),
(47, 'Sofa Minimalis Nyaman', 'Wijaya Kusuma', 'Jl. Tlogomulyo No. 55', '001', '006', 'Pedurungan Tengah', 'Pedurungan', 5, 'Sofa dengan desain minimalis modern', 'Rangka kayu, busa, kain', 'Stapler tembak, mesin jahit', -7.01670000, 110.45830000, '2025-12-10 23:16:20'),
(48, 'Keripik Singkong Renyah', 'Bu Sumi', 'Jl. Boja No. 111', '003', '002', 'Sadeng', 'Gunungpati', 6, 'Keripik singkong aneka rasa', 'Singkong segar, bumbu', 'Pisau perajang, penggorengan', -7.08400000, 110.37400000, '2025-12-10 23:16:20'),
(49, 'Jamur Crispy Sehat', 'Agus Riyadi', 'Jl. Sekaran No. 88', '005', '008', 'Sekaran', 'Gunungpati', 6, 'Jamur tiram crispy dan segar', 'Jamur tiram, tepung', 'Kumbung jamur, penggorengan', -7.08200000, 110.37200000, '2025-12-10 23:16:20'),
(50, 'Kosmetik Herbal Alami', 'dr. Sinta Dewi', 'Jl. Perintis Kemerdekaan No. 22', '008', '003', 'Tandang', 'Tembalang', 7, 'Produk skincare dari bahan alami', 'Minyak essensial, ekstrak tanaman', 'Mixer kosmetik, botol kemasan', -7.05150000, 110.44100000, '2025-12-10 23:16:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `ref_kecamatan`
--
ALTER TABLE `ref_kecamatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `umkm`
--
ALTER TABLE `umkm`
  ADD PRIMARY KEY (`id_umkm`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ref_kecamatan`
--
ALTER TABLE `ref_kecamatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `umkm`
--
ALTER TABLE `umkm`
  MODIFY `id_umkm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `umkm`
--
ALTER TABLE `umkm`
  ADD CONSTRAINT `umkm_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
