<?php
// ============================================================
// FILE: auth_check.php
// Deskripsi: Proteksi session - include di semua halaman admin
// ============================================================

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Hapus semua data session
    session_unset();
    session_destroy();
    
    // Redirect ke halaman login
    header('Location: login.php');
    exit();
}
?>
