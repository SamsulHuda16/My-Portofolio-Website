<?php
/**
 * Admin Panel - Tambah Sertifikat Baru (Create)
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul           = trim($_POST['judul'] ?? '');
    $penerbit        = trim($_POST['penerbit'] ?? '');
    $tahun           = trim($_POST['tahun'] ?? '');
    $deskripsi       = trim($_POST['deskripsi'] ?? '');
    $link_kredensial = trim($_POST['link_kredensial'] ?? '');

    if (empty($judul) || empty($penerbit) || empty($tahun)) {
        $error_msg = 'Judul sertifikat, Penerbit, dan Tahun wajib diisi!';
    } elseif (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = 'File gambar sertifikat wajib diunggah!';
    } else {
        $file         = $_FILES['gambar'];
        $fileName     = $file['name'];
        $fileTmpName  = $file['tmp_name'];
        $fileSize     = $file['size'];
        $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt   = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'pdf'];
        $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

        if (!in_array($fileExt, $allowedExt)) {
            $error_msg = 'Format file tidak valid! Gunakan JPG, PNG, WEBP, SVG, atau PDF.';
        } elseif ($fileSize > $maxSizeBytes) {
            $error_msg = 'Ukuran file terlalu besar! Maksimal 5 MB.';
        } else {
            $newFileName = 'cert_' . time() . '_' . uniqid() . '.' . $fileExt;
            $uploadDir   = '../assets/uploads/';
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $destination)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO certificates (judul, penerbit, tahun, deskripsi, gambar, link_kredensial) VALUES (:judul, :penerbit, :tahun, :deskripsi, :gambar, :link_kredensial)");
                    $stmt->bindParam(':judul', $judul, PDO::PARAM_STR);
                    $stmt->bindParam(':penerbit', $penerbit, PDO::PARAM_STR);
                    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_STR);
                    $stmt->bindParam(':deskripsi', $deskripsi, PDO::PARAM_STR);
                    $stmt->bindParam(':gambar', $newFileName, PDO::PARAM_STR);
                    $stmt->bindParam(':link_kredensial', $link_kredensial, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        header("Location: sertifikat.php?msg=added");
                        exit;
                    } else {
                        $error_msg = 'Gagal menyimpan sertifikat ke database.';
                    }
                } catch (PDOException $e) {
                    $error_msg = 'Database Error: ' . $e->getMessage();
                }
            } else {
                $error_msg = 'Gagal mengunggah file ke server.';
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
    <title>Tambah Sertifikat Baru - Admin Panel</title>
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
                <li><a href="sertifikat.php" class="active">📜 Sertifikat</a></li>
                <li><a href="pengaturan.php">⚙️ Pengaturan</a></li>
                <li><a href="../index.php" target="_blank">🌐 Lihat Website</a></li>
                <li class="nav-user-item">
                    <div class="user-info">
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
        <div class="admin-header-action" style="max-width: 700px; margin: 0 auto 25px;">
            <h1 class="admin-title">Tambah Sertifikat Baru</h1>
            <a href="sertifikat.php" class="btn-secondary">&larr; Kembali</a>
        </div>

        <div class="form-card">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form action="sertifikat_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul" class="form-label">Nama / Judul Sertifikat *</label>
                    <input type="text" id="judul" name="judul" class="form-control" placeholder="Contoh: Full-Stack Web Developer Certification" value="<?= htmlspecialchars($_POST['judul'] ?? ''); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="penerbit" class="form-label">Institusi / Penerbit *</label>
                    <input type="text" id="penerbit" name="penerbit" class="form-control" placeholder="Contoh: Dicoding Academy / Google / Kominfo" value="<?= htmlspecialchars($_POST['penerbit'] ?? ''); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="tahun" class="form-label">Tahun Perolehan *</label>
                    <input type="text" id="tahun" name="tahun" class="form-control" placeholder="Contoh: 2025" value="<?= htmlspecialchars($_POST['tahun'] ?? ''); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi Singkat (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" placeholder="Jelaskan kompetensi yang diuji dalam sertifikasi ini..."><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="link_kredensial" class="form-label">Link Verifikasi / Kredensial (Opsional)</label>
                    <input type="url" id="link_kredensial" name="link_kredensial" class="form-control" placeholder="https://example.com/verify/12345" value="<?= htmlspecialchars($_POST['link_kredensial'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="gambar" class="form-label">File / Gambar Sertifikat *</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml" required>
                    <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 4px;">Format yang didukung: JPG, PNG, WEBP, SVG (Maksimal 5 MB).</small>
                </div>

                <div class="form-actions">
                    <a href="sertifikat.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Sertifikat</button>
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

