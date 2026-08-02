<?php
/**
 * Frontend Halaman Publik Portofolio
 * Menampilkan Profil Developer, Section Tentang Saya, Hasil Proyek, & Sertifikat Penghargaan secara Dinamis dari MySQL Database
 */
require_once 'koneksi.php';

try {
    // Ambil data profil admin/developer
    $stmtProfile = $pdo->prepare("SELECT * FROM admin LIMIT 1");
    $stmtProfile->execute();
    $profile = $stmtProfile->fetch();

    $nama_lengkap     = $profile['nama_lengkap'] ?? 'Huda';
    $gelar            = $profile['gelar'] ?? 'Senior Full-Stack Web Developer';
    $bio              = $profile['bio'] ?? 'Saya spesialis dalam membangun aplikasi web yang aman, cepat, dan terstruktur.';
    $tentang_lengkap  = $profile['tentang_lengkap'] ?? 'Saya seorang Full-Stack Web Developer yang berdedikasi tinggi dalam menciptakan aplikasi web berkualitas tinggi, terstruktur, dan efisien. Dengan pemahaman mendalam tentang arsitektur PHP Native (PDO), perancangan database relasional MySQL, dan penulisan kode HTML5 & Pure CSS3 murni tanpa bergantung pada framework berat, saya memastikan setiap aplikasi berjalan cepat, aman dari celah keamanan, serta memiliki antarmuka pengguna yang responsif dan modern.';
    
    $pengalaman_tahun = $profile['pengalaman_tahun'] ?? '7+';
    $kepuasan_klien   = $profile['kepuasan_klien'] ?? '100%';

    $foto_profil      = (!empty($profile['foto_profil']) && file_exists('assets/uploads/' . $profile['foto_profil'])) 
                        ? 'assets/uploads/' . $profile['foto_profil'] 
                        : 'assets/uploads/profile.svg';
    
    $skills_raw       = $profile['skills'] ?? 'PHP 8+ (PDO), MySQL / MariaDB, HTML5 & Pure CSS3, Laravel, CodeIgniter 3, Security & Auth, REST API';
    $skills_list      = array_filter(array_map('trim', explode(',', $skills_raw)));

    $alamat           = !empty($profile['alamat']) ? $profile['alamat'] : 'Kertusoko, Krucil, Probolinggo, Jawa Timur';
    $whatsapp         = !empty($profile['whatsapp']) ? $profile['whatsapp'] : '081337212405';
    $email            = !empty($profile['email']) ? $profile['email'] : 'hudabismillah16@gmail.com';
    $instagram        = !empty($profile['instagram']) ? $profile['instagram'] : 'zm18099';
    $github           = !empty($profile['github']) ? $profile['github'] : 'SamsulHuda16';

    // Format Nomor WhatsApp untuk Link wa.me
    $wa_clean = preg_replace('/[^0-9]/', '', $whatsapp);
    if (strpos($wa_clean, '0') === 0) {
        $wa_clean = '62' . substr($wa_clean, 1);
    }

    // Format Link & Text Display Instagram
    $ig_username = trim(str_replace(['https://instagram.com/', 'http://instagram.com/', 'instagram.com/', '@'], '', $instagram), '/');
    $ig_link     = !empty($ig_username) ? "https://instagram.com/" . urlencode($ig_username) : "#";
    $ig_display  = !empty($ig_username) ? "instagram.com/" . $ig_username : "Instagram";

    // Format Link & Text Display GitHub
    $gh_username = trim(str_replace(['https://github.com/', 'http://github.com/', 'github.com/', '@'], '', $github), '/');
    $gh_link     = !empty($gh_username) ? "https://github.com/" . urlencode($gh_username) : "#";
    $gh_display  = !empty($gh_username) ? "github.com/" . $gh_username : "GitHub";

    // Ambil seluruh data proyek dari database
    $stmtProjects = $pdo->prepare("SELECT * FROM projects ORDER BY id DESC");
    $stmtProjects->execute();
    $projects = $stmtProjects->fetchAll();

    // Hitung total proyek secara OTOMATIS dari tabel projects
    $stmtCount = $pdo->prepare("SELECT COUNT(*) as total FROM projects");
    $stmtCount->execute();
    $countResult  = $stmtCount->fetch();
    $jumlah       = (int) $countResult['total'];
    $total_proyek = $jumlah > 0 ? $jumlah . '+' : '0';

    // Ambil data sertifikat & penghargaan
    $stmtCert = $pdo->prepare("SELECT * FROM certificates ORDER BY id DESC");
    $stmtCert->execute();
    $certificates = $stmtCert->fetchAll();
} catch (PDOException $e) {
    $projects = [];
    $certificates = [];
    $error_msg = "Gagal mengambil data dari database: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portofolio & Showcase Proyek Pengembangan Aplikasi Web Full-Stack oleh <?= htmlspecialchars($nama_lengkap); ?>.">
    <meta name="author" content="<?= htmlspecialchars($nama_lengkap); ?>">
    <title><?= htmlspecialchars($nama_lengkap); ?> - Full-Stack Developer Portofolio</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/uploads/a6235286-ebd9-45ba-a4c7-4ea4fb42eb4a-removebg-preview.png?v=<?= time(); ?>">
    <!-- Google Fonts & Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Ambient Background Glows -->
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="brand-logo">
                <span><?= htmlspecialchars($nama_lengkap); ?>.Developer</span>
            </a>

            <!-- Mobile Menu Toggle Button -->
            <button class="nav-toggle" id="navToggle" aria-label="Buka Menu Navigasi">
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </button>

            <ul class="nav-links" id="navMenu">
                <li><a href="#about" class="nav-link">Profil</a></li>
                <li><a href="#about-me" class="nav-link">Tentang Saya</a></li>
                <li><a href="#projects" class="nav-link">Hasil Proyek</a></li>
                <li><a href="#certificates" class="nav-link">Sertifikat</a></li>
                <li>
                    <a href="login.php" class="btn-admin">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Admin Panel
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero / Profile Section -->
    <header class="hero-section" id="about">
        <div class="container">
            <div class="profile-hero-wrapper">
                <div class="profile-avatar-container">
                    <img src="<?= htmlspecialchars($foto_profil); ?>" alt="<?= htmlspecialchars($nama_lengkap); ?> Profile Photo" class="profile-avatar-img">
                    <span class="status-indicator" title="Available for Freelance & Full-time"></span>
                </div>
                <div class="profile-bio">
                    <span class="hero-tag">👋 Halo, Saya <?= htmlspecialchars($nama_lengkap); ?></span>
                    <h1 class="hero-title">
                        <span class="gradient-text"><?= htmlspecialchars($gelar); ?></span>
                    </h1>
                    <p class="hero-desc">
                        <?= nl2br(htmlspecialchars($bio)); ?>
                    </p>

                    <!-- Dynamic Tech Stack Badges -->
                    <?php if (!empty($skills_list)): ?>
                        <div class="tech-stack-row">
                            <?php foreach ($skills_list as $skill): ?>
                                <span class="tech-chip">⚡ <?= htmlspecialchars($skill); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Dedicated "Tentang Saya" Section - Premium Redesign -->
    <section class="about-me-section" id="about-me">
        <div class="container">

            <!-- Section Header Centered -->
            <div class="about-header">
                <span class="hero-tag">🙋‍♂️ Tentang Saya</span>
                <h2 class="about-main-title">
                    Membangun Solusi Digital yang
                    <span class="gradient-text"> Cepat, Aman &amp; Elegan</span>
                </h2>
            </div>

            <!-- Top Row: Quote Block + Stats Row -->
            <div class="about-quote-row">
                <blockquote class="about-blockquote">
                    <span class="quote-mark">&ldquo;</span>
                    <p><?= nl2br(htmlspecialchars($tentang_lengkap)); ?></p>
                    <footer class="quote-author">
                        <img src="<?= htmlspecialchars($foto_profil); ?>" alt="<?= htmlspecialchars($nama_lengkap); ?>" class="quote-avatar">
                        <div>
                            <strong><?= htmlspecialchars($nama_lengkap); ?></strong>
                            <span><?= htmlspecialchars($gelar); ?></span>
                        </div>
                    </footer>
                </blockquote>

                <!-- Stat Cards (vertical) -->
                <div class="about-stat-col">
                    <div class="stat-pill">
                        <div class="stat-pill-number"><?= htmlspecialchars($pengalaman_tahun); ?></div>
                        <div class="stat-pill-info">
                            <span class="stat-pill-label">Tahun</span>
                            <span class="stat-pill-desc">Pengalaman Dev</span>
                        </div>
                    </div>
                    <div class="stat-pill">
                        <div class="stat-pill-number" style="background: linear-gradient(135deg, #10b981, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?= htmlspecialchars($total_proyek); ?></div>
                        <div class="stat-pill-info">
                            <span class="stat-pill-label">Proyek</span>
                            <span class="stat-pill-desc">Berhasil Rilis</span>
                        </div>
                    </div>
                    <div class="stat-pill">
                        <div class="stat-pill-number" style="background: linear-gradient(135deg, #f59e0b, #ef4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?= htmlspecialchars($kepuasan_klien); ?></div>
                        <div class="stat-pill-info">
                            <span class="stat-pill-label">Kualitas</span>
                            <span class="stat-pill-desc">Code Standard</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Skills + Values Grid -->
            <div class="about-bottom-grid">

                <!-- Tech Skill Bars -->
                <div class="skills-bar-section">
                    <h3 class="skills-bar-title">🛠️ Penguasaan Teknologi</h3>
                    <div class="skill-bar-list">
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>PHP Native (PDO)</span><span>95%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 95%; background: linear-gradient(90deg, #6366f1, #a855f7);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>MySQL / MariaDB</span><span>90%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 90%; background: linear-gradient(90deg, #10b981, #06b6d4);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>HTML5 &amp; Pure CSS3</span><span>92%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 92%; background: linear-gradient(90deg, #f59e0b, #ef4444);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>REST API &amp; Backend Security</span><span>85%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 85%; background: linear-gradient(90deg, #06b6d4, #6366f1);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>JavaScript (Vanilla)</span><span>78%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 78%; background: linear-gradient(90deg, #a855f7, #6366f1);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>Laravel (PHP Framework)</span><span>82%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 82%; background: linear-gradient(90deg, #ef4444, #f59e0b);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>CodeIgniter 3 (CI3)</span><span>88%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 88%; background: linear-gradient(90deg, #f59e0b, #10b981);"></div></div>
                        </div>
                        <div class="skill-bar-item">
                            <div class="skill-bar-meta"><span>Python (Backend / Scripting)</span><span>72%</span></div>
                            <div class="skill-bar-track"><div class="skill-bar-fill" style="width: 72%; background: linear-gradient(90deg, #3b82f6, #06b6d4);"></div></div>
                        </div>
                    </div>
                </div>

                <!-- Core Values Grid -->
                <div class="values-grid">
                    <h3 class="skills-bar-title">💡 Nilai-Nilai Profesional</h3>
                    <div class="values-list">
                        <div class="value-card">
                            <div class="value-icon-wrap" style="background: rgba(99,102,241,0.12); border-color: rgba(99,102,241,0.25);">🔒</div>
                            <div class="value-text">
                                <h4>Keamanan Kode</h4>
                                <p>PDO Prepared Statements, BCRYPT hashing, dan Session Guard untuk mencegah SQL Injection &amp; XSS sepenuhnya.</p>
                            </div>
                        </div>
                        <div class="value-card">
                            <div class="value-icon-wrap" style="background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.25);">⚡</div>
                            <div class="value-text">
                                <h4>Performa Optimal</h4>
                                <p>Pure HTML5 &amp; Vanilla CSS3 murni — tanpa framework berlebih, halaman berjalan ringan &amp; super cepat.</p>
                            </div>
                        </div>
                        <div class="value-card">
                            <div class="value-icon-wrap" style="background: rgba(6,182,212,0.12); border-color: rgba(6,182,212,0.25);">🛢️</div>
                            <div class="value-text">
                                <h4>Database Efisien</h4>
                                <p>Perancangan skema relasional MySQL yang efisien dengan integritas data dan query teroptimasi.</p>
                            </div>
                        </div>
                        <div class="value-card">
                            <div class="value-icon-wrap" style="background: rgba(168,85,247,0.12); border-color: rgba(168,85,247,0.25);">🚀</div>
                            <div class="value-text">
                                <h4>Arsitektur Rapi</h4>
                                <p>Pemisahan logika Backend (Admin Panel) &amp; Frontend publik secara bersih sesuai prinsip Clean Architecture.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Main Portfolio Projects Section -->
    <main class="portfolio-section" id="projects">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">💻 Hasil Proyek Saya</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 6px;">Berikut adalah beberapa aplikasi &amp; sistem yang telah berhasil saya kembangkan:</p>
                </div>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 16px; border-radius: 10px; margin-bottom: 30px;">
                    <?= htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <div class="projects-grid">
                <?php if (count($projects) > 0): ?>
                    <?php foreach ($projects as $project): ?>
                        <article class="project-card">
                            <div class="project-img-wrapper">
                                <?php 
                                    $image_path = 'assets/uploads/' . $project['gambar'];
                                    if (!empty($project['gambar']) && file_exists($image_path)): 
                                ?>
                                    <img src="<?= htmlspecialchars($image_path); ?>" alt="<?= htmlspecialchars($project['judul']); ?>" class="project-img" loading="lazy">
                                <?php else: ?>
                                    <div class="project-img-placeholder">
                                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span><?= htmlspecialchars($project['judul']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="project-body">
                                <h3 class="project-title"><?= htmlspecialchars($project['judul']); ?></h3>
                                <p class="project-desc"><?= nl2br(htmlspecialchars($project['deskripsi_teknis'])); ?></p>
                                
                                <div class="project-footer">
                                    <?php if (!empty($project['link_github'])): ?>
                                        <a href="<?= htmlspecialchars($project['link_github']); ?>" target="_blank" rel="noopener noreferrer" class="btn-github">
                                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                            </svg>
                                            Source Code &amp; Repositori
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 0.85rem; color: var(--text-dim);">Private Repository</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3>Belum Ada Proyek</h3>
                        <p>Daftar proyek portofolio akan ditampilkan di sini setelah ditambahkan melalui Admin Panel.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Certificates & Awards Section -->
    <section class="portfolio-section" id="certificates" style="padding-top: 10px; border-top: 1px solid var(--border-color);">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">📜 Sertifikat &amp; Penghargaan</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 6px;">Lisensi profesional &amp; sertifikasi kompetensi yang telah dicapai:</p>
                </div>
            </div>

            <div class="projects-grid">
                <?php if (count($certificates) > 0): ?>
                    <?php foreach ($certificates as $cert): ?>
                        <?php $cert_img_path = 'assets/uploads/' . $cert['gambar']; ?>
                        <article class="project-card cert-card-item cert-img-trigger"
                                 data-cert-img="<?= htmlspecialchars($cert_img_path); ?>"
                                 data-cert-title="<?= htmlspecialchars($cert['judul']); ?>"
                                 data-cert-issuer="<?= htmlspecialchars($cert['penerbit']); ?> (<?= htmlspecialchars($cert['tahun']); ?>)"
                                 style="cursor: pointer;"
                                 title="Klik untuk lihat Sertifikat Full">
                            <div class="project-img-wrapper" style="height: 210px; background: #0b0f19; position: relative;">
                                <?php if (!empty($cert['gambar']) && file_exists($cert_img_path)): ?>
                                    <img src="<?= htmlspecialchars($cert_img_path); ?>" alt="<?= htmlspecialchars($cert['judul']); ?>" class="project-img" loading="lazy">
                                    <div class="cert-zoom-overlay">
                                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                        <span>🔍 Klik Lihat Full Sertifikat</span>
                                    </div>
                                <?php else: ?>
                                    <div class="project-img-placeholder">
                                        <span style="font-size: 2.5rem;">📜</span>
                                        <span><?= htmlspecialchars($cert['judul']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="project-body">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <h3 class="project-title" style="font-size: 1.15rem; margin-bottom: 0;"><?= htmlspecialchars($cert['judul']); ?></h3>
                                    <span class="tech-badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.3);"><?= htmlspecialchars($cert['tahun']); ?></span>
                                </div>

                                <p style="font-size: 0.88rem; color: var(--accent); font-weight: 600; margin-bottom: 12px;">
                                    🏛️ <?= htmlspecialchars($cert['penerbit']); ?>
                                </p>

                                <?php if (!empty($cert['deskripsi'])): ?>
                                    <p class="project-desc" style="font-size: 0.88rem; margin-bottom: 16px;"><?= nl2br(htmlspecialchars($cert['deskripsi'])); ?></p>
                                <?php endif; ?>
                                
                                <div class="project-footer" style="margin-top: auto; display: flex; gap: 8px; flex-wrap: wrap;">
                                    <?php if (!empty($cert['gambar']) && file_exists($cert_img_path)): ?>
                                        <button class="btn-cert-main-trigger open-cert-modal-btn" 
                                                type="button"
                                                data-cert-img="<?= htmlspecialchars($cert_img_path); ?>"
                                                data-cert-title="<?= htmlspecialchars($cert['judul']); ?>"
                                                data-cert-issuer="<?= htmlspecialchars($cert['penerbit']); ?> (<?= htmlspecialchars($cert['tahun']); ?>)">
                                            🔍 Lihat Sertifikat Full
                                        </button>
                                    <?php endif; ?>

                                    <?php if (!empty($cert['link_kredensial'])): ?>
                                        <a href="<?= htmlspecialchars($cert['link_kredensial']); ?>" target="_blank" rel="noopener noreferrer" class="btn-credential-link" onclick="event.stopPropagation();">
                                            🔗 Verifikasi Kredensial
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <span style="font-size: 2.5rem; display: block; margin-bottom: 10px;">📜</span>
                        <h3>Belum Ada Sertifikat</h3>
                        <p>Sertifikat &amp; penghargaan akan ditampilkan di sini setelah ditambahkan dari Admin Panel.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Column 1: Brand & Address & Quick Consultation -->
                <div class="footer-col brand-col">
                    <a href="index.php" class="footer-logo">
                        <span><?= htmlspecialchars($nama_lengkap); ?>.dev</span>
                        <span class="brand-badge">Portofolio</span>
                    </a>
                    <p class="footer-bio">
                        Full-Stack Web Developer yang berfokus pada pengembangan sistem web berkinerja tinggi, aman, dan responsif.
                    </p>
                    
                    <div class="footer-contact-info">
                        <div class="info-item">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span><strong>Alamat:</strong> <?= htmlspecialchars($alamat); ?></span>
                        </div>
                    </div>

                    <div class="footer-cta-box">
                        <span class="cta-label">💡 Butuh Solusi Web?</span>
                        <a href="https://wa.me/<?= htmlspecialchars($wa_clean); ?>?text=Halo%20<?= urlencode($nama_lengkap); ?>,%20saya%20ingin%20konsultasi%20mengenai%20proyek%20web" target="_blank" rel="noopener noreferrer" class="btn-wa-consult">
                            💬 Konsultasi WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Column 2: Company -->
                <div class="footer-col">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="#about-me">Tentang</a></li>
                        <li><a href="#projects">Portofolio</a></li>
                        <li><a href="#certificates">Karir &amp; Sertifikat</a></li>
                        <li><a href="#projects" class="footer-highlight-link">[Lihat Portofolio]</a></li>
                    </ul>
                </div>

                <!-- Column 3: Kontak -->
                <div class="footer-col">
                    <h4 class="footer-title">Kontak</h4>
                    <ul class="footer-contact-list">
                        <li>
                            <a href="https://wa.me/<?= htmlspecialchars($wa_clean); ?>?text=Halo%20<?= urlencode($nama_lengkap); ?>,%20saya%20ingin%20bertanya" target="_blank" rel="noopener noreferrer" class="contact-link wa-link">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>WhatsApp: <?= htmlspecialchars($whatsapp); ?> <small style="display:block; color: #34d399;">(Chat sekarang)</small></span>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:<?= htmlspecialchars($email); ?>" class="contact-link">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Email: <?= htmlspecialchars($email); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= htmlspecialchars($ig_link); ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span>Instagram: <?= htmlspecialchars($ig_display); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= htmlspecialchars($gh_link); ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                </svg>
                                <span>GitHub: <?= htmlspecialchars($gh_display); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                
            </div>

            <!-- Footer Bottom Copyright Bar -->
            <div class="footer-bottom">
                <p>&copy; <?= date('Y'); ?> <?= htmlspecialchars($nama_lengkap); ?> Portofolio. Built with PHP Native, HTML5, Pure CSS &amp; MySQL.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Widget -->
    <a href="https://wa.me/<?= htmlspecialchars($wa_clean); ?>?text=Halo%20<?= urlencode($nama_lengkap); ?>,%20saya%20tertarik%20dengan%20portofolio%20Anda" target="_blank" rel="noopener noreferrer" class="floating-wa-btn" title="Chat via WhatsApp">
        <div class="wa-btn-pulse"></div>
        <svg width="28" height="28" fill="currentColor" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
        <span class="wa-tooltip">Hubungi WA Saya</span>
    </a>

    <!-- Modal Lightbox Preview Sertifikat Full -->
    <div class="cert-modal-overlay" id="certModalOverlay" aria-hidden="true">
        <div class="cert-modal-content">
            <button class="cert-modal-close" id="certModalClose" aria-label="Tutup Sertifikat">&times;</button>
            <div class="cert-modal-img-container">
                <img src="" alt="" id="certModalImg" class="cert-modal-img">
            </div>
            <div class="cert-modal-caption">
                <div>
                    <h3 id="certModalTitle" class="cert-modal-title"></h3>
                    <p id="certModalIssuer" class="cert-modal-issuer"></p>
                </div>
                <a href="" id="certModalOpenBtn" target="_blank" rel="noopener noreferrer" class="btn-cert-full">
                    🔍 Buka File Gambar ↗
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar Mobile Toggle
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');

            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    navToggle.classList.toggle('active');
                    navMenu.classList.toggle('active');
                });

                navMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function() {
                        navToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                    });
                });

                document.addEventListener('click', function(e) {
                    if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                        navToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                    }
                });
            }

            // Lightbox Modal Full Screen Sertifikat
            const modalOverlay = document.getElementById('certModalOverlay');
            const modalClose = document.getElementById('certModalClose');
            const modalImg = document.getElementById('certModalImg');
            const modalTitle = document.getElementById('certModalTitle');
            const modalIssuer = document.getElementById('certModalIssuer');
            const modalOpenBtn = document.getElementById('certModalOpenBtn');

            function openCertModal(imgSrc, title, issuer) {
                if (!imgSrc) return;
                modalImg.src = imgSrc;
                modalImg.alt = title || 'Sertifikat';
                modalTitle.textContent = title || 'Sertifikat';
                modalIssuer.textContent = issuer || '';
                modalOpenBtn.href = imgSrc;
                modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden'; // Stop scrolling background
            }

            function closeCertModal() {
                modalOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Bind click events on all cert-img-trigger and open-cert-modal-btn
            document.querySelectorAll('.cert-img-trigger, .open-cert-modal-btn').forEach(element => {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const imgSrc = this.getAttribute('data-cert-img');
                    const title = this.getAttribute('data-cert-title');
                    const issuer = this.getAttribute('data-cert-issuer');
                    openCertModal(imgSrc, title, issuer);
                });
            });

            if (modalClose) {
                modalClose.addEventListener('click', closeCertModal);
            }

            if (modalOverlay) {
                modalOverlay.addEventListener('click', function(e) {
                    if (e.target === modalOverlay) {
                        closeCertModal();
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
                    closeCertModal();
                }
            });
        });
    </script>

</body>
</html>
