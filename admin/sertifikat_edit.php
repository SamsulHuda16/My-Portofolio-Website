<?php
/**
 * Admin Panel - Edit Sertifikat (Update)
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: sertifikat.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $cert = $stmt->fetch();

    if (!$cert) {
        header("Location: sertifikat.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul           = trim($_POST['judul'] ?? '');
    $penerbit        = trim($_POST['penerbit'] ?? '');
    $tahun           = trim($_POST['tahun'] ?? '');
    $deskripsi       = trim($_POST['deskripsi'] ?? '');
    $link_kredensial = trim($_POST['link_kredensial'] ?? '');

    if (empty($judul) || empty($penerbit) || empty($tahun)) {
        $error_msg = 'Judul sertifikat, Penerbit, dan Tahun wajib diisi!';
    } else {
        $gambarName = $cert['gambar'];

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file         = $_FILES['gambar'];
            $fileName     = $file['name'];
            $fileTmpName  = $file['tmp_name'];
            $fileSize     = $file['size'];
            $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExt   = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $maxSizeBytes = 5 * 1024 * 1024;

            if (!in_array($fileExt, $allowedExt)) {
                $error_msg = 'Format file tidak valid!';
            } elseif ($fileSize > $maxSizeBytes) {
                $error_msg = 'Ukuran file terlalu besar! Maksimal 5 MB.';
            } else {
                $newFileName = 'cert_' . time() . '_' . uniqid() . '.' . $fileExt;
                $uploadDir   = '../assets/uploads/';
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpName, $destination)) {
                    $oldFilePath = $uploadDir . $cert['gambar'];
                    if (!empty($cert['gambar']) && file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                    $gambarName = $newFileName;
                } else {
                    $error_msg = 'Gagal mengunggah file baru.';
                }
            }
        }

        if (empty($error_msg)) {
            try {
                $updateStmt = $pdo->prepare("UPDATE certificates SET judul = :judul, penerbit = :penerbit, tahun = :tahun, deskripsi = :deskripsi, gambar = :gambar, link_kredensial = :link_kredensial WHERE id = :id");
                $updateStmt->bindParam(':judul', $judul, PDO::PARAM_STR);
                $updateStmt->bindParam(':penerbit', $penerbit, PDO::PARAM_STR);
                $updateStmt->bindParam(':tahun', $tahun, PDO::PARAM_STR);
                $updateStmt->bindParam(':deskripsi', $deskripsi, PDO::PARAM_STR);
                $updateStmt->bindParam(':gambar', $gambarName, PDO::PARAM_STR);
                $updateStmt->bindParam(':link_kredensial', $link_kredensial, PDO::PARAM_STR);
                $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($updateStmt->execute()) {
                    header("Location: sertifikat.php?msg=updated");
                    exit;
                } else {
                    $error_msg = 'Gagal memperbarui data sertifikat.';
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
    <title>Edit Sertifikat - Admin Panel</title>
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
            <h1 class="admin-title">Edit Sertifikat</h1>
            <a href="sertifikat.php" class="btn-secondary">&larr; Kembali</a>
        </div>

        <div class="form-card">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form action="sertifikat_edit.php?id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul" class="form-label">Nama / Judul Sertifikat *</label>
                    <input type="text" id="judul" name="judul" class="form-control" value="<?= htmlspecialchars($cert['judul']); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="penerbit" class="form-label">Institusi / Penerbit *</label>
                    <input type="text" id="penerbit" name="penerbit" class="form-control" value="<?= htmlspecialchars($cert['penerbit']); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="tahun" class="form-label">Tahun Perolehan *</label>
                    <input type="text" id="tahun" name="tahun" class="form-control" value="<?= htmlspecialchars($cert['tahun']); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi Singkat (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control"><?= htmlspecialchars($cert['deskripsi']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="link_kredensial" class="form-label">Link Verifikasi / Kredensial (Opsional)</label>
                    <input type="url" id="link_kredensial" name="link_kredensial" class="form-control" value="<?= htmlspecialchars($cert['link_kredensial']); ?>">
                </div>

                <div class="form-group">
                    <label for="gambar" class="form-label">Ganti File Gambar (Biarkan kosong jika tidak diubah)</label>
                    <?php 
                        $img_file = '../assets/uploads/' . $cert['gambar'];
                        if (!empty($cert['gambar']) && file_exists($img_file)): 
                    ?>
                        <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.04); padding: 10px; border-radius: 8px;">
                            <img src="<?= htmlspecialchars($img_file); ?>" alt="Preview" style="width: 80px; height: 50px; object-fit: cover; border-radius: 6px;">
                            <span style="font-size: 0.85rem; color: var(--text-muted);">Gambar saat ini: <code><?= htmlspecialchars($cert['gambar']); ?></code></span>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                </div>

                <div class="form-actions">
                    <a href="sertifikat.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Update Sertifikat</button>
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

