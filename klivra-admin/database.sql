-- =============================================
--  KLIVRA ADMIN SYSTEM — DATABASE SCHEMA
--  Nataniel Pendong Portfolio CMS
-- =============================================

CREATE DATABASE IF NOT EXISTS klivra_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE klivra_cms;

-- ── ADMIN USER ──
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password: pendongjansen (bcrypt hashed)
INSERT INTO admin_users (username, password) VALUES
('superadmin-klivra', '$2y$12$NLqVpqM8z6RfJvUhPxGHOe8tKdO5v3SjVq9HiRwK4pNhOgA5EFWvS');

-- ── HERO SECTION ──
CREATE TABLE IF NOT EXISTS hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge_text VARCHAR(100) DEFAULT 'Tersedia untuk proyek baru',
  title_line1 VARCHAR(200) DEFAULT 'Teknisi',
  title_highlight VARCHAR(200) DEFAULT 'Jaringan CCTV',
  title_line2 VARCHAR(200) DEFAULT '& Web Development Profesional',
  subtitle TEXT,
  whatsapp_number VARCHAR(30) DEFAULT '6289504211494',
  whatsapp_message TEXT,
  stat_projects VARCHAR(20) DEFAULT '10+',
  stat_years VARCHAR(20) DEFAULT '2+',
  stat_satisfaction VARCHAR(20) DEFAULT '100%',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO hero (subtitle, whatsapp_message) VALUES
('Mahasiswa aktif dengan pengalaman nyata di lapangan. Spesialis instalasi CCTV, konfigurasi jaringan, dan keamanan sistem — hasil kerja rapi, tepat waktu.',
'Halo Nataniel, saya ingin konsultasi gratis mengenai layanan CCTV/Jaringan');

-- ── ABOUT SECTION ──
CREATE TABLE IF NOT EXISTS about (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) DEFAULT 'Nataniel Pendong',
  section_label VARCHAR(100) DEFAULT 'Tentang Saya',
  headline1 VARCHAR(200) DEFAULT 'Mahasiswa.',
  headline2 VARCHAR(200) DEFAULT 'Praktisi. Problem Solver.',
  bio_paragraph1 TEXT,
  bio_paragraph2 TEXT,
  bio_paragraph3 TEXT,
  profile_image VARCHAR(255) DEFAULT 'pp.jpeg',
  badge_text VARCHAR(100) DEFAULT 'Tersedia untuk proyek',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO about (bio_paragraph1, bio_paragraph2, bio_paragraph3) VALUES
('Halo! Nama saya Nataniel Pendong. Saya adalah mahasiswa teknik informatika aktif yang juga berprofesi sebagai teknisi jaringan CCTV dan Web Developer. Saya menggabungkan ilmu yang dipelajari di kampus dengan pengalaman langsung di lapangan untuk memberikan solusi teknis yang andal dan efisien.',
'Saya percaya bahwa sistem keamanan dan jaringan CCTV yang baik adalah fondasi penting bagi keamanan bisnis maupun hunian modern. Setiap pekerjaan saya kerjakan dengan serius, rapi, dan berorientasi pada kepuasan pelanggan.',
'Selain itu, saya juga mengembangkan kemampuan dalam pembuatan aplikasi web modern yang terintegrasi, termasuk pengelolaan data, sistem monitoring, dan dashboard berbasis web untuk kebutuhan bisnis maupun personal.');

-- ── SKILLS / TAGS ──
CREATE TABLE IF NOT EXISTS skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT INTO skills (name, sort_order) VALUES
('CCTV Analog & IP', 1), ('Crimping & Cabling', 2), ('Fiber Optik', 3),
('Linux Dasar', 4), ('Web Developer', 5);

-- ── SERVICES ──
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon_svg TEXT,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO services (icon_svg, title, description, sort_order) VALUES
('<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>', 'Instalasi CCTV', 'Pemasangan kamera CCTV indoor & outdoor, termasuk DVR/NVR, dan konfigurasi akses remote.', 1),
('<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>', 'Jaringan LAN / WiFi', 'Pemasangan kabel UTP, konfigurasi switch, router, dan access point untuk kantor, ruko, maupun rumah.', 2),
('<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>', 'Maintenance & Troubleshoot', 'Perawatan berkala sistem CCTV dan jaringan yang sudah terpasang agar tetap berjalan optimal.', 3),
('<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>', 'Pengembangan Website', 'Pembuatan website profesional, landing page, dan sistem manajemen konten sesuai kebutuhan.', 4),
('<path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/>', 'Point-to-Point Wireless', 'Instalasi jaringan wireless jarak jauh menggunakan perangkat TP-Link CPE, dan lainnya.', 5),
('<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>', 'Keamanan Jaringan Dasar', 'Konfigurasi firewall dasar, VLAN, dan segmentasi jaringan untuk meningkatkan keamanan sistem Anda.', 6);

