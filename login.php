<?php
// ============================================================
// FILE: login.php
// Deskripsi: Halaman login admin café
// ============================================================

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Proses login saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include koneksi database
    require_once 'config/database.php';

    // Ambil dan bersihkan input
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        // Cari user berdasarkan username dengan prepared statement
        $stmt = $koneksi->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password dengan password_verify
            if (password_verify($password, $user['password'])) {
                // Login berhasil - set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect ke dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Password yang Anda masukkan salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }

        $stmt->close();
        $koneksi->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Menu Café</title>
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
            --dark-bg: #0f1117;
            --card-bg: #1a1d27;
            --card-border: rgba(200, 169, 126, 0.15);
            --text-main: #e8e6e3;
            --text-muted: #8b8d94;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(200, 169, 126, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(200, 169, 126, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(200, 169, 126, 0.03) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes bgShift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(2%, -2%) rotate(1deg); }
            66% { transform: translate(-1%, 1%) rotate(-0.5deg); }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 48px 40px;
            backdrop-filter: blur(20px);
            box-shadow: 
                0 25px 60px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(200, 169, 126, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.03);
            animation: cardAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes cardAppear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 32px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(200, 169, 126, 0.3);
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 8px 24px rgba(200, 169, 126, 0.3); }
            50% { box-shadow: 0 8px 40px rgba(200, 169, 126, 0.5); }
        }

        .login-card h2 {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 4px;
        }

        .login-card .subtitle {
            color: var(--text-muted);
            text-align: center;
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

        .form-control {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(200, 169, 126, 0.06);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(200, 169, 126, 0.1);
            color: var(--text-main);
        }

        .form-control::placeholder {
            color: rgba(139, 141, 148, 0.5);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-right: none;
            color: var(--text-muted);
            border-radius: 12px 0 0 12px;
            padding: 14px 16px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(200, 169, 126, 0.4);
            color: #fff;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.12);
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #f5a5ad;
            border-radius: 12px;
            font-size: 0.9rem;
            animation: shakeError 0.5s ease-in-out;
        }

        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            50% { transform: translateX(8px); }
            75% { transform: translateX(-4px); }
        }

        .footer-text {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Icon -->
            <div class="login-icon">
                <i class="bi bi-cup-hot-fill"></i>
            </div>

            <!-- Judul -->
            <h2>Café Dashboard</h2>
            <p class="subtitle">Masuk untuk mengelola menu café Anda</p>

            <!-- Pesan Error -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" action="login.php" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Masukkan username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan password"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none" style="color: var(--primary); font-size: 0.9rem;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu
                </a>
            </div>

            <p class="footer-text">
                &copy; 2026 Café Menu Management System
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
