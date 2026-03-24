# 🛡️ KLIVRA CMS — Panduan Instalasi

## Tentang Sistem
Sistem CMS (Content Management System) untuk portfolio website Nataniel Pendong.
Dibangun dengan PHP, MySQL, HTML, dan CSS murni — tanpa framework tambahan.

---

## 📁 Struktur File

```
klivra-admin/
├── index.php              ← Landing page (dinamis dari database)
├── login.php              ← Redirect ke admin/login.php
├── setup.php              ← INSTALLER (hapus setelah setup!)
├── database.sql           ← SQL manual (alternatif setup.php)
├── .htaccess              ← Security rules Apache
│
├── admin/
│   ├── login.php          ← Halaman login admin
│   ├── logout.php         ← Proses logout
│   ├── dashboard.php      ← Halaman utama dashboard
│   ├── hero.php           ← Edit Hero Section
│   ├── about.php          ← Edit Tentang Saya + upload foto profil
│   ├── services.php       ← CRUD Layanan
│   ├── experience.php     ← CRUD Pengalaman Kerja
│   ├── portfolio.php      ← CRUD Portofolio + upload foto
│   ├── skills.php         ← CRUD Skills/Tags
│   └── settings.php       ← Pengaturan umum + ganti password
│
├── includes/
│   ├── config.php         ← Konfigurasi database & helpers
│   ├── header.php         ← Layout header dashboard
│   └── footer.php         ← Layout footer dashboard
│
└── uploads/
    ├── gallery/           ← Foto portofolio yang diupload
    ├── profile/           ← Foto profil yang diupload
    └── .htaccess          ← Security uploads folder
```

---

## 🚀 Cara Instalasi

### CARA 1: Menggunakan setup.php (DIREKOMENDASIKAN)

1. **Upload semua file** ke folder website Anda (misal: `public_html/` atau `htdocs/`)

2. **Beri izin write** pada folder uploads:
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/gallery/
   chmod 755 uploads/profile/
   ```

3. **Buka browser** dan akses:
   ```
   http://yourdomain.com/setup.php
   ```

4. **Isi form** konfigurasi database:
   - Host: `localhost`
   - Username MySQL
   - Password MySQL
   - Nama Database: `klivra_cms`

5. **Klik "Jalankan Instalasi"** — sistem akan membuat database dan data default otomatis

6. **⚠️ HAPUS `setup.php`** setelah instalasi selesai!

---

### CARA 2: Manual via phpMyAdmin

1. Buka phpMyAdmin, buat database baru: `klivra_cms`

2. Import file `database.sql`

3. Edit `includes/config.php`, sesuaikan:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'username_mysql_anda');
   define('DB_PASS', 'password_mysql_anda');
   define('DB_NAME', 'klivra_cms');
   ```

4. Karena password di `database.sql` sudah di-hash manual, jalankan query ini
   di phpMyAdmin untuk mengatur password yang benar:
   ```sql
   UPDATE admin_users 
   SET password = '$2y$12$xxx' -- generate dari PHP: password_hash('pendongjansen', PASSWORD_BCRYPT)
   WHERE username = 'superadmin-klivra';
   ```
   > **ATAU** gunakan setup.php yang akan otomatis mengatur hash yang benar.

---

## 🔐 Kredensial Login

| Field    | Value              |
|----------|--------------------|
| Username | `superadmin-klivra` |
| Password | `pendongjansen`    |
| URL      | `/admin/login.php` |

---

## 📸 Foto Portofolio Lama

File index.php yang lama menggunakan foto dengan nama seperti `pekerjaan1.jpeg`, `pekerjaan2.jpeg`, dll.

**Untuk menggunakan foto lama tersebut:**
- Pastikan file foto tersebut tetap ada di root folder (sejajar dengan `index.php`)
- Database sudah dikonfigurasi untuk mereferensikan nama file tersebut
- Atau upload ulang foto melalui dashboard admin → Portofolio → Edit → Upload Foto Baru

---

## ⚙️ Fitur Dashboard Admin

| Menu | Fungsi |
|------|--------|
| **Hero Section** | Edit judul, subtitle, badge, statistik, nomor WA |
| **Tentang Saya** | Edit bio, headline, upload foto profil |
| **Layanan** | Tambah/edit/hapus layanan + ikon SVG |
| **Pengalaman** | Tambah/edit/hapus riwayat kerja |
| **Portofolio** | Tambah/edit/hapus foto + detail proyek |
| **Skills/Tags** | Kelola badge skill yang muncul di section About |
| **Pengaturan** | Edit judul site, WhatsApp, email, footer, SEO |
| **Ganti Password** | Ubah password admin |

---

## 🔧 Persyaratan Server

- PHP 7.4+ (rekomendasi PHP 8.0+)
- MySQL 5.7+ atau MariaDB 10.3+
- Apache dengan `mod_rewrite` aktif
- Ekstensi PHP: `pdo`, `pdo_mysql`, `fileinfo`

---

## 🛡️ Keamanan

- ✅ Password di-hash dengan bcrypt (cost 12)
- ✅ CSRF protection pada semua form
- ✅ Prepared statements (SQL injection protection)
- ✅ Session timeout 1 jam
- ✅ Input sanitization (`htmlspecialchars`)
- ✅ Upload validation (tipe file & ukuran)
- ✅ `.htaccess` memblokir akses langsung ke `includes/`
- ✅ PHP eksekusi diblokir di folder `uploads/`

---

## 📞 Kontak

Dibuat untuk Nataniel Pendong — Teknisi Jaringan CCTV & Web Developer, Manado.