-- ── EXPERIENCE ──
CREATE TABLE IF NOT EXISTS experience (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company VARCHAR(200) NOT NULL,
  role VARCHAR(200) NOT NULL,
  period VARCHAR(100),
  is_current TINYINT(1) DEFAULT 0,
  tasks TEXT COMMENT 'JSON array of task strings',
  tags TEXT COMMENT 'JSON array of tag strings',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO experience (company, role, period, is_current, tasks, tags, sort_order) VALUES
('CV. Winwin SolutionWay', 'Junior Network & CCTV Technician', '2024 – Sekarang', 1,
 '["Instalasi dan konfigurasi sistem CCTV IP & Analog","Pemasangan jaringan LAN/WiFi untuk gedung & kantor","Troubleshooting perangkat jaringan dan CCTV","Perbaikan kamera PTZ HikVision & Huawei"]',
 '["CCTV","LAN/WAN","MikroTik","HikVision","TP-Link"]', 1),
('Freelance / Proyek Mandiri', 'Web Developer & IT Support', '2023 – Sekarang', 1,
 '["Pembuatan website portfolio dan landing page","Pengembangan sistem manajemen berbasis web","Konfigurasi remote akses CCTV via aplikasi","Konsultasi teknis jaringan untuk UMKM"]',
 '["PHP","MySQL","HTML/CSS","JavaScript","iCSee"]', 2);

-- ── PORTFOLIO / GALLERY ──
CREATE TABLE IF NOT EXISTS portfolio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(255) NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  category ENUM('cctv','networking','programming') NOT NULL,
  client VARCHAR(200),
  year VARCHAR(10),
  overlay_title VARCHAR(200),
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO portfolio (image, title, description, category, client, year, overlay_title, sort_order) VALUES
('pekerjaan2.jpeg', 'Perbaikan CCTV Hik-Vision PTZ', 'Pengecekan dan perbaikan CCTV Hik-Vision PTZ', 'cctv', 'PT. Pertamina Geothermal Lahendong', '2025', 'Perbaikan CCTV Hik-Vision PTZ', 1),
('pekerjaan1.jpeg', 'Konfigurasi CCTV ezviz H8C PTZ', 'Konfigurasi 16 Unit CCTV ezviz H8C untuk akses remote menggunakan aplikasi ezviz.', 'networking', 'Peternakan Ayam Tatelu', '2026', 'Konfigurasi CCTV Ezviz H8C PTZ Cam', 2),
('pekerjaan5.jpeg', 'Perbaikan CCTV Bullet HIK-Vision', 'Pemasangan 6 kamera outdoor IP66 untuk hunian pribadi dengan night vision.', 'cctv', 'PT. Pertamina Geothermal Lahendong', '2025', 'Perbaikan CCTV Hik-Vision Bullet', 3),
('pekerjaan7.jpeg', 'Perbaikan Instalasi Jaringan switch POE', 'Pengecekan dan perbaikan instalasi switch POE kamera outdoor bersama CV.Winwin SolutionWay sebagai junior networking technician.', 'networking', 'PT. Pertamina Geothermal Lahendong', '2025', 'Instalasi jaringan Switch POE', 4),
('pekerjaan23.jpeg', 'Website Manajemen Kerja', 'Pembuatan Website khusus teknisi untuk mempermudah dalam mengetahui jadwal kerja harian, bulanan dan tahunan untuk PT. IKA TEKNO JAYA', 'programming', 'PT. IKA TEKNO JAYA', '2026', 'Website Manajemen Kerja', 5),
('pekerjaan12.jpeg', 'Perbaikan Kamera Huawei Bullet', 'Perbaikan 15+ kamera IP Bullet & PTZ untuk area Polres-minut.', 'cctv', 'Polres-minut', '2025', 'Pemasangan Kamera CCTV Huawei', 6),
('pekerjaan9.jpeg', 'Instalasi Fiber Optik', 'Instalasi kabel FO terstruktur untuk 20+ CCTV di polres minut.', 'networking', 'Polres-minut', '2025', 'Structured Cabling', 7),
('pekerjaan25.jpeg', 'Website Landing Page', 'Pengembangan Website Landing Page untuk promosi Klivra.', 'programming', 'Proyek Mandiri', '2026', 'Landing page', 8),
('pekerjaan11.jpeg', 'Cabling dan configurasi NVR 24port', 'Configurasi dan Cabling NVR 24port untuk jaringan CCTV Huawei Bullet/Dom/PTZ Polres-minut.', 'networking', 'Polres-Minut', '2025', 'Cabling & Configurasi NVR 24port', 9);

-- ── CONTACT / SETTINGS ──
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Nataniel Pendong – Teknisi Jaringan CCTV & Web Development'),
('nav_logo_text', 'Secure visions'),
('whatsapp_number', '6289504211494'),
('whatsapp_default_message', 'Halo Nataniel, saya ingin konsultasi gratis mengenai layanan CCTV/Jaringan'),
('contact_email', 'nataniel@email.com'),
('contact_location', 'Manado, Sulawesi Utara'),
('footer_text', '© 2025 Nataniel Pendong. Dibuat dengan ❤️ di Manado.'),
('meta_description', 'Teknisi jaringan CCTV dan Web Developer profesional di Manado. Instalasi CCTV, jaringan LAN/WiFi, dan pengembangan website.'),
('google_maps_embed', '');
