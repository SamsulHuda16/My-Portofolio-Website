<?php
/**
 * Admin Panel - Tambah Proyek Baru (Create)
 * Menangani input formulir proyek baru dan pengunggahan file gambar secara aman
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$nama_admin = $_SESSION['admin_username'] ?? 'Admin';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul            = trim($_POST['judul'] ?? '');
    $deskripsi_teknis = trim($_POST['deskripsi_teknis'] ?? '');
    $link_github      = trim($_POST['link_github'] ?? '');

    if (empty($judul) || empty($deskripsi_teknis)) {
        $error_msg = 'Judul proyek dan Deskripsi teknis wajib diisi!';
    } elseif (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = 'Gambar proyek wajib diunggah!';
    } else {
        $file         = $_FILES['gambar'];
        $fileName     = $file['name'];
        $fileTmpName  = $file['tmp_name'];
        $fileSize     = $file['size'];
        $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt   = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

        if (!in_array($fileExt, $allowedExt)) {
            $error_msg = 'Ekstensi file gambar tidak valid! Format yang diperbolehkan: JPG, JPEG, PNG, WEBP, SVG.';
        } elseif ($fileSize > $maxSizeBytes) {
            $error_msg = 'Ukuran file gambar terlalu besar! Maksimal 5 MB.';
        } else {
            $newFileName = time() . '_' . uniqid() . '.' . $fileExt;
            $uploadDir   = '../assets/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $destination)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO projects (judul, deskripsi_teknis, link_github, gambar) VALUES (:judul, :deskripsi_teknis, :link_github, :gambar)");
                    $stmt->bindParam(':judul', $judul, PDO::PARAM_STR);
                    $stmt->bindParam(':deskripsi_teknis', $deskripsi_teknis, PDO::PARAM_STR);
                    $stmt->bindParam(':link_github', $link_github, PDO::PARAM_STR);
                    $stmt->bindParam(':gambar', $newFileName, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        header("Location: index.php?msg=added");
                        exit;
                    } else {
                        $error_msg = 'Gagal menyimpan data ke database.';
                    }
                } catch (PDOException $e) {
                    $error_msg = 'Database Error: ' . $e->getMessage();
                }
            } else {
                $error_msg = 'Gagal mengunggah file gambar ke server.';
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
    <title>Tambah Proyek Baru - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <!-- Header Navbar Admin -->
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
                <li><a href="index.php" class="active">📋 Data Proyek</a></li>
                <li><a href="sertifikat.php">📜 Sertifikat</a></li>
                <li><a href="pengaturan.php">⚙️ Pengaturan</a></li>
                <li><a href="../index.php" target="_blank">🌐 Lihat Website</a></li>
                <li class="nav-user-item">
                    <div class="user-info">
                        <div class="user-avatar-badge">
                            <div class="user-avatar-icon">👤</div>
                            <span class="user-name"><?= htmlspecialchars((string)$nama_admin); ?></span>
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

    <!-- Main Container -->
    <main class="admin-main">
        <div class="admin-header-action" style="max-width: 700px; margin: 0 auto 25px;">
            <h1 class="admin-title">Tambah Proyek Baru</h1>
            <a href="index.php" class="btn-secondary">&larr; Kembali</a>
        </div>

        <div class="form-card">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form action="tambah.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul" class="form-label">Judul Proyek *</label>
                    <input type="text" id="judul" name="judul" class="form-control" placeholder="Contoh: E-Commerce Marketplace API" value="<?= htmlspecialchars($_POST['judul'] ?? ''); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="deskripsi_teknis" class="form-label">Deskripsi Teknis *</label>
                    <textarea id="deskripsi_teknis" name="deskripsi_teknis" class="form-control" placeholder="Jelaskan fitur, arsitektur, dan teknologi yang digunakan dalam proyek ini..." required><?= htmlspecialchars($_POST['deskripsi_teknis'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="link_github" class="form-label">Link Repositori GitHub (Opsional)</label>
                    <input type="url" id="link_github" name="link_github" class="form-control" placeholder="https://github.com/username/repository" value="<?= htmlspecialchars($_POST['link_github'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="gambar" class="form-label">Gambar Proyek / Screenshot *</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml" required>
                    <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 4px;">Format yang didukung: JPG, PNG, WEBP, SVG (Maksimal 5 MB).</small>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Proyek</button>
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

