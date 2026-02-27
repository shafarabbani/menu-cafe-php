-- ============================================================
-- DATABASE: cafe_menu_db
-- Deskripsi: Database untuk Sistem Manajemen Menu Café
-- Siap diimport langsung ke phpMyAdmin
-- ============================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `cafe_menu_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `cafe_menu_db`;

-- ============================================================
-- TABEL: users
-- Menyimpan data pengguna admin
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert admin user
-- Username: admin | Password: admin123 (sudah di-hash dengan bcrypt)
-- Hash dihasilkan menggunakan: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$10$zACdexBoa/Fg6Tg2lOX7Eu8K4OP8qLKe0l.wF4Yl4aA9xmcdJmHOK');

-- ============================================================
-- TABEL: menu
-- Menyimpan data menu café
-- ============================================================
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_menu` VARCHAR(100) NOT NULL,
  `harga` INT(11) NOT NULL,
  `kategori` ENUM('Makanan', 'Minuman', 'Cemilan', 'Dessert') NOT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA DUMMY: 10 menu café
-- ============================================================
INSERT INTO `menu` (`nama_menu`, `harga`, `kategori`, `gambar`) VALUES
('Nasi Goreng Spesial', 35000, 'Makanan', 'nasi-goreng-spesial.jpg'),
('Cappuccino', 28000, 'Minuman', 'cappuccino.jpg'),
('Roti Bakar Cokelat', 18000, 'Cemilan', 'roti-bakar-cokelat.jpg'),
('Es Teh Manis', 10000, 'Minuman', 'es-teh-manis.jpg'),
('Mie Goreng Jawa', 30000, 'Makanan', 'mie-goreng-jawa.jpg'),
('Kentang Goreng', 20000, 'Cemilan', 'kentang-goreng.jpg'),
('Es Kopi Susu', 25000, 'Minuman', 'es-kopi-susu.jpg'),
('Brownies Lava', 22000, 'Dessert', 'brownies-lava.jpg'),
('Ayam Geprek', 32000, 'Makanan', 'ayam-geprek.jpg'),
('Pancake Maple', 27000, 'Dessert', 'pancake-maple.jpg');
