<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Pengaturan Umum';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $keys = ['site_title','nav_logo_text','whatsapp_number','whatsapp_default_message',
             'contact_email','contact_location','footer_text','meta_description'];
    foreach ($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")
           ->execute([$key,$val,$val]);
    }
    setFlash('success', 'Pengaturan berhasil disimpan.');
    header('Location: settings.php'); exit;
}

// Load all settings
$stmt = $db->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) $settings[$row['setting_key']] = $row['setting_value'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="card" style="max-width:760px;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Pengaturan Umum Website
    </div>
  </div>

  <form method="POST" action="settings.php">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div style="background:var(--surface);border-radius:10px;border:1px solid var(--border);padding:1rem 1.2rem;margin-bottom:1.5rem;">
      <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Identitas Site</p>
      <div class="form-group">
        <label>Judul Website (meta title)</label>
        <input type="text" name="site_title" value="<?= clean($settings['site_title']??'') ?>">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label>Teks Logo (Navbar)</label>
        <input type="text" name="nav_logo_text" value="<?= clean($settings['nav_logo_text']??'') ?>" placeholder="Secure visions">
      </div>
    </div>

    <div style="background:var(--surface);border-radius:10px;border:1px solid var(--border);padding:1rem 1.2rem;margin-bottom:1.5rem;">
      <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Kontak & WhatsApp</p>
      <div class="form-row">
        <div class="form-group">
          <label>Nomor WhatsApp</label>
          <input type="text" name="whatsapp_number" value="<?= clean($settings['whatsapp_number']??'') ?>" placeholder="628xxxxxxxxxx">
          <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Format: 628xxx (tanpa +)</p>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="text" name="contact_email" value="<?= clean($settings['contact_email']??'') ?>" placeholder="email@contoh.com">
        </div>
      </div>
      <div class="form-group">
        <label>Pesan Default WhatsApp</label>
        <input type="text" name="whatsapp_default_message" value="<?= clean($settings['whatsapp_default_message']??'') ?>">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label>Lokasi</label>
        <input type="text" name="contact_location" value="<?= clean($settings['contact_location']??'') ?>" placeholder="Manado, Sulawesi Utara">
      </div>
    </div>

    <div style="background:var(--surface);border-radius:10px;border:1px solid var(--border);padding:1rem 1.2rem;margin-bottom:1.5rem;">
      <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">SEO & Footer</p>
      <div class="form-group">
        <label>Meta Description (SEO)</label>
        <textarea name="meta_description" rows="2"><?= clean($settings['meta_description']??'') ?></textarea>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label>Teks Footer</label>
        <input type="text" name="footer_text" value="<?= clean($settings['footer_text']??'') ?>">
      </div>
    </div>

    <button type="submit" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      Simpan Semua Pengaturan
    </button>
  </form>
</div>

<!-- Password Change Section -->
<div class="card" style="max-width:760px;margin-top:1.2rem;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Ubah Password Admin
    </div>
  </div>
  <?php
  // Handle password change
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password']) && verifyCsrf()) {
      $current = $_POST['current_password'] ?? '';
      $new     = $_POST['new_password'] ?? '';
      $confirm = $_POST['confirm_password'] ?? '';
      $db2 = getDB();
      $user = $db2->prepare("SELECT password FROM admin_users WHERE id=?");
      $user->execute([$_SESSION['admin_id']]);
      $userData = $user->fetch();
      if (!password_verify($current, $userData['password'])) {
          $pwError = 'Password saat ini salah.';
      } elseif (strlen($new) < 8) {
          $pwError = 'Password baru minimal 8 karakter.';
      } elseif ($new !== $confirm) {
          $pwError = 'Konfirmasi password tidak cocok.';
      } else {
          $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
          $db2->prepare("UPDATE admin_users SET password=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
          setFlash('success', 'Password berhasil diubah.');
          header('Location: settings.php'); exit;
      }
  }
  ?>
  <?php if (!empty($pwError)): ?>
    <div class="alert alert-error" style="margin:0 0 1rem;">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      <?= clean($pwError) ?>
    </div>
  <?php endif; ?>
  <form method="POST" action="settings.php">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <input type="hidden" name="change_password" value="1">
    <div class="form-row">
      <div class="form-group">
        <label>Password Saat Ini</label>
        <input type="password" name="current_password" required>
      </div>
      <div class="form-group">
        <label>Password Baru (min. 8 karakter)</label>
        <input type="password" name="new_password" required minlength="8">
      </div>
    </div>
    <div class="form-group" style="max-width:340px;">
      <label>Konfirmasi Password Baru</label>
      <input type="password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-secondary">
      <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Ubah Password
    </button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
