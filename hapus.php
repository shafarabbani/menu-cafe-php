<?php
// ============================================================
// FILE: hapus.php
// Deskripsi: Proses hapus menu + hapus file gambar
// ============================================================

// Cek autentikasi
require_once 'auth_check.php';
require_once 'config/database.php';

// Ambil ID menu
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Validasi ID
if ($id <= 0) {
    $_SESSION['pesan_sukses'] = 'ID menu tidak valid!';
    header('Location: dashboard.php');
    exit();
}

// Cari data menu untuk mendapatkan nama file gambar
$stmt = $koneksi->prepare("SELECT id, nama_menu, gambar FROM menu WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Data tidak ditemukan
    $_SESSION['pesan_sukses'] = 'Data menu tidak ditemukan atau sudah dihapus!';
    $stmt->close();
    $koneksi->close();
    header('Location: dashboard.php');
    exit();
}

$menu = $result->fetch_assoc();
$stmt->close();

// Hapus data dari database
$stmt_delete = $koneksi->prepare("DELETE FROM menu WHERE id = ?");
$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {
    // Hapus file gambar dari folder uploads
    $gambar_path = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $menu['gambar'];
    if (!empty($menu['gambar']) && file_exists($gambar_path)) {
        unlink($gambar_path);
    }

    $_SESSION['pesan_sukses'] = 'Menu "' . $menu['nama_menu'] . '" berhasil dihapus!';
} else {
    $_SESSION['pesan_sukses'] = 'Gagal menghapus menu: ' . $stmt_delete->error;
}

$stmt_delete->close();
$koneksi->close();

// Redirect ke halaman utama
header('Location: dashboard.php');
exit();
?>
