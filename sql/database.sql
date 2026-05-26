-- Database for UKK Learning Website
CREATE DATABASE IF NOT EXISTS `ukk_learning_db`;
USE `ukk_learning_db`;

-- ============================================
-- TABLE: users (Tabel Pengguna)
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `no_induk` VARCHAR(50),
  `kelas` VARCHAR(50),
  `no_telp` VARCHAR(15),
  `alamat` TEXT,
  `foto_profile` VARCHAR(255) DEFAULT 'default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: mapel (Mata Pelajaran)
-- ============================================
CREATE TABLE IF NOT EXISTS `mapel` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_mapel` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  `icon` VARCHAR(255),
  `warna` VARCHAR(20),
  `guru_pengampu` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: materi (Materi Pembelajaran)
-- ============================================
CREATE TABLE IF NOT EXISTS `materi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `mapel_id` INT NOT NULL,
  `judul_materi` VARCHAR(150) NOT NULL,
  `konten` LONGTEXT,
  `video_url` VARCHAR(255),
  `file_pdf` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`mapel_id`) REFERENCES `mapel`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: user_mapel (Progress Pengguna)
-- ============================================
CREATE TABLE IF NOT EXISTS `user_mapel` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `mapel_id` INT NOT NULL,
  `status_belajar` ENUM('belum','sedang','selesai') DEFAULT 'belum',
  `progress` INT DEFAULT 0,
  `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`mapel_id`) REFERENCES `mapel`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_mapel` (`user_id`, `mapel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: kuis (Kuis/Latihan)
-- ============================================
CREATE TABLE IF NOT EXISTS `kuis` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `materi_id` INT NOT NULL,
  `pertanyaan` TEXT NOT NULL,
  `opsi_a` VARCHAR(255),
  `opsi_b` VARCHAR(255),
  `opsi_c` VARCHAR(255),
  `opsi_d` VARCHAR(255),
  `jawaban_benar` CHAR(1),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`materi_id`) REFERENCES `materi`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: nilai_kuis (Nilai Kuis Pengguna)
-- ============================================
CREATE TABLE IF NOT EXISTS `nilai_kuis` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `kuis_id` INT NOT NULL,
  `jawaban_user` CHAR(1),
  `benar` BOOLEAN,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`kuis_id`) REFERENCES `kuis`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: galeri (Foto Sekolah)
-- ============================================
CREATE TABLE IF NOT EXISTS `galeri` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(150),
  `deskripsi` TEXT,
  `foto` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: kontak (Kontak Sekolah)
-- ============================================
CREATE TABLE IF NOT EXISTS `kontak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pengirim` VARCHAR(100),
  `email_pengirim` VARCHAR(100),
  `no_telp` VARCHAR(15),
  `pesan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DATA: Mata Pelajaran
-- ============================================
INSERT INTO `mapel` (`nama_mapel`, `deskripsi`, `icon`, `warna`, `guru_pengampu`) VALUES
('Bahasa Indonesia', 'Pelajaran tentang bahasa, sastra, dan komunikasi dalam bahasa Indonesia', '📚', '#FF6B6B', 'Ibu Siti Nurhaliza'),
('Matematika', 'Pelajaran tentang bilangan, aljabar, geometri, dan kalkulus', '🔢', '#4ECDC4', 'Pak Bambang Sutrisno'),
('Bahasa Inggris', 'Pelajaran tentang bahasa Inggris, grammar, dan komunikasi', '🗣️', '#45B7D1', 'Ibu Eka Kusuma'),
('IPAS (IPA & IPS)', 'Pelajaran tentang sains dan ilmu sosial', '🔬', '#96CEB4', 'Pak Arif Rahman'),
('PJOK (Penjas & Kesehatan)', 'Pelajaran tentang olahraga dan kesehatan jasmani', '⚽', '#FFEAA7', 'Pak Hendrik Wijaya'),
('PKn (Pendidikan Kewarganegaraan)', 'Pelajaran tentang nilai-nilai pancasila dan kewarganegaraan', '🏛️', '#DDA15E', 'Ibu Dewi Santoso'),
('Seni Rupa', 'Pelajaran tentang seni, desain, dan kreativitas visual', '🎨', '#BC6C25', 'Ibu Ratna Putri'),
('Agama Islam', 'Pelajaran tentang Islam, Al-Quran, dan hadis', '☪️', '#06A77D', 'Pak Imam Hidayat'),
('Informatika', 'Pelajaran tentang teknologi informasi dan komputer', '💻', '#2E86AB', 'Pak Rizki Pratama'),
('DKL RPL (Rekayasa Perangkat Lunak)', 'Pelajaran tentang pengembangan software dan aplikasi', '⚙️', '#A23B72', 'Pak Hendra Kusuma'),
('Coding & Programming', 'Pelajaran tentang pemrograman dan algoritma', '👨‍💻', '#F18F01', 'Pak Budi Hartono'),
('Artificial Intelligence (AI)', 'Pelajaran tentang kecerdasan buatan dan machine learning', '🤖', '#C73E1D', 'Pak Yudi Setiawan');

-- ============================================
-- INSERT DATA: Materi Contoh
-- ============================================
INSERT INTO `materi` (`mapel_id`, `judul_materi`, `konten`, `video_url`, `file_pdf`) VALUES
(1, 'Pengenalan Bahasa Indonesia', 'Bahasa Indonesia adalah bahasa resmi Republik Indonesia yang menjadi bahasa kesatuan dan bahasa negara.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'materi_1.pdf'),
(2, 'Operasi Bilangan Bulat', 'Bilangan bulat adalah himpunan dari seluruh bilangan baik negatif, nol, dan positif yang dapat digunakan dalam berbagai operasi matematika.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'materi_2.pdf'),
(3, 'Basic English Grammar', 'Grammar adalah tata bahasa yang menjadi fondasi dalam belajar bahasa Inggris untuk berkomunikasi dengan baik.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'materi_3.pdf');

-- ============================================
-- INSERT DATA: Sample Kuis
-- ============================================
INSERT INTO `kuis` (`materi_id`, `pertanyaan`, `opsi_a`, `opsi_b`, `opsi_c`, `opsi_d`, `jawaban_benar`) VALUES
(1, 'Apa kepanjangan dari BBI?', 'Badan Bahasa Indonesia', 'Badan Belajar Indonesia', 'Badan Baku Indonesia', 'Badan Budaya Indonesia', 'A'),
(2, 'Berapa hasil dari 15 + (-8)?', '7', '23', '-7', '8', 'A'),
(3, 'What is the correct form?', 'He go to school', 'He goes to school', 'He going to school', 'He gone to school', 'B');