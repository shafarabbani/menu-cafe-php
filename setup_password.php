<?php
// ============================================================
// FILE: setup_password.php
// Deskripsi: Script untuk generate hash password admin
// Jalankan sekali, lalu hapus file ini!
// ============================================================

// Password yang ingin di-hash
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<h2>Setup Password Admin</h2>";
echo "<p><strong>Password:</strong> {$password}</p>";
echo "<p><strong>Hash bcrypt:</strong> {$hash}</p>";
echo "<hr>";
echo "<p>Salin hash di atas ke file <code>database.sql</code>, atau jalankan query berikut di phpMyAdmin:</p>";
echo "<pre>UPDATE users SET password = '{$hash}' WHERE username = 'admin';</pre>";
echo "<br>";
echo "<p style='color:red;'><strong>⚠️ PENTING:</strong> Hapus file ini setelah selesai setup!</p>";
?>
