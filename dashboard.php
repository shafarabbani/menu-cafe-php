<?php
// ============================================================
// FILE: dashboard.php
// Deskripsi: Dashboard Admin - Menampilkan daftar menu café (hanya admin)
// ============================================================

// Cek autentikasi
require_once 'auth_check.php';
require_once 'config/database.php';

// Pesan sukses dari operasi CRUD
$pesan_sukses = '';
if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    unset($_SESSION['pesan_sukses']);
}

// Ambil semua data menu
$query = "SELECT * FROM menu ORDER BY id DESC";
$result = $koneksi->query($query);

// Cek error query
if (!$result) {
    $error_db = "Gagal mengambil data menu: " . $koneksi->error;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Manajemen Menu Café</title>
    <meta name="description" content="Sistem Manajemen Menu Café - Dashboard Admin">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #c8a97e;
            --primary-dark: #a8845a;
            --primary-light: rgba(200, 169, 126, 0.15);
            --dark-bg: #0f1117;
            --sidebar-bg: #141720;
            --card-bg: #1a1d27;
            --card-bg-hover: #1f2230;
            --card-border: rgba(200, 169, 126, 0.1);
            --text-main: #e8e6e3;
            --text-muted: #8b8d94;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #3498db;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 16px 0;
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(200, 169, 126, 0.3);
        }

        .brand-text {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--text-main);
        }

        .brand-text span {
            color: var(--primary);
        }

        .nav-link-custom {
            color: var(--text-muted) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 10px 18px !important;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--primary) !important;
            background: var(--primary-light);
        }

        .btn-logout {
            background: rgba(231, 76, 60, 0.12);
            border: 1px solid rgba(231, 76, 60, 0.25);
            color: #e74c3c !important;
            font-weight: 500;
            padding: 10px 20px !important;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(231, 76, 60, 0.25);
            transform: translateY(-1px);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            padding: 32px 0;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== STATS CARDS ===== */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
            border-color: rgba(200, 169, 126, 0.3);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.makanan { background: rgba(46, 204, 113, 0.15); color: var(--success); }
        .stat-icon.minuman { background: rgba(52, 152, 219, 0.15); color: var(--info); }
        .stat-icon.cemilan { background: rgba(243, 156, 18, 0.15); color: var(--warning); }
        .stat-icon.total { background: var(--primary-light); color: var(--primary); }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 8px 0 2px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* ===== TABLE SECTION ===== */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .btn-tambah {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(200, 169, 126, 0.4);
            color: #fff;
        }

        .table-wrapper {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table thead th {
            background: rgba(200, 169, 126, 0.08);
            color: var(--primary);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--card-border);
            white-space: nowrap;
        }

        .table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            font-size: 0.9rem;
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--card-bg-hover);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .menu-img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--card-border);
            transition: transform 0.3s ease;
        }

        .menu-img:hover {
            transform: scale(1.5);
            z-index: 10;
            position: relative;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        .badge-kategori {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-Makanan { background: rgba(46, 204, 113, 0.15); color: #2ecc71; }
        .badge-Minuman { background: rgba(52, 152, 219, 0.15); color: #3498db; }
        .badge-Cemilan { background: rgba(243, 156, 18, 0.15); color: #f39c12; }
        .badge-Dessert { background: rgba(155, 89, 182, 0.15); color: #9b59b6; }

        .harga-text {
            font-weight: 700;
            color: var(--primary);
        }

        .btn-aksi {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 500;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background: rgba(52, 152, 219, 0.12);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.25);
        }

        .btn-edit:hover {
            background: rgba(52, 152, 219, 0.25);
            color: #3498db;
            transform: translateY(-1px);
        }

        .btn-hapus {
            background: rgba(231, 76, 60, 0.12);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.25);
        }

        .btn-hapus:hover {
            background: rgba(231, 76, 60, 0.25);
            color: #e74c3c;
            transform: translateY(-1px);
        }

        /* ===== ALERT ===== */
        .alert-success-custom {
            background: rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.25);
            color: #6deca9;
            border-radius: 14px;
            padding: 16px 24px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 16px;
            color: rgba(200, 169, 126, 0.3);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .table-wrapper { overflow-x: auto; }
            .stat-card { margin-bottom: 12px; }
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-custom">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- Brand -->
                <a href="dashboard.php" class="navbar-brand-custom">
                    <div class="brand-icon">
                        <i class="bi bi-cup-hot-fill"></i>
                    </div>
                    <div class="brand-text">Café<span>Menu</span></div>
                </a>

                <!-- Navigation -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="index.php" class="nav-link-custom" target="_blank">
                        <i class="bi bi-eye-fill"></i> Lihat Menu Publik
                    </a>
                    <a href="dashboard.php" class="nav-link-custom active">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                    <a href="tambah.php" class="nav-link-custom">
                        <i class="bi bi-plus-circle-fill"></i> Tambah Menu
                    </a>
                    <a href="logout.php" class="nav-link-custom btn-logout">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">
        <div class="container">
            <!-- Greeting -->
            <div class="mb-4">
                <h1 class="section-title" style="font-size:1.5rem;">
                    Selamat Datang, <span style="color:var(--primary);"><?= htmlspecialchars($_SESSION['username']) ?></span> 👋
                </h1>
                <p class="text-muted" style="color:var(--text-muted)!important;">Kelola menu café Anda dari satu tempat.</p>
            </div>

            <!-- Pesan Sukses -->
            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success-custom d-flex align-items-center mb-4">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <?= htmlspecialchars($pesan_sukses) ?>
                </div>
            <?php endif; ?>

            <!-- Error Database -->
            <?php if (isset($error_db)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($error_db) ?>
                </div>
            <?php else: ?>

            <?php
                // Hitung statistik
                $total = $result->num_rows;
                $count_makanan = 0;
                $count_minuman = 0;
                $count_cemilan = 0;
                $count_dessert = 0;
                $data_menu = [];

                while ($row = $result->fetch_assoc()) {
                    $data_menu[] = $row;
                    switch ($row['kategori']) {
                        case 'Makanan': $count_makanan++; break;
                        case 'Minuman': $count_minuman++; break;
                        case 'Cemilan': $count_cemilan++; break;
                        case 'Dessert': $count_dessert++; break;
                    }
                }
            ?>

            <!-- Stat Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon total"><i class="bi bi-grid-fill"></i></div>
                        <div class="stat-value"><?= $total ?></div>
                        <div class="stat-label">Total Menu</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon makanan"><i class="bi bi-egg-fried"></i></div>
                        <div class="stat-value"><?= $count_makanan ?></div>
                        <div class="stat-label">Makanan</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon minuman"><i class="bi bi-cup-straw"></i></div>
                        <div class="stat-value"><?= $count_minuman ?></div>
                        <div class="stat-label">Minuman</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon cemilan"><i class="bi bi-cake2-fill"></i></div>
                        <div class="stat-value"><?= $count_cemilan + $count_dessert ?></div>
                        <div class="stat-label">Cemilan & Dessert</div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="section-header">
                <h2 class="section-title"><i class="bi bi-menu-button-wide-fill me-2" style="color:var(--primary);"></i>Daftar Menu</h2>
                <a href="tambah.php" class="btn-tambah">
                    <i class="bi bi-plus-lg"></i> Tambah Menu Baru
                </a>
            </div>

            <div class="table-wrapper">
                <?php if (count($data_menu) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_menu as $index => $menu): ?>
                            <tr>
                                <td style="color:var(--text-muted);"><?= $index + 1 ?></td>
                                <td>
                                    <?php
                                    $gambar_path = 'uploads/' . $menu['gambar'];
                                    if (file_exists($gambar_path) && !empty($menu['gambar'])):
                                    ?>
                                        <img src="<?= htmlspecialchars($gambar_path) ?>" 
                                             alt="<?= htmlspecialchars($menu['nama_menu']) ?>" 
                                             class="menu-img">
                                    <?php else: ?>
                                        <div class="menu-img d-flex align-items-center justify-content-center" 
                                             style="background:var(--primary-light); color:var(--primary); font-size:1.2rem;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($menu['nama_menu']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-kategori badge-<?= htmlspecialchars($menu['kategori']) ?>">
                                        <?= htmlspecialchars($menu['kategori']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="harga-text">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit.php?id=<?= $menu['id'] ?>" class="btn-aksi btn-edit">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $menu['id'] ?>" 
                                           class="btn-aksi btn-hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus menu \'<?= htmlspecialchars(addslashes($menu['nama_menu'])) ?>\'?');">
                                            <i class="bi bi-trash3-fill"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>Belum Ada Menu</h5>
                    <p>Klik tombol "Tambah Menu Baru" untuk menambahkan menu pertama Anda.</p>
                    <a href="tambah.php" class="btn-tambah mt-3">
                        <i class="bi bi-plus-lg"></i> Tambah Menu Baru
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Tutup koneksi
if (isset($koneksi)) {
    $koneksi->close();
}
?>
