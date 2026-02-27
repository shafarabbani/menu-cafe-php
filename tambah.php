<?php
// ============================================================
// FILE: tambah.php
// Deskripsi: Form tambah menu baru + upload gambar
// ============================================================

// Cek autentikasi
require_once 'auth_check.php';
require_once 'config/database.php';

$errors = [];
$nama_menu = '';
$harga = '';
$kategori = '';

// Konstanta upload
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
$allowed_ext = ['jpg', 'jpeg', 'png'];

// Proses form saat disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan input
    $nama_menu = trim($_POST['nama_menu'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');

    // ===== VALIDASI =====
    
    // Nama menu wajib diisi
    if (empty($nama_menu)) {
        $errors[] = 'Nama menu wajib diisi!';
    } elseif (strlen($nama_menu) > 100) {
        $errors[] = 'Nama menu maksimal 100 karakter!';
    }

    // Harga wajib diisi dan harus angka
    if (empty($harga)) {
        $errors[] = 'Harga wajib diisi!';
    } elseif (!is_numeric($harga) || $harga <= 0) {
        $errors[] = 'Harga harus berupa angka positif!';
    } elseif ($harga > 99999999) {
        $errors[] = 'Harga terlalu besar!';
    }

    // Kategori wajib dipilih
    $kategori_valid = ['Makanan', 'Minuman', 'Cemilan', 'Dessert'];
    if (empty($kategori)) {
        $errors[] = 'Kategori wajib dipilih!';
    } elseif (!in_array($kategori, $kategori_valid)) {
        $errors[] = 'Kategori tidak valid!';
    }

    // ===== VALIDASI GAMBAR =====
    $nama_file_gambar = '';
    
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Gambar menu wajib diunggah!';
    } elseif ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        // Error upload
        switch ($_FILES['gambar']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'Ukuran file terlalu besar! Maksimal 2MB.';
                break;
            default:
                $errors[] = 'Terjadi kesalahan saat mengunggah file. Silakan coba lagi.';
                break;
        }
    } else {
        $file = $_FILES['gambar'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Cek ekstensi file
        if (!in_array($file_ext, $allowed_ext)) {
            $errors[] = 'Format file tidak didukung! Hanya JPG, JPEG, dan PNG yang diperbolehkan.';
        }

        // Cek tipe MIME
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = 'Tipe file tidak valid!';
        }

        // Cek ukuran file
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Ukuran file terlalu besar! Maksimal 2MB.';
        }
    }

    // ===== PROSES SIMPAN =====
    if (empty($errors)) {
        // Buat folder uploads jika belum ada
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        // Buat nama file unik
        $nama_file_gambar = time() . '_' . uniqid() . '.' . $file_ext;
        $target_path = UPLOAD_DIR . $nama_file_gambar;

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Simpan ke database dengan prepared statement
            $stmt = $koneksi->prepare("INSERT INTO menu (nama_menu, harga, kategori, gambar) VALUES (?, ?, ?, ?)");
            $harga_int = (int) $harga;
            $stmt->bind_param("siss", $nama_menu, $harga_int, $kategori, $nama_file_gambar);

            if ($stmt->execute()) {
                $_SESSION['pesan_sukses'] = 'Menu "' . $nama_menu . '" berhasil ditambahkan!';
                $stmt->close();
                $koneksi->close();
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = 'Gagal menyimpan data ke database: ' . $stmt->error;
                // Hapus file yang sudah diupload jika gagal simpan
                if (file_exists($target_path)) {
                    unlink($target_path);
                }
            }
            $stmt->close();
        } else {
            $errors[] = 'Gagal mengunggah file gambar! Pastikan folder uploads memiliki izin tulis.';
        }
    }
}

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - Manajemen Menu Café</title>
    <meta name="description" content="Tambah menu baru ke daftar menu café">
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
            --card-border: rgba(200, 169, 126, 0.1);
            --text-main: #e8e6e3;
            --text-muted: #8b8d94;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-custom {
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 16px 0;
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
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            box-shadow: 0 4px 15px rgba(200, 169, 126, 0.3);
        }

        .brand-text { font-weight: 700; font-size: 1.15rem; color: var(--text-main); }
        .brand-text span { color: var(--primary); }

        .nav-link-custom {
            color: var(--text-muted) !important;
            font-weight: 500; font-size: 0.9rem;
            padding: 10px 18px !important;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex; align-items: center; gap: 8px;
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
        }

        .btn-logout:hover { background: rgba(231, 76, 60, 0.25); }

        /* Main Content */
        .main-content {
            padding: 32px 0;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Card */
        .form-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 40px;
            max-width: 680px;
            margin: 0 auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .form-card h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-card .subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .form-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(200, 169, 126, 0.06);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(200, 169, 126, 0.1);
            color: var(--text-main);
        }

        .form-control::placeholder { color: rgba(139, 141, 148, 0.5); }

        .form-select option {
            background: var(--card-bg);
            color: var(--text-main);
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed rgba(200, 169, 126, 0.25);
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(200, 169, 126, 0.03);
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: rgba(200, 169, 126, 0.06);
        }

        .upload-area.drag-over {
            border-color: var(--primary);
            background: rgba(200, 169, 126, 0.1);
        }

        .upload-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .upload-text {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .upload-text strong {
            color: var(--primary);
        }

        .preview-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            object-fit: cover;
            margin-top: 16px;
            border: 2px solid var(--card-border);
            display: none;
        }

        /* Buttons */
        .btn-simpan {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-simpan:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(200, 169, 126, 0.4);
            color: #fff;
        }

        .btn-batal {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
            font-weight: 500;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-batal:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
        }

        /* Error Alert */
        .alert-error {
            background: rgba(231, 76, 60, 0.12);
            border: 1px solid rgba(231, 76, 60, 0.25);
            color: #f5a5ad;
            border-radius: 14px;
            padding: 16px 24px;
        }

        .alert-error ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-error li {
            margin-bottom: 4px;
        }

        /* Input Group */
        .input-group-text {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-muted);
            border-radius: 12px 0 0 12px;
            padding: 14px 16px;
            font-weight: 600;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }

        @media (max-width: 576px) {
            .form-card { padding: 24px 20px; }
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-custom">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <a href="dashboard.php" class="navbar-brand-custom">
                    <div class="brand-icon"><i class="bi bi-cup-hot-fill"></i></div>
                    <div class="brand-text">Café<span>Menu</span></div>
                </a>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="dashboard.php" class="nav-link-custom">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                    <a href="tambah.php" class="nav-link-custom active">
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
            <div class="form-card">
                <h2><i class="bi bi-plus-circle-fill me-2" style="color:var(--primary);"></i>Tambah Menu Baru</h2>
                <p class="subtitle">Isi formulir di bawah untuk menambahkan menu ke daftar café.</p>

                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                    <div class="alert-error mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                            <div>
                                <strong>Terjadi Kesalahan:</strong>
                                <ul class="mt-2">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="tambah.php" enctype="multipart/form-data" id="formTambah">
                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label for="nama_menu" class="form-label">Nama Menu</label>
                        <input type="text" 
                               class="form-control" 
                               id="nama_menu" 
                               name="nama_menu" 
                               placeholder="Contoh: Nasi Goreng Spesial"
                               value="<?= htmlspecialchars($nama_menu) ?>"
                               maxlength="100"
                               required>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="harga" 
                                   name="harga" 
                                   placeholder="Contoh: 25000"
                                   value="<?= htmlspecialchars($harga) ?>"
                                   min="1"
                                   max="99999999"
                                   required>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="" disabled <?= empty($kategori) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                            <option value="Makanan" <?= $kategori === 'Makanan' ? 'selected' : '' ?>>🍛 Makanan</option>
                            <option value="Minuman" <?= $kategori === 'Minuman' ? 'selected' : '' ?>>🥤 Minuman</option>
                            <option value="Cemilan" <?= $kategori === 'Cemilan' ? 'selected' : '' ?>>🍿 Cemilan</option>
                            <option value="Dessert" <?= $kategori === 'Dessert' ? 'selected' : '' ?>>🍰 Dessert</option>
                        </select>
                    </div>

                    <!-- Upload Gambar -->
                    <div class="mb-4">
                        <label class="form-label">Gambar Menu</label>
                        <div class="upload-area" id="uploadArea" onclick="document.getElementById('gambar').click();">
                            <div class="upload-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                            <p class="upload-text">
                                Klik di sini atau seret file untuk mengunggah<br>
                                <small>Format: <strong>JPG, JPEG, PNG</strong> | Maks: <strong>2MB</strong></small>
                            </p>
                            <img id="previewImg" class="preview-img" alt="Preview">
                        </div>
                        <input type="file" 
                               class="d-none" 
                               id="gambar" 
                               name="gambar" 
                               accept=".jpg,.jpeg,.png">
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-simpan">
                            <i class="bi bi-check-circle-fill me-2"></i>Simpan Menu
                        </button>
                        <a href="dashboard.php" class="btn-batal d-flex align-items-center">
                            <i class="bi bi-arrow-left me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== Preview Gambar =====
        const inputGambar = document.getElementById('gambar');
        const previewImg = document.getElementById('previewImg');
        const uploadArea = document.getElementById('uploadArea');

        inputGambar.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Validasi ukuran di client
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // ===== Drag & Drop =====
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (e.dataTransfer.files.length) {
                inputGambar.files = e.dataTransfer.files;
                inputGambar.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
