-- Skrip Pengaturan Database Portfolio & Project Management
-- Jalankan file ini di phpMyAdmin atau MySQL CLI

CREATE DATABASE IF NOT EXISTS `db_portfolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_portfolio`;

-- --------------------------------------------------------

-- Struktur dari tabel `admin` (Termasuk Pengaturan Profil Developer & Tentang Saya)
CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL DEFAULT 'Huda',
  `gelar` VARCHAR(150) NOT NULL DEFAULT 'Senior Full-Stack Web Developer',
  `bio` TEXT DEFAULT NULL,
  `tentang_lengkap` TEXT DEFAULT NULL,
  `pengalaman_tahun` VARCHAR(20) NOT NULL DEFAULT '3+',
  `total_proyek` VARCHAR(20) NOT NULL DEFAULT '5+',
  `kepuasan_klien` VARCHAR(20) NOT NULL DEFAULT '100%',
  `foto_profil` VARCHAR(255) NOT NULL DEFAULT 'profile.svg',
  `skills` VARCHAR(255) NOT NULL DEFAULT 'PHP 8+ (PDO), MySQL / MariaDB, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API',
  `alamat` VARCHAR(255) NOT NULL DEFAULT 'Kertusoko, Krucil, Probolinggo, Jawa Timur',
  `whatsapp` VARCHAR(50) NOT NULL DEFAULT '081337212405',
  `email` VARCHAR(100) NOT NULL DEFAULT 'hudabismillah16@gmail.com',
  `instagram` VARCHAR(100) NOT NULL DEFAULT 'zm18099',
  `github` VARCHAR(100) NOT NULL DEFAULT 'SamsulHuda16',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Seed data Akun Admin & Profil Default
INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `gelar`, `bio`, `tentang_lengkap`, `pengalaman_tahun`, `total_proyek`, `kepuasan_klien`, `foto_profil`, `skills`, `alamat`, `whatsapp`, `email`, `instagram`, `github`) VALUES
(1, 'admin', '$2y$10$jh9qw6/m29Ju5s8YogDuf.fl7lojPQtQeamvbo5LZf/o9aezbjxFa', 'Huda', 'Senior Full-Stack Web Developer', 'Saya spesialis dalam membangun aplikasi web yang aman, cepat, dan terstruktur dengan PHP Native (PDO Architecture), MySQL, HTML5, dan Pure CSS (Vanilla CSS).', 'Saya seorang Full-Stack Web Developer yang berdedikasi tinggi dalam menciptakan aplikasi web berkualitas tinggi, terstruktur, dan efisien. Dengan pemahaman mendalam tentang arsitektur PHP Native (PDO), perancangan database relasional MySQL, dan penulisan kode HTML5 & Pure CSS3 murni tanpa bergantung pada framework berat, saya memastikan setiap aplikasi berjalan cepat, aman dari celah keamanan, serta memiliki antarmuka pengguna yang responsif dan modern.', '3+', '5+', '100%', 'profile.svg', 'PHP 8+ (PDO), MySQL / MariaDB, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API', 'Kertusoko, Krucil, Probolinggo, Jawa Timur', '081337212405', 'hudabismillah16@gmail.com', 'zm18099', 'SamsulHuda16')
ON DUPLICATE KEY UPDATE 
  `nama_lengkap` = VALUES(`nama_lengkap`),
  `gelar` = VALUES(`gelar`),
  `bio` = VALUES(`bio`),
  `tentang_lengkap` = VALUES(`tentang_lengkap`),
  `pengalaman_tahun` = VALUES(`pengalaman_tahun`),
  `total_proyek` = VALUES(`total_proyek`),
  `kepuasan_klien` = VALUES(`kepuasan_klien`),
  `skills` = VALUES(`skills`),
  `alamat` = VALUES(`alamat`),
  `whatsapp` = VALUES(`whatsapp`),
  `email` = VALUES(`email`),
  `instagram` = VALUES(`instagram`),
  `github` = VALUES(`github`);

