<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Edit Hero Section';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $fields = ['badge_text','title_line1','title_highlight','title_line2','subtitle',
               'whatsapp_number','whatsapp_message','stat_projects','stat_years','stat_satisfaction'];
    $values = [];
    foreach ($fields as $f) $values[$f] = trim($_POST[$f] ?? '');

    $stmt = $db->prepare("UPDATE hero SET badge_text=?,title_line1=?,title_highlight=?,title_line2=?,subtitle=?,whatsapp_number=?,whatsapp_message=?,stat_projects=?,stat_years=?,stat_satisfaction=? WHERE id=1");
    $stmt->execute(array_values($values));
    setFlash('success', 'Hero section berhasil diperbarui.');
    header('Location: hero.php');
    exit;
}

$hero = $db->query("SELECT * FROM hero WHERE id=1")->fetch();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card" style="max-width:760px;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Edit Hero Section
    </div>
    <a href="../index.php" target="_blank" class="btn btn-sm btn-secondary">Preview</a>
  </div>

  <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.5rem;font-size:.82rem;color:var(--muted);">
    <strong style="color:var(--accent);">ℹ️ Info:</strong> Bagian ini mengontrol tampilan utama (hero) di halaman pertama website Anda.
  </div>

  <form method="POST" action="hero.php">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <div class="form-group">
      <label>Teks Badge (Status)</label>
      <input type="text" name="badge_text" value="<?= clean($hero['badge_text'] ?? '') ?>" placeholder="Tersedia untuk proyek baru">
      <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Teks kecil dengan titik hijau di atas judul.</p>
    </div>

    <div style="background:var(--surface);border-radius:10px;border:1px solid var(--border);padding:1rem 1.2rem;margin-bottom:1.2rem;">
      <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Judul Utama (H1)</p>
      <div class="form-group">
        <label>Baris 1</label>
        <input type="text" name="title_line1" value="<?= clean($hero['title_line1'] ?? '') ?>" placeholder="Teknisi">
      </div>
      <div class="form-group">
        <label>Teks Highlight (warna aksen)</label>
        <input type="text" name="title_highlight" value="<?= clean($hero['title_highlight'] ?? '') ?>" placeholder="Jaringan CCTV">
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label>Baris 2</label>
        <input type="text" name="title_line2" value="<?= clean($hero['title_line2'] ?? '') ?>" placeholder="& Web Development Profesional">
      </div>
    </div>

    <div class="form-group">
      <label>Teks Deskripsi (Subtitle)</label>
      <textarea name="subtitle" placeholder="Deskripsi singkat tentang Anda..."><?= clean($hero['subtitle'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Nomor WhatsApp</label>
        <input type="text" name="whatsapp_number" value="<?= clean($hero['whatsapp_number'] ?? '') ?>" placeholder="628xxxxxxxxxx">
        <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Format: 628xxx (tanpa +)</p>
      </div>
      <div class="form-group">
        <label>Pesan Default WA</label>
        <input type="text" name="whatsapp_message" value="<?= clean($hero['whatsapp_message'] ?? '') ?>" placeholder="Halo Nataniel...">
      </div>
    </div>

    <div style="background:var(--surface);border-radius:10px;border:1px solid var(--border);padding:1rem 1.2rem;margin-bottom:1.2rem;">
      <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Statistik</p>
      <div class="form-row-3">
        <div class="form-group" style="margin-bottom:0;">
          <label>Proyek Selesai</label>
          <input type="text" name="stat_projects" value="<?= clean($hero['stat_projects'] ?? '10+') ?>" placeholder="10+">
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label>Tahun Pengalaman</label>
          <input type="text" name="stat_years" value="<?= clean($hero['stat_years'] ?? '2+') ?>" placeholder="2+">
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label>Kepuasan Klien</label>
          <input type="text" name="stat_satisfaction" value="<?= clean($hero['stat_satisfaction'] ?? '100%') ?>" placeholder="100%">
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      Simpan Hero Section
    </button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
