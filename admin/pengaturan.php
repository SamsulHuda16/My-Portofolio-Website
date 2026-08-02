<?php
/**
 * Admin Panel - Pengaturan Profil & Foto Developer
 * Mengizinkan admin untuk mengunggah foto profil baru, memperbarui biografi tentang saya, statistik, skills, dan password.
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$admin_id = $_SESSION['admin_id'] ?? 1;

// Ambil data admin saat ini
try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch();

    if (!$admin) {
        $stmt = $pdo->prepare("SELECT * FROM admin LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();
    }
} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}

$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap     = trim($_POST['nama_lengkap'] ?? '');
    $gelar            = trim($_POST['gelar'] ?? '');
    $bio              = trim($_POST['bio'] ?? '');
    $tentang_lengkap  = trim($_POST['tentang_lengkap'] ?? '');
    $pengalaman_tahun = trim($_POST['pengalaman_tahun'] ?? '7+');
    $total_proyek     = trim($_POST['total_proyek'] ?? '5+');
    $kepuasan_klien   = trim($_POST['kepuasan_klien'] ?? '100%');
    $skills           = trim($_POST['skills'] ?? '');
    $alamat           = trim($_POST['alamat'] ?? 'Kertusoko, Krucil, Probolinggo, Jawa Timur');
    $whatsapp         = trim($_POST['whatsapp'] ?? '081337212405');
    $email            = trim($_POST['email'] ?? 'hudabismillah16@gmail.com');
    $instagram        = trim($_POST['instagram'] ?? 'zm18099');
    $github           = trim($_POST['github'] ?? 'SamsulHuda16');

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (empty($nama_lengkap) || empty($gelar)) {
        $error_msg = 'Nama lengkap dan Gelar wajib diisi!';
    } else {
        $fotoName = $admin['foto_profil'] ?? 'profile.svg';

        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            $file         = $_FILES['foto_profil'];
            $fileName     = $file['name'];
            $fileTmpName  = $file['tmp_name'];
            $fileSize     = $file['size'];
            $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExt   = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

            if (!in_array($fileExt, $allowedExt)) {
                $error_msg = 'Format file foto tidak valid! Gunakan format JPG, PNG, WEBP, atau SVG.';
            } elseif ($fileSize > $maxSizeBytes) {
                $error_msg = 'Ukuran file foto terlalu besar! Maksimal 5 MB.';
            } else {
                $newFileName = 'profile_' . time() . '_' . uniqid() . '.' . $fileExt;
                $uploadDir   = '../assets/uploads/';
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpName, $destination)) {
                    if (!empty($admin['foto_profil']) && $admin['foto_profil'] !== 'profile.svg') {
                        $oldFilePath = $uploadDir . $admin['foto_profil'];
                        if (file_exists($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                    }
                    $fotoName = $newFileName;
                } else {
                    $error_msg = 'Gagal mengunggah foto profil baru ke server.';
                }
            }
        }

        $password_hash = $admin['password'];
        if (!empty($new_password)) {
            if (empty($old_password)) {
                $error_msg = 'Masukkan password lama Anda untuk mengubah password!';
            } elseif (!password_verify($old_password, $admin['password'])) {
                $error_msg = 'Password lama Anda tidak cocok!';
            } elseif (strlen($new_password) < 6) {
                $error_msg = 'Password baru minimal harus 6 karakter!';
            } else {
                $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            }
        }

        if (empty($error_msg)) {
            try {
                $updateStmt = $pdo->prepare("UPDATE admin SET 
                    nama_lengkap = :nama_lengkap, 
                    gelar = :gelar, 
                    bio = :bio, 
                    tentang_lengkap = :tentang_lengkap,
                    pengalaman_tahun = :pengalaman_tahun,
                    total_proyek = :total_proyek,
                    kepuasan_klien = :kepuasan_klien,
                    skills = :skills, 
                    alamat = :alamat,
                    whatsapp = :whatsapp,
                    email = :email,
                    instagram = :instagram,
                    github = :github,
                    foto_profil = :foto_profil, 
                    password = :password 
                    WHERE id = :id");

                $updateStmt->bindParam(':nama_lengkap', $nama_lengkap, PDO::PARAM_STR);
                $updateStmt->bindParam(':gelar', $gelar, PDO::PARAM_STR);
                $updateStmt->bindParam(':bio', $bio, PDO::PARAM_STR);
                $updateStmt->bindParam(':tentang_lengkap', $tentang_lengkap, PDO::PARAM_STR);
                $updateStmt->bindParam(':pengalaman_tahun', $pengalaman_tahun, PDO::PARAM_STR);
                $updateStmt->bindParam(':total_proyek', $total_proyek, PDO::PARAM_STR);
                $updateStmt->bindParam(':kepuasan_klien', $kepuasan_klien, PDO::PARAM_STR);
                $updateStmt->bindParam(':skills', $skills, PDO::PARAM_STR);
                $updateStmt->bindParam(':alamat', $alamat, PDO::PARAM_STR);
                $updateStmt->bindParam(':whatsapp', $whatsapp, PDO::PARAM_STR);
                $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
                $updateStmt->bindParam(':instagram', $instagram, PDO::PARAM_STR);
                $updateStmt->bindParam(':github', $github, PDO::PARAM_STR);
                $updateStmt->bindParam(':foto_profil', $fotoName, PDO::PARAM_STR);
                $updateStmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $updateStmt->bindParam(':id', $admin['id'], PDO::PARAM_INT);

                if ($updateStmt->execute()) {
                    $success_msg = '✅ Pengaturan profil & informasi "Tentang Saya" berhasil diperbarui!';
                    
                    $stmt->execute();
                    $admin = $stmt->fetch();
                } else {
                    $error_msg = 'Gagal memperbarui data profil ke database.';
                }
            } catch (PDOException $e) {
                $error_msg = 'Database Error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil & Tentang Saya - Admin Panel</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/uploads/favicon_circle.png?v=<?= time(); ?>">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <a href="index.php" class="admin-logo">
                Dev<span>Admin</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="admin-nav-toggle" id="adminNavToggle" aria-label="Buka Menu Navigasi">
                <span class="admin-hamburger-bar"></span>
                <span class="admin-hamburger-bar"></span>
                <span class="admin-hamburger-bar"></span>
            </button>

            <ul class="admin-nav-links" id="adminNavMenu">
                <li><a href="index.php">📋 Data Proyek</a></li>
                <li><a href="sertifikat.php">📜 Sertifikat</a></li>
                <li><a href="pengaturan.php" class="active">⚙️ Pengaturan</a></li>
                <li><a href="../index.php" target="_blank">🌐 Lihat Website</a></li>
                <li class="nav-user-item">
                    <div class="user-info">
                        <div class="user-avatar-badge">
                            <div class="user-avatar-icon">👤</div>
                            <span class="user-name"><?= htmlspecialchars($admin['nama_lengkap'] ?? 'Admin'); ?></span>
                        </div>
                        <a href="logout.php" class="btn-logout" title="Keluar dari Panel Admin">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        var toggle = document.getElementById('adminNavToggle');
        var menu   = document.getElementById('adminNavMenu');
        toggle.addEventListener('click', function() {
            menu.classList.toggle('open');
            toggle.classList.toggle('open');
        });
    </script>

    <main class="admin-main">
        <div class="admin-header-action" style="max-width: 750px; margin: 0 auto 25px;">
            <div>
                <h1 class="admin-title">⚙️ Pengaturan Profil &amp; "Tentang Saya"</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Ubah foto profil, biografi "Tentang Saya", statistik, keahlian, dan password admin Anda.</p>
            </div>
            <a href="index.php" class="btn-secondary">&larr; Kembali</a>
        </div>

        <div class="form-card" style="max-width: 750px;">
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form action="pengaturan.php" method="POST" enctype="multipart/form-data">
                
                <!-- Section Foto Profil -->
                <div class="form-group" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                    <label class="form-label" style="font-weight: 700; color: var(--primary);">📷 Foto Profil Developer</label>
                    <div style="display: flex; align-items: center; gap: 20px; margin-top: 12px; flex-wrap: wrap;">
                        <?php 
                            $foto_path = '../assets/uploads/' . ($admin['foto_profil'] ?? 'profile.svg');
                            if (!empty($admin['foto_profil']) && file_exists($foto_path)): 
                        ?>
                            <img src="<?= htmlspecialchars($foto_path); ?>" alt="Preview Profile" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); box-shadow: 0 0 15px rgba(99,102,241,0.3);">
                        <?php else: ?>
                            <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--bg-surface-elevated); display: flex; align-items: center; justify-content: center; font-size: 2rem;">👤</div>
                        <?php endif; ?>

                        <div style="flex: 1; min-width: 220px;">
                            <label for="foto_profil" class="form-label" style="font-size: 0.85rem;">Pilih Foto Baru (JPG, PNG, WEBP, SVG)</label>
                            <input type="file" id="foto_profil" name="foto_profil" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                            <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 5px;">Maksimal ukuran file: 5 MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Identitas -->
                <div class="form-group">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap Developer *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($admin['nama_lengkap'] ?? ''); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="gelar" class="form-label">Gelar / Jabatan Utama *</label>
                    <input type="text" id="gelar" name="gelar" class="form-control" value="<?= htmlspecialchars($admin['gelar'] ?? ''); ?>" placeholder="Contoh: Senior Full-Stack Web Developer" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Biografi Singkat (Hero Header)</label>
                    <textarea id="bio" name="bio" class="form-control" style="min-height: 80px;"><?= htmlspecialchars($admin['bio'] ?? ''); ?></textarea>
                </div>

                <!-- Detail "Tentang Saya" -->
                <div class="form-group">
                    <label for="tentang_lengkap" class="form-label">Deskripsi Lengkap "Tentang Saya" (Section Dedicated)</label>
                    <textarea id="tentang_lengkap" name="tentang_lengkap" class="form-control" style="min-height: 120px;" placeholder="Tuliskan latar belakang profesional, visi, dan filosofi pemrograman Anda..."><?= htmlspecialchars($admin['tentang_lengkap'] ?? ''); ?></textarea>
                </div>

                <!-- Statistik Highlights -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 24px;">
                    <div>
                        <label for="pengalaman_tahun" class="form-label">Tahun Pengalaman</label>
                        <input type="text" id="pengalaman_tahun" name="pengalaman_tahun" class="form-control" value="<?= htmlspecialchars($admin['pengalaman_tahun'] ?? '7+'); ?>" placeholder="Contoh: 7+">
                    </div>
                    <div>
                        <label for="total_proyek" class="form-label">Total Proyek</label>
                        <input type="text" id="total_proyek" name="total_proyek" class="form-control" value="<?= htmlspecialchars($admin['total_proyek'] ?? '5+'); ?>" placeholder="Contoh: 5+">
                    </div>
                    <div>
                        <label for="kepuasan_klien" class="form-label">Tingkat Kepuasan</label>
                        <input type="text" id="kepuasan_klien" name="kepuasan_klien" class="form-control" value="<?= htmlspecialchars($admin['kepuasan_klien'] ?? '100%'); ?>" placeholder="Contoh: 100%">
                    </div>
                </div>

                <div class="form-group">
                    <label for="skills" class="form-label">Daftar Keahlian / Tech Stack (Pisahkan dengan Koma)</label>
                    <input type="text" id="skills" name="skills" class="form-control" value="<?= htmlspecialchars($admin['skills'] ?? ''); ?>" placeholder="Contoh: PHP 8+, MySQL, HTML5 & Pure CSS3, REST API">
                    <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 4px;">Pemisah koma (<code>,</code>) akan diproses menjadi badge keahlian di halaman publik.</small>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-color); margin: 30px 0;">

                <!-- Informasi Kontak & Footer -->
                <h3 style="font-size: 1.1rem; margin-bottom: 15px; color: var(--text-main);">📞 Informasi Kontak &amp; Media Sosial (Footer)</h3>

                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat Lengkap (Footer)</label>
                    <input type="text" id="alamat" name="alamat" class="form-control" value="<?= htmlspecialchars($admin['alamat'] ?? 'Kertusoko, Krucil, Probolinggo, Jawa Timur'); ?>" placeholder="Contoh: Kertusoko, Krucil, Probolinggo, Jawa Timur">
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                        <input type="text" id="whatsapp" name="whatsapp" class="form-control" value="<?= htmlspecialchars($admin['whatsapp'] ?? '081337212405'); ?>" placeholder="Contoh: 081337212405">
                    </div>
                    <div>
                        <label for="email" class="form-label">Email Kontak</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? 'hudabismillah16@gmail.com'); ?>" placeholder="Contoh: hudabismillah16@gmail.com">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 24px;">
                    <div>
                        <label for="instagram" class="form-label">Instagram Username / Link</label>
                        <input type="text" id="instagram" name="instagram" class="form-control" value="<?= htmlspecialchars($admin['instagram'] ?? 'zm18099'); ?>" placeholder="Contoh: zm18099 atau instagram.com/zm18099">
                    </div>
                    <div>
                        <label for="github" class="form-label">GitHub Username / Link</label>
                        <input type="text" id="github" name="github" class="form-control" value="<?= htmlspecialchars($admin['github'] ?? 'SamsulHuda16'); ?>" placeholder="Contoh: SamsulHuda16 atau github.com/SamsulHuda16">
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-color); margin: 30px 0;">

                <!-- Form Ganti Password -->
                <h3 style="font-size: 1.1rem; margin-bottom: 15px; color: var(--text-main);">🔒 Ubah Password Admin (Opsional)</h3>

                <div class="form-group">
                    <label for="old_password" class="form-label">Password Lama</label>
                    <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Masukkan password lama jika ingin mengubah password">
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">💾 Simpan Perubahan Profil</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const toggleBtn = document.getElementById('adminNavToggle');
        const navMenu = document.getElementById('adminNavMenu');
        if (toggleBtn && navMenu) {
            toggleBtn.addEventListener('click', function() {
                this.classList.toggle('open');
                navMenu.classList.toggle('open');
            });
        }
    </script>
</body>
</html>