-- --------------------------------------------------------

-- Struktur dari tabel `projects`
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(150) NOT NULL,
  `deskripsi_teknis` TEXT NOT NULL,
  `link_github` VARCHAR(255) DEFAULT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Seed 5 Data Proyek Hasil Kerja Huda
INSERT INTO `projects` (`id`, `judul`, `deskripsi_teknis`, `link_github`, `gambar`) VALUES
(1, 'RentalMobil - Car Rental Management System', 'Aplikasi manajemen penyewaan mobil berbasis PHP & MySQL. Dilengkapi fitur reservasi armada, pengolahan transaksi rental, riwayat pemesanan pelanggan, dan dashboard laporan administrator.', 'https://github.com/huda/rentalmobil-app', 'rentalmobil.svg'),
(2, 'E-Commerce Marketplace API & Web Platform', 'Sistem e-commerce skala penuh berbasis PHP Native (PDO Architecture), MySQL, dan Vanilla CSS. Terintegrasi dengan payment gateway, keranjang belanja, invoice otomatis, dan REST API.', 'https://github.com/huda/ecommerce-php-native', 'ecommerce.svg'),
(3, 'Smart Clinic & Rekam Medis Pasien', 'Aplikasi manajemen klinik digital untuk mengelola antrean pasien realtime, rekam medis elektronik, stok obat-obatan, dan laporan statistik pendapatan bulanan.', 'https://github.com/huda/smart-clinic-system', 'clinic.svg'),
(4, 'Portfolio & Project Management Web App', 'Aplikasi manajemen portofolio & showcase proyek dengan antarmuka dark glassmorphism modern, fitur autentikasi admin yang aman (BCRYPT), dan manajemen CRUD lengkap.', 'https://github.com/huda/portfolio-crud-php', 'portfolio.svg'),
(5, 'Smart Inventory & Point of Sale (POS) System', 'Aplikasi Kasir (POS) dan Manajemen Stok Barang Realtime berbasis PHP Native, PDO MySQL, & JavaScript. Fitur mencakup pencetakan struk belanja, barcode scanner, manajemen supplier, dan grafik analisis laba rugi.', 'https://github.com/huda/smart-pos-inventory', 'pos.svg')
ON DUPLICATE KEY UPDATE 
  `judul` = VALUES(`judul`),
  `deskripsi_teknis` = VALUES(`deskripsi_teknis`),
  `link_github` = VALUES(`link_github`),
  `gambar` = VALUES(`gambar`);

-- --------------------------------------------------------

-- Struktur dari tabel `certificates`
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(150) NOT NULL,
  `penerbit` VARCHAR(100) NOT NULL,
  `tahun` VARCHAR(20) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `link_kredensial` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Seed data Sertifikat & Penghargaan
INSERT INTO `certificates` (`id`, `judul`, `penerbit`, `tahun`, `deskripsi`, `gambar`, `link_kredensial`) VALUES
(1, 'Full-Stack Web Developer Expert', 'Digital Talent Academy', '2025', 'Sertifikasi kompetensi pengembangan aplikasi web skala penuh menggunakan PHP Native, PDO MySQL, HTML5, dan Pure CSS.', 'cert1.svg', 'https://example.com/credential/cert-123'),
(2, 'MySQL Database Administrator Professional', 'International Database Association', '2025', 'Sertifikasi keahlian perancangan skema relasional, optimasi kueri SQL, dan keamanan database MySQL.', 'cert2.svg', 'https://example.com/credential/cert-456')
ON DUPLICATE KEY UPDATE 
  `judul` = VALUES(`judul`),
  `penerbit` = VALUES(`penerbit`),
  `tahun` = VALUES(`tahun`),
  `deskripsi` = VALUES(`deskripsi`),
  `gambar` = VALUES(`gambar`),
  `link_kredensial` = VALUES(`link_kredensial`);
