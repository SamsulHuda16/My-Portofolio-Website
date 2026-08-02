<?php
/**
 * Admin Panel - Edit Proyek (Update)
 * Memperbarui data proyek dan menangani pergantian file gambar opsional
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$nama_admin = $_SESSION['admin_username'] ?? 'Admin';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data proyek dari database
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch();

    if (!$project) {
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul            = trim($_POST['judul'] ?? '');
    $deskripsi_teknis = trim($_POST['deskripsi_teknis'] ?? '');
    $link_github      = trim($_POST['link_github'] ?? '');

    if (empty($judul) || empty($deskripsi_teknis)) {
        $error_msg = 'Judul proyek dan Deskripsi teknis wajib diisi!';
    } else {
        $gambarName = $project['gambar']; // Default gunakan gambar lama

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file         = $_FILES['gambar'];
            $fileName     = $file['name'];
            $fileTmpName  = $file['tmp_name'];
            $fileSize     = $file['size'];
            $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExt   = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

            if (!in_array($fileExt, $allowedExt)) {
                $error_msg = 'Ekstensi file gambar tidak valid!';
            } elseif ($fileSize > $maxSizeBytes) {
                $error_msg = 'Ukuran file gambar terlalu besar! Maksimal 5 MB.';
            } else {
                $newFileName = time() . '_' . uniqid() . '.' . $fileExt;
                $uploadDir   = '../assets/uploads/';
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpName, $destination)) {
                    $oldFilePath = $uploadDir . $project['gambar'];
                    if (!empty($project['gambar']) && file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                    $gambarName = $newFileName;
                } else {
                    $error_msg = 'Gagal mengunggah gambar baru ke server.';
                }
            }
        }

        if (empty($error_msg)) {
            try {
                $updateStmt = $pdo->prepare("UPDATE projects SET judul = :judul, deskripsi_teknis = :deskripsi_teknis, link_github = :link_github, gambar = :gambar WHERE id = :id");
                $updateStmt->bindParam(':judul', $judul, PDO::PARAM_STR);
                $updateStmt->bindParam(':deskripsi_teknis', $deskripsi_teknis, PDO::PARAM_STR);
                $updateStmt->bindParam(':link_github', $link_github, PDO::PARAM_STR);
                $updateStmt->bindParam(':gambar', $gambarName, PDO::PARAM_STR);
                $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($updateStmt->execute()) {
                    header("Location: index.php?msg=updated");
                    exit;
                } else {
                    $error_msg = 'Gagal memperbarui data proyek di database.';
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
    <title>Edit Proyek - Admin Panel</title>
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
            <h1 class="admin-title">Edit Proyek</h1>
            <a href="index.php" class="btn-secondary">&larr; Kembali</a>
        </div>

        <div class="form-card">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form action="edit.php?id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul" class="form-label">Judul Proyek *</label>
                    <input type="text" id="judul" name="judul" class="form-control" value="<?= htmlspecialchars($project['judul']); ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="deskripsi_teknis" class="form-label">Deskripsi Teknis *</label>
                    <textarea id="deskripsi_teknis" name="deskripsi_teknis" class="form-control" required><?= htmlspecialchars($project['deskripsi_teknis']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="link_github" class="form-label">Link Repositori GitHub (Opsional)</label>
                    <input type="url" id="link_github" name="link_github" class="form-control" value="<?= htmlspecialchars($project['link_github']); ?>">
                </div>

                <div class="form-group">
                    <label for="gambar" class="form-label">Ganti Gambar Proyek (Biarkan kosong jika tidak diubah)</label>
                    
                    <?php 
                        $img_file = '../assets/uploads/' . $project['gambar'];
                        if (!empty($project['gambar']) && file_exists($img_file)): 
                    ?>
                        <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.04); padding: 10px; border-radius: 8px;">
                            <img src="<?= htmlspecialchars($img_file); ?>" alt="Preview Current" style="width: 80px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.85rem; color: var(--text-muted);">Gambar saat ini: <code><?= htmlspecialchars($project['gambar']); ?></code></span>
                        </div>
                    <?php endif; ?>

                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Update Proyek</button>
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

