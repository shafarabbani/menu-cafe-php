<?php
// ============================================================
// FILE: hapus.php
// Deskripsi: Proses hapus menu via API
// ============================================================

// Cek autentikasi
require_once 'auth_check.php';
require_once 'config/api.php';

// Ambil ID menu
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Validasi ID
if ($id <= 0) {
    $_SESSION['pesan_sukses'] = 'ID menu tidak valid!';
    header('Location: dashboard.php');
    exit();
}

// Panggil API delete
$response = api_delete('/api/menu/delete.php', ['id' => $id]);

if ($response['code'] === 200 && ($response['body']['status'] ?? '') === 'success') {
    $_SESSION['pesan_sukses'] = $response['body']['message'];
} elseif ($response['code'] === 404) {
    $_SESSION['pesan_sukses'] = 'Data menu tidak ditemukan atau sudah dihapus!';
} else {
    $_SESSION['pesan_sukses'] = $response['body']['message'] ?? 'Gagal menghapus menu.';
}

// Redirect ke halaman utama
header('Location: dashboard.php');
exit();
?>
