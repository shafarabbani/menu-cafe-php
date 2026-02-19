<?php
// ============================================================
// FILE: config/database.php
// Deskripsi: Konfigurasi koneksi database MySQL
// ============================================================

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cafe_menu_db');

// Membuat koneksi ke database
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi database
if ($koneksi->connect_error) {
    die('
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Kesalahan Koneksi</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-dark d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="alert alert-danger text-center shadow-lg" style="max-width:500px;">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Gagal Koneksi Database</h4>
            <hr>
            <p class="mb-0">Tidak dapat terhubung ke database. Pastikan MySQL sudah berjalan dan konfigurasi sudah benar.</p>
            <p class="text-muted mt-2"><small>Kode Error: ' . $koneksi->connect_errno . '</small></p>
        </div>
    </body>
    </html>
    ');
}

// Set charset ke utf8mb4
$koneksi->set_charset("utf8mb4");
?>
