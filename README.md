# 🚀 Portfolio & Project Management Web Application

Aplikasi web **Portfolio & Project Management** berbahan dasar **PHP Native (PDO)**, **MySQL / MariaDB**, **HTML5**, dan **Pure CSS (Vanilla)**. Didesain secara eksklusif dengan tema dark glassmorphism modern, efisiensi tinggi, fitur manajemen portofolio lengkap, serta perlindungan keamanan tingkat lanjut.

---

## 🛠️ Teknologi & Fitur Utama

- **Backend**: PHP Native 8.x+ (PDO Prepared Statements, Session Guard, BCRYPT Password Hashing, Auto-Migration System).
- **Database**: MySQL / MariaDB (Perancangan relasional & Prepared Statements anti SQL Injection).
- **Frontend**: HTML5 Semantik, Pure CSS (Vanilla CSS3, Flexbox, CSS Grid, Glassmorphism, Responsive Micro-animations, Lightbox Preview).
- **Fitur Publik (Frontend)**:
  - 👤 **Hero Header**: Profil developer, status ketersediaan, biografi singkat, dan **Dynamic Tech Stack Badges** (PHP 8+, MySQL, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API).
  - 🙋‍♂️ **Tentang Saya**: Section dedicated dengan kutipan filosofi, statistik highlight (Tahun Pengalaman, Total Proyek, Kepuasan Klien), bilah penguasaan teknologi (Skills Bar), dan nilai-nilai profesional.
  - 💻 **Hasil Proyek**: Grid proyek interaktif dengan preview gambar, deskripsi teknis, dan tautan repositori GitHub.
  - 📜 **Sertifikat & Penghargaan**: Showcase sertifikasi kompetensi dengan modal **Lightbox Full Preview** & verifikasi kredensial.
  - 📞 **Footer & Kontak**: Informasi kontak dinamis (Alamat, WhatsApp `wa.me`, Email, Instagram, GitHub) & Floating WhatsApp Widget.
- **Fitur Admin Panel (Backend)**:
  - 🔐 **Autentikasi Aman**: Login admin terproteksi dengan `password_verify` (BCRYPT) & `auth_check.php`.
  - 📋 **Manajemen Proyek (CRUD)**: Tambah, edit, dan hapus proyek portofolio beserta manajemen upload gambar.
  - 📜 **Manajemen Sertifikat (CRUD)**: Tambah, edit, dan hapus sertifikat/penghargaan beserta upload gambar & kredensial.
  - ⚙️ **Pengaturan Profil & Footer**: Ubah nama, gelar, biografi, statistik, tech stack, password admin, serta kontak footer (Alamat, WhatsApp, Email, Instagram, GitHub).
  - 🚪 **Logout**: Session destruction aman dari celah keamanan.

---

## 📁 Struktur Folder Project

```text
c:\laragon\Portofolio\huda\
├── database.sql           # Skrip inisialisasi Database & Tabel (Schema & Seed Data)
├── koneksi.php            # Koneksi PDO Database & Sistem Auto-Migration Otomatis
├── index.php              # Halaman Publik Portofolio & Showcase Proyek
├── login.php              # Halaman Login Admin Panel
├── .gitignore             # File Konfigurasi Git (Abaikan File Temp & Uploads)
├── README.md              # Dokumentasi Lengkap Sistem & Panduan Instalasi
├── assets/
│   ├── css/
│   │   ├── style.css      # Custom Styling Frontend Publik (Glassmorphism & Responsive)
│   │   └── admin.css      # Custom Styling Panel Administrator
│   └── uploads/           # Penyimpanan Gambar Profil, Proyek & Sertifikat
└── admin/
    ├── auth_check.php         # Middleware Pengecekan Session Admin
    ├── index.php              # Dashboard Admin (Kelola Proyek)
    ├── tambah.php             # Form & Proses Tambah Proyek
    ├── edit.php               # Form & Proses Edit Proyek
    ├── hapus.php              # Proses Hapus Proyek & Pembersihan File
    ├── sertifikat.php         # Dashboard Kelola Sertifikat
    ├── sertifikat_tambah.php  # Form & Proses Tambah Sertifikat
    ├── sertifikat_edit.php    # Form & Proses Edit Sertifikat
    ├── sertifikat_hapus.php   # Proses Hapus Sertifikat & Pembersihan File
    ├── pengaturan.php         # Form Pengaturan Profil, "Tentang Saya" & Kontak Footer
    └── logout.php             # Skrip Logout & Hapus Session
```

---

## 💻 Panduan Instalasi & Penggunaan di Localhost (Laragon / XAMPP)

### Langkah 1: Persiapan Server Lokal
1. Pastikan **Laragon** atau **XAMPP** telah terinstal dan berjalan di komputer Anda.
2. Jalankan modul **Apache** dan **MySQL** pada Control Panel.

### Langkah 2: Impor Database SQL (Opsional - Terintegrasi Auto-Migration)
1. Buka **phpMyAdmin** melalui alamat: `http://localhost/phpmyadmin` (atau menu Database Laragon).
2. Buat database baru dengan nama `db_portfolio`.
3. Impor file `database.sql` yang berada di direktori proyek ini.
4. *Catatan*: `koneksi.php` dilengkapi fitur **Auto-Migration** yang akan otomatis membuat tabel & kolom yang diperlukan jika database belum diimpor manual.

### Langkah 3: Konfigurasi Koneksi Database
Buka file `koneksi.php` dan sesuaikan parameter jika MySQL Anda menggunakan password:
```php
$host     = 'localhost';
$dbname   = 'db_portfolio';
$username = 'root';
$password = ''; // Isikan password MySQL jika ada
```

### Langkah 4: Akses Aplikasi di Browser
- **Halaman Publik (Frontend Portofolio)**:
  `http://localhost/Portofolio/huda/index.php` atau `http://localhost/Portofolio/huda/`
- **Halaman Admin Panel (Backend Login)**:
  `http://localhost/Portofolio/huda/login.php`

---

## 🔑 Kredensial Login Admin Default

| Parameter | Kredensial Default |
| :--- | :--- |
| **URL Login** | `http://localhost/Portofolio/huda/login.php` |
| **Username** | `admin` |
| **Password** | `admin123` |

> 🔒 *Password dienkripsi menggunakan algoritma `PASSWORD_BCRYPT` untuk menjamin keamanan akun admin.*

---

## 🔒 Fitur Keamanan & Best Practices

1. **SQL Injection Prevention**: Seluruh kueri database menggunakan **PDO Prepared Statements** dengan *parameterized queries*.
2. **XSS (Cross-Site Scripting) Prevention**: Output variabel di-escape menggunakan fungsi `htmlspecialchars()`.
3. **Session Authentication Guard**: Seluruh halaman admin terproteksi oleh `admin/auth_check.php`. Akses tanpa login otomatis dialihkan ke `login.php`.
4. **File Upload Handling**: Gambar divalidasi ekstensi filenya (`jpg`, `jpeg`, `png`, `webp`, `svg`), batasan ukuran file 5 MB, dan nama file di-generate ulang dengan stempel waktu unik (`time() + uniqid()`).
5. **Clean Code & Architecture**: Pemisahan yang rapi antara logika Backend Admin, Auto-Migration, dan Frontend Publik.
