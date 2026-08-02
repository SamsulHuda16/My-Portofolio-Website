<?php
/**
 * File Koneksi Database MySQL menggunakan PDO (PHP Data Objects)
 * Menyediakan koneksi yang aman dari SQL Injection melalui Prepared Statements
 * Serta dilengkapi fitur Auto-Migration tabel & kolom database secara otomatis.
 */

$host     = 'localhost';
$dbname   = 'db_portfolio';
$username = 'root';
$password = ''; // Default password Laragon/XAMPP kosong

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

    // Auto-Migrate: Tambah kolom profil & tentang saya pada tabel admin jika belum ada
    try {
        $checkCol = $pdo->query("SHOW COLUMNS FROM `admin` LIKE 'nama_lengkap'");
        if ($checkCol && $checkCol->rowCount() === 0) {
            $pdo->exec("ALTER TABLE `admin` 
                ADD COLUMN `nama_lengkap` VARCHAR(100) NOT NULL DEFAULT 'Huda',
                ADD COLUMN `gelar` VARCHAR(150) NOT NULL DEFAULT 'Senior Full-Stack Web Developer',
                ADD COLUMN `bio` TEXT DEFAULT NULL,
                ADD COLUMN `tentang_lengkap` TEXT DEFAULT NULL,
                ADD COLUMN `pengalaman_tahun` VARCHAR(20) NOT NULL DEFAULT '7+',
                ADD COLUMN `total_proyek` VARCHAR(20) NOT NULL DEFAULT '5+',
                ADD COLUMN `kepuasan_klien` VARCHAR(20) NOT NULL DEFAULT '100%',
                ADD COLUMN `foto_profil` VARCHAR(255) NOT NULL DEFAULT 'profile.svg',
                ADD COLUMN `skills` VARCHAR(255) NOT NULL DEFAULT 'PHP 8+ (PDO), MySQL / MariaDB, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API'");
            
            $pdo->exec("UPDATE `admin` SET 
                `nama_lengkap` = 'Huda', 
                `gelar` = 'Senior Full-Stack Web Developer',
                `bio` = 'Saya spesialis dalam membangun aplikasi web yang aman, cepat, dan terstruktur dengan PHP Native (PDO Architecture), MySQL, HTML5, dan Pure CSS (Vanilla CSS).',
                `tentang_lengkap` = 'Saya seorang Full-Stack Web Developer yang berdedikasi tinggi dalam menciptakan aplikasi web berkualitas tinggi, terstruktur, dan efisien. Dengan pemahaman mendalam tentang arsitektur PHP Native (PDO), perancangan database relasional MySQL, dan penulisan kode HTML5 & Pure CSS3 murni tanpa bergantung pada framework berat, saya memastikan setiap aplikasi berjalan cepat, aman dari celah keamanan, serta memiliki antarmuka pengguna yang responsif dan modern.',
                `pengalaman_tahun` = '7+',
                `total_proyek` = '5+',
                `kepuasan_klien` = '100%',
                `foto_profil` = 'profile.svg',
                `skills` = 'PHP 8+ (PDO), MySQL / MariaDB, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API'
                WHERE `id` = 1 OR `username` = 'admin'");
        } else {
            // Cek jika kolom tentang_lengkap belum ada
            $checkTentang = $pdo->query("SHOW COLUMNS FROM `admin` LIKE 'tentang_lengkap'");
            if ($checkTentang && $checkTentang->rowCount() === 0) {
                $pdo->exec("ALTER TABLE `admin` 
                    ADD COLUMN `tentang_lengkap` TEXT DEFAULT NULL,
                    ADD COLUMN `pengalaman_tahun` VARCHAR(20) NOT NULL DEFAULT '7+',
                    ADD COLUMN `total_proyek` VARCHAR(20) NOT NULL DEFAULT '5+',
                    ADD COLUMN `kepuasan_klien` VARCHAR(20) NOT NULL DEFAULT '100%'");
                
                $pdo->exec("UPDATE `admin` SET 
                    `tentang_lengkap` = 'Saya seorang Full-Stack Web Developer yang berdedikasi tinggi dalam menciptakan aplikasi web berkualitas tinggi, terstruktur, dan efisien. Dengan pemahaman mendalam tentang arsitektur PHP Native (PDO), perancangan database relasional MySQL, dan penulisan kode HTML5 & Pure CSS3 murni tanpa bergantung pada framework berat, saya memastikan setiap aplikasi berjalan cepat, aman dari celah keamanan, serta memiliki antarmuka pengguna yang responsif dan modern.',
                    `pengalaman_tahun` = '7+',
                    `total_proyek` = '5+',
                    `kepuasan_klien` = '100%'
                    WHERE `id` = 1 OR `username` = 'admin'");
            }

            // Cek jika kolom whatsapp/kontak footer belum ada
            $checkKontak = $pdo->query("SHOW COLUMNS FROM `admin` LIKE 'whatsapp'");
            if ($checkKontak && $checkKontak->rowCount() === 0) {
                $pdo->exec("ALTER TABLE `admin` 
                    ADD COLUMN `alamat` VARCHAR(255) NOT NULL DEFAULT 'Kertusoko, Krucil, Probolinggo, Jawa Timur',
                    ADD COLUMN `whatsapp` VARCHAR(50) NOT NULL DEFAULT '081337212405',
                    ADD COLUMN `email` VARCHAR(100) NOT NULL DEFAULT 'hudabismillah16@gmail.com',
                    ADD COLUMN `instagram` VARCHAR(100) NOT NULL DEFAULT 'zm18099',
                    ADD COLUMN `github` VARCHAR(100) NOT NULL DEFAULT 'SamsulHuda16'");
                
                $pdo->exec("UPDATE `admin` SET 
                    `alamat` = 'Kertusoko, Krucil, Probolinggo, Jawa Timur',
                    `whatsapp` = '081337212405',
                    `email` = 'hudabismillah16@gmail.com',
                    `instagram` = 'zm18099',
                    `github` = 'SamsulHuda16'
                    WHERE `id` = 1 OR `username` = 'admin'");
            }

            // Pastikan skills mencakup Laravel dan CodeIgniter 3 jika belum ada di DB
            try {
                $curSkills = $pdo->query("SELECT `skills` FROM `admin` WHERE `id` = 1 OR `username` = 'admin'")->fetchColumn();
                if ($curSkills && strpos($curSkills, 'Laravel') === false) {
                    $newSkillsList = 'PHP 8+ (PDO), MySQL / MariaDB, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API';
                    $pdo->exec("UPDATE `admin` SET `skills` = '$newSkillsList' WHERE `id` = 1 OR `username` = 'admin'");
                }
            } catch (PDOException $ex) {}
        }
    } catch (PDOException $ex) {}

    // Auto-Migrate: Pastikan tabel projects terisi 5 sampel proyek
    try {
        $countProjects = $pdo->query("SELECT COUNT(*) FROM `projects`")->fetchColumn();
        if ($countProjects < 5) {
            $pdo->exec("INSERT INTO `projects` (`id`, `judul`, `deskripsi_teknis`, `link_github`, `gambar`) VALUES
            (1, 'RentalMobil - Car Rental Management System', 'Aplikasi manajemen penyewaan mobil berbasis PHP & MySQL. Dilengkapi fitur reservasi armada, pengolahan transaksi rental, riwayat pemesanan pelanggan, dan dashboard laporan administrator.', 'https://github.com/huda/rentalmobil-app', 'rentalmobil.svg'),
            (2, 'E-Commerce Marketplace API & Web Platform', 'Sistem e-commerce skala penuh berbasis PHP Native (PDO Architecture), MySQL, dan Vanilla CSS. Terintegrasi dengan payment gateway, keranjang belanja, invoice otomatis, dan REST API.', 'https://github.com/huda/ecommerce-php-native', 'ecommerce.svg'),
            (3, 'Smart Clinic & Rekam Medis Pasien', 'Aplikasi manajemen klinik digital untuk mengelola antrean pasien realtime, rekam medis elektronik, stok obat-obatan, dan laporan statistik pendapatan bulanan.', 'https://github.com/huda/smart-clinic-system', 'clinic.svg'),
            (4, 'Portfolio & Project Management Web App', 'Aplikasi manajemen portofolio & showcase proyek dengan antarmuka dark glassmorphism modern, fitur autentikasi admin yang aman (BCRYPT), dan manajemen CRUD lengkap.', 'https://github.com/huda/portfolio-crud-php', 'portfolio.svg'),
            (5, 'Smart Inventory & Point of Sale (POS) System', 'Aplikasi Kasir (POS) dan Manajemen Stok Barang Realtime berbasis PHP Native, PDO MySQL, & JavaScript. Fitur mencakup pencetakan struk belanja, barcode scanner, manajemen supplier, dan grafik analisis laba rugi.', 'https://github.com/huda/smart-pos-inventory', 'pos.svg')
            ON DUPLICATE KEY UPDATE 
              `judul` = VALUES(`judul`),
              `deskripsi_teknis` = VALUES(`deskripsi_teknis`),
              `link_github` = VALUES(`link_github`),
              `gambar` = VALUES(`gambar`);");
        }
    } catch (PDOException $ex) {}

    // Auto-Migrate: Tambah tabel certificates jika belum ada
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `certificates` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `judul` VARCHAR(150) NOT NULL,
          `penerbit` VARCHAR(100) NOT NULL,
          `tahun` VARCHAR(20) NOT NULL,
          `deskripsi` TEXT DEFAULT NULL,
          `gambar` VARCHAR(255) NOT NULL,
          `link_kredensial` VARCHAR(255) DEFAULT NULL,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $checkCert = $pdo->query("SELECT COUNT(*) FROM `certificates`")->fetchColumn();
        if ($checkCert == 0) {
            $pdo->exec("INSERT INTO `certificates` (`id`, `judul`, `penerbit`, `tahun`, `deskripsi`, `gambar`, `link_kredensial`) VALUES
            (1, 'Full-Stack Web Developer Expert', 'Digital Talent Academy', '2025', 'Sertifikasi kompetensi pengembangan aplikasi web skala penuh menggunakan PHP Native, PDO MySQL, HTML5, dan Pure CSS.', 'cert1.svg', 'https://example.com/credential/cert-123'),
            (2, 'MySQL Database Administrator Professional', 'International Database Association', '2025', 'Sertifikasi keahlian perancangan skema relasional, optimasi kueri SQL, dan keamanan database MySQL.', 'cert2.svg', 'https://example.com/credential/cert-456')");
        }
    } catch (PDOException $ex) {}

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . htmlspecialchars($e->getMessage()));
}
