<?php
// ============================================================
// FILE: index.php
// Deskripsi: Halaman publik - Melihat daftar menu café (tanpa login)
// ============================================================

require_once 'config/database.php';

// Ambil semua data menu
$query = "SELECT * FROM menu ORDER BY kategori ASC, nama_menu ASC";
$result = $koneksi->query($query);

$data_menu = [];
$kategori_list = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_menu[] = $row;
        if (!in_array($row['kategori'], $kategori_list)) {
            $kategori_list[] = $row['kategori'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Café — Nikmati Hidangan Terbaik Kami</title>
    <meta name="description" content="Jelajahi menu café kami. Berbagai pilihan makanan, minuman, cemilan, dan dessert tersedia untuk Anda.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #c8a97e;
            --primary-dark: #a8845a;
            --primary-light: rgba(200, 169, 126, 0.15);
            --primary-glow: rgba(200, 169, 126, 0.25);
            --dark-bg: #0a0c10;
            --dark-bg-2: #0f1117;
            --card-bg: #151821;
            --card-bg-hover: #1a1e2a;
            --card-border: rgba(200, 169, 126, 0.08);
            --text-main: #e8e6e3;
            --text-muted: #8b8d94;
            --success: #2ecc71;
            --info: #3498db;
            --warning: #f39c12;
            --purple: #9b59b6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            position: relative;
            padding: 80px 0 60px;
            overflow: hidden;
            background: var(--dark-bg-2);
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -30%;
            width: 160%;
            height: 200%;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(200, 169, 126, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 30%, rgba(200, 169, 126, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 90%, rgba(200, 169, 126, 0.03) 0%, transparent 50%);
            animation: heroBgShift 25s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes heroBgShift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(2%, -3%) scale(1.02); }
            66% { transform: translate(-1%, 2%) scale(0.98); }
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to top, var(--dark-bg), transparent);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .hero-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #fff;
            margin-bottom: 24px;
            box-shadow: 0 12px 40px rgba(200, 169, 126, 0.35);
            animation: iconFloat 4s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0); box-shadow: 0 12px 40px rgba(200, 169, 126, 0.35); }
            50% { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(200, 169, 126, 0.45); }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--primary), #e8c99b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 500px;
            margin: 0 auto 32px;
            line-height: 1.7;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            display: block;
        }

        .hero-stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* ===== NAVBAR (sticky) ===== */
        .navbar-public {
            background: rgba(15, 17, 23, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--card-border);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-public.scrolled {
            background: rgba(15, 17, 23, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon-sm {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(200, 169, 126, 0.3);
        }

        .brand-text {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-main);
        }

        .brand-text span { color: var(--primary); }

        .btn-admin-login {
            background: rgba(200, 169, 126, 0.1);
            border: 1px solid rgba(200, 169, 126, 0.25);
            color: var(--primary) !important;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 8px 18px;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-admin-login:hover {
            background: rgba(200, 169, 126, 0.2);
            transform: translateY(-1px);
            color: var(--primary) !important;
        }

        /* ===== FILTER SECTION ===== */
        .filter-section {
            padding: 40px 0 20px;
        }

        .filter-pills {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-pill {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.88rem;
            padding: 10px 22px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .filter-pill:hover {
            border-color: rgba(200, 169, 126, 0.3);
            color: var(--text-main);
            transform: translateY(-1px);
        }

        .filter-pill.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 20px rgba(200, 169, 126, 0.3);
        }

        .filter-pill .emoji {
            font-size: 1.1rem;
        }

        /* ===== MENU GRID ===== */
        .menu-section {
            padding: 20px 0 80px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .menu-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            animation: cardFadeIn 0.6s ease forwards;
            opacity: 0;
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow:
                0 20px 50px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(200, 169, 126, 0.15);
            border-color: rgba(200, 169, 126, 0.2);
        }

        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .menu-card:hover .menu-card-img {
            transform: scale(1.05);
        }

        .menu-card-img-wrapper {
            overflow: hidden;
            position: relative;
        }

        .menu-card-img-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: linear-gradient(to top, var(--card-bg), transparent);
        }

        .menu-card-img-placeholder {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, rgba(200, 169, 126, 0.08), rgba(200, 169, 126, 0.03));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: rgba(200, 169, 126, 0.3);
        }

        .menu-card-body {
            padding: 20px 24px 24px;
        }

        .menu-card-kategori {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .kategori-Makanan { background: rgba(46, 204, 113, 0.12); color: #2ecc71; }
        .kategori-Minuman { background: rgba(52, 152, 219, 0.12); color: #3498db; }
        .kategori-Cemilan { background: rgba(243, 156, 18, 0.12); color: #f39c12; }
        .kategori-Dessert { background: rgba(155, 89, 182, 0.12); color: #9b59b6; }

        .menu-card-nama {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .menu-card-harga {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #e8c99b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 16px;
            color: rgba(200, 169, 126, 0.2);
        }

        .empty-state h5 {
            color: var(--text-main);
            margin-bottom: 8px;
        }

        /* ===== FOOTER ===== */
        .footer-public {
            background: var(--dark-bg-2);
            border-top: 1px solid var(--card-border);
            padding: 40px 0;
            text-align: center;
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .footer-text a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .hero-subtitle { font-size: 0.95rem; }
            .hero-section { padding: 50px 0 40px; }
            .menu-grid { grid-template-columns: 1fr 1fr; gap: 16px; }
            .menu-card-img, .menu-card-img-placeholder { height: 160px; }
            .menu-card-body { padding: 14px 16px 18px; }
            .menu-card-nama { font-size: 0.95rem; }
            .menu-card-harga { font-size: 1.1rem; }
            .hero-stats { gap: 24px; }
        }

        @media (max-width: 480px) {
            .menu-grid { grid-template-columns: 1fr; }
            .hero-stats { gap: 20px; }
        }

        /* ===== SCROLL REVEAL ===== */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-public" id="mainNavbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="index.php" class="navbar-brand-custom">
                    <div class="brand-icon-sm">
                        <i class="bi bi-cup-hot-fill"></i>
                    </div>
                    <div class="brand-text">Café<span>Menu</span></div>
                </a>
                <a href="login.php" class="btn-admin-login">
                    <i class="bi bi-shield-lock-fill"></i> Login Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="bi bi-cup-hot-fill"></i>
                </div>
                <h1 class="hero-title">Jelajahi <span>Menu</span> Kami</h1>
                <p class="hero-subtitle">Temukan berbagai pilihan hidangan istimewa yang kami sajikan dengan penuh cinta dan bahan-bahan berkualitas terbaik.</p>
                
                <?php if (count($data_menu) > 0): ?>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?= count($data_menu) ?></span>
                        <span class="hero-stat-label">Total Menu</span>
                    </div>
                    <?php
                    $kategori_icons = [
                        'Makanan' => '🍛',
                        'Minuman' => '🥤',
                        'Cemilan' => '🍿',
                        'Dessert' => '🍰'
                    ];
                    foreach ($kategori_list as $kat):
                        $count = count(array_filter($data_menu, fn($m) => $m['kategori'] === $kat));
                    ?>
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?= $count ?></span>
                        <span class="hero-stat-label"><?= htmlspecialchars($kat) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (count($data_menu) > 0): ?>
    <!-- ===== FILTER ===== -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-pills">
                <button class="filter-pill active" data-filter="all">
                    <span class="emoji">☕</span> Semua Menu
                </button>
                <?php foreach ($kategori_list as $kat): ?>
                <button class="filter-pill" data-filter="<?= htmlspecialchars($kat) ?>">
                    <span class="emoji"><?= $kategori_icons[$kat] ?? '🍽️' ?></span> <?= htmlspecialchars($kat) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ===== MENU GRID ===== -->
    <section class="menu-section">
        <div class="container">
            <div class="menu-grid" id="menuGrid">
                <?php foreach ($data_menu as $index => $menu): ?>
                <div class="menu-card" data-kategori="<?= htmlspecialchars($menu['kategori']) ?>" style="animation-delay: <?= $index * 0.08 ?>s;">
                    <div class="menu-card-img-wrapper">
                        <?php
                        $gambar_path = 'uploads/' . $menu['gambar'];
                        if (!empty($menu['gambar']) && file_exists($gambar_path)):
                        ?>
                            <img src="<?= htmlspecialchars($gambar_path) ?>" 
                                 alt="<?= htmlspecialchars($menu['nama_menu']) ?>" 
                                 class="menu-card-img"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="menu-card-img-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="menu-card-body">
                        <div class="menu-card-kategori kategori-<?= htmlspecialchars($menu['kategori']) ?>">
                            <?= $kategori_icons[$menu['kategori']] ?? '🍽️' ?> <?= htmlspecialchars($menu['kategori']) ?>
                        </div>
                        <h3 class="menu-card-nama"><?= htmlspecialchars($menu['nama_menu']) ?></h3>
                        <div class="menu-card-harga">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php else: ?>
    <!-- ===== EMPTY STATE ===== -->
    <section class="menu-section">
        <div class="container">
            <div class="empty-state">
                <i class="bi bi-cup"></i>
                <h5>Menu Belum Tersedia</h5>
                <p>Kami sedang menyiapkan menu terbaik untuk Anda. Silakan kembali lagi nanti!</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== FOOTER ===== -->
    <footer class="footer-public">
        <div class="container">
            <p class="footer-text">
                &copy; 2026 <a href="index.php">CaféMenu</a> — Semua Hak Dilindungi.
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== Navbar scroll effect =====
        const navbar = document.getElementById('mainNavbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ===== Category Filter =====
        const filterPills = document.querySelectorAll('.filter-pill');
        const menuCards = document.querySelectorAll('.menu-card');

        filterPills.forEach(pill => {
            pill.addEventListener('click', () => {
                // Update active state
                filterPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');

                const filter = pill.dataset.filter;

                menuCards.forEach((card, idx) => {
                    if (filter === 'all' || card.dataset.kategori === filter) {
                        card.style.display = '';
                        card.style.animation = `cardFadeIn 0.5s ease ${idx * 0.05}s forwards`;
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // ===== Scroll reveal =====
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>
<?php
// Tutup koneksi
if (isset($koneksi)) {
    $koneksi->close();
}
?>
