<?php
/**
 * Admin Panel - Kelola Sertifikat & Penghargaan (Read)
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$nama_admin = $_SESSION['admin_username'] ?? 'Admin';
$certificates = [];
$error = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM certificates ORDER BY id DESC");
    $stmt->execute();
    $certificates = $stmt->fetchAll();

    $stmtAdmin = $pdo->prepare("SELECT * FROM admin WHERE id = :id LIMIT 1");
    $admin_id = $_SESSION['admin_id'] ?? 1;
    $stmtAdmin->bindParam(':id', $admin_id, PDO::PARAM_INT);
    $stmtAdmin->execute();
    $admin_data = $stmtAdmin->fetch();
    if ($admin_data && !empty($admin_data['nama_lengkap'])) {
        $nama_admin = $admin_data['nama_lengkap'];
    }
} catch (PDOException $e) {
    $error = "Terjadi kesalahan saat mengambil data sertifikat: " . $e->getMessage();
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sertifikat & Penghargaan - Admin Panel</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/uploads/favicon_circle.png?v=<?= time(); ?>">
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

            <!-- Navigation Links -->
            <ul class="admin-nav-links" id="adminNavMenu">
                <li><a href="index.php">📋 Data Proyek</a></li>
                <li><a href="sertifikat.php" class="active">📜 Sertifikat</a></li>
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

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header-action">
            <div>
                <h1 class="admin-title">📜 Kelola Sertifikat & Penghargaan</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola sertifikasi profesional, lisensi, dan penghargaan Anda.</p>
            </div>
            <a href="sertifikat_tambah.php" class="btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Sertifikat Baru
            </a>
        </div>

        <!-- Alert Notification -->
        <?php if ($msg === 'added'): ?>
            <div class="alert alert-success">✅ Sertifikat baru berhasil ditambahkan!</div>
        <?php elseif ($msg === 'updated'): ?>
            <div class="alert alert-success">✅ Data sertifikat berhasil diperbarui!</div>
        <?php elseif ($msg === 'deleted'): ?>
            <div class="alert alert-success">✅ Sertifikat dan file gambar berhasil dihapus!</div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Table Data Sertifikat -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Judul Sertifikat</th>
                            <th>Penerbit &amp; Tahun</th>
                            <th>Link Kredensial</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($certificates) > 0): ?>
                            <?php $no = 1; foreach ($certificates as $cert): ?>
                                <tr>
                                    <td data-label="No"><?= $no++; ?></td>
                                    <td data-label="Gambar">
                                        <?php 
                                            $img_file = '../assets/uploads/' . $cert['gambar'];
                                            if (!empty($cert['gambar']) && file_exists($img_file)): 
                                        ?>
                                            <img src="<?= htmlspecialchars($img_file); ?>" alt="Thumb" class="thumb-preview">
                                        <?php else: ?>
                                            <span style="font-size: 0.75rem; color: var(--text-dim);">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Judul Sertifikat" style="font-weight: 600; color: var(--text-main);">
                                        <?= htmlspecialchars($cert['judul']); ?>
                                    </td>
                                    <td data-label="Penerbit & Tahun" style="color: var(--text-muted);">
                                        <strong><?= htmlspecialchars($cert['penerbit']); ?></strong> (<?= htmlspecialchars($cert['tahun']); ?>)
                                    </td>
                                    <td data-label="Link Kredensial">
                                        <?php if (!empty($cert['link_kredensial'])): ?>
                                            <a href="<?= htmlspecialchars($cert['link_kredensial']); ?>" target="_blank" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                                Lihat Kredensial &rarr;
                                            </a>
                                        <?php else: ?>
                                            <span style="color: var(--text-dim); font-size: 0.85rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Aksi" style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="sertifikat_edit.php?id=<?= $cert['id']; ?>" class="btn-edit">Edit</a>
                                            <a href="sertifikat_hapus.php?id=<?= $cert['id']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus sertifikat ini?');">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    Belum ada data sertifikat. Klik tombol <strong>"Tambah Sertifikat Baru"</strong> untuk menambahkan data.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
