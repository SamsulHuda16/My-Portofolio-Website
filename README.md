# 🚀 Portfolio & Project Management Web Application

Aplikasi web **Portfolio & Project Management** berbahan dasar **PHP Native**, **PDO MySQL**, **HTML5**, dan **Pure CSS (Vanilla)**. Didesain secara eksklusif dengan tema dark modern, efisiensi tinggi, serta perlindungan keamanan tingkat lanjut.

---

## 🛠️ Teknologi & Fitur Utama

- **Backend**: PHP Native 8.x+ (PDO Prepared Statements, Session Authentication, Password Hashing).
- **Database**: MySQL / MariaDB (Prepared statements anti SQL Injection).
- **Frontend**: HTML5 Semantik, Pure CSS (Vanilla CSS, CSS Grid, Flexbox, Glassmorphism, Micro-animations).
- **Fitur Admin**:
  - 🔐 Autentikasi Login Admin yang aman (`password_verify`).
  - 📋 Dashboard Manajemen Proyek (Read).
  - ➕ Tambah Proyek Baru + Upload Gambar (Create).
  - ✏️ Edit Data Proyek & Ganti Gambar (Update).
  - 🗑️ Hapus Proyek & Pembersihan File Otomatis (Delete).
  - 🚪 Logout Session destruction.

---

## 📁 Struktur Folder Project

```text
c:\laragon\Portofolio\huda\
├── database.sql           # Script insialisasi Database & Tabel
├── koneksi.php            # Koneksi PDO Database
├── index.php              # Halaman Publik Portofolio
├── login.php              # Halaman Login Admin
├── .gitignore             # File konfigurasi Git
├── README.md              # Dokumentasi & Panduan Instalasi
├── assets/
│   ├── css/
│   │   ├── style.css      # Styling Frontend Publik
│   │   └── admin.css      # Styling Admin Panel
│   └── uploads/           # Direktori Penyimpanan Gambar Proyek
└── admin/
    ├── auth_check.php     # Middleware Pengecekan Session Admin
    ├── index.php          # Dashboard Admin
    ├── tambah.php         # Form & Proses Tambah Proyek
    ├── edit.php           # Form & Proses Edit Proyek
    ├── hapus.php          # Proses Hapus Proyek
    └── logout.php         # Skrip Logout Session
```

---

## 💻 Panduan Instalasi & Penggunaan di Localhost (Laragon / XAMPP)

### Langkah 1: Persiapan Server Lokal
1. Pastikan **Laragon** atau **XAMPP** telah terinstal di komputer Anda.
2. Jalankan modul **Apache** dan **MySQL** pada Control Panel Laragon/XAMPP.

### Langkah 2: Impor Database SQL
1. Buka **phpMyAdmin** di browser melalui alamat: `http://localhost/phpmyadmin` (atau menu Database di Laragon).
2. Buat database baru dengan nama `db_portfolio` (atau biarkan otomatis dibuat oleh file SQL).
3. Pilih menu **Import** / **Impor**, lalu pilih file `database.sql` yang berada di direktori proyek ini.
4. Klik **Go** / **Kirim** untuk mengeksekusi skrip SQL.

### Langkah 3: Konfigurasi Koneksi Database (Jika Ada Perubahan Kredensial)
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

> 🔒 *Password dienkripsi menggunakan algoritma `PASSWORD_BCRYPT` untuk menjamin keamanan akun.*

---

## 🔒 Fitur Keamanan Diterapkan

1. **SQL Injection Prevention**: Seluruh kueri database menggunakan **PDO Prepared Statements** dengan *parameterized queries*.
2. **XSS (Cross-Site Scripting) Prevention**: Output variabel di-escape menggunakan fungsi `htmlspecialchars()`.
3. **Session Authentication Guard**: Halaman admin dilindungi oleh `admin/auth_check.php`. Akses tanpa login langsung di-redirect ke `login.php`.
4. **File Upload Handling**: Gambar yang diunggah divalidasi ekstensi filenya (`jpg`, `jpeg`, `png`, `webp`) dan namanya di-generate ulang dengan stempel waktu unik (`time()`) untuk menghindari tumpang tindih file.
