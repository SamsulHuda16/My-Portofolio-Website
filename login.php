<?php
/**
 * Halaman Login Admin Panel
 * Menangani autentikasi user dengan password_verify & PHP Session
 */
session_start();
require_once 'koneksi.php';

// Jika admin sudah login, redirect langsung ke Dashboard Admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/index.php");
    exit;
}

$error_msg = '';

// Proses form login saat metode request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_msg = 'Username dan Password wajib diisi!';
    } else {
        try {
            // Prepared Statement untuk mencegah SQL Injection
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $admin = $stmt->fetch();

            // Verifikasi Hash Password BCRYPT
            if ($admin && password_verify($password, $admin['password'])) {
                // Keamanan Session: Regenerate Session ID
                session_regenerate_id(true);

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $admin['id'];
                $_SESSION['admin_username']  = $admin['username'];

                header("Location: admin/index.php");
                exit;
            } else {
                $error_msg = 'Username atau Password salah!';
            }
        } catch (PDOException $e) {
            $error_msg = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Portofolio Management</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/uploads/favicon_circle.png?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Admin Login</h2>
                <p>Masuk untuk mengelola portofolio & proyek Anda</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username admin" required autofocus autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk Ke Dashboard
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px;">
                <a href="index.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
                    &larr; Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>

</body>
</html>
