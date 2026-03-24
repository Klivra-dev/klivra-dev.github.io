<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Edit Tentang Saya';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $name       = trim($_POST['name'] ?? '');
    $headline1  = trim($_POST['headline1'] ?? '');
    $headline2  = trim($_POST['headline2'] ?? '');
    $bio1       = trim($_POST['bio_paragraph1'] ?? '');
    $bio2       = trim($_POST['bio_paragraph2'] ?? '');
    $bio3       = trim($_POST['bio_paragraph3'] ?? '');
    $badge_text = trim($_POST['badge_text'] ?? '');

    $profileImage = null;

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $file = $_FILES['profile_image'];
        if ($file['size'] <= MAX_FILE_SIZE && in_array($file['type'], ALLOWED_TYPES)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . time() . '.' . $ext;
            $dest = PROFILE_UPLOAD_PATH . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $profileImage = 'uploads/profile/' . $filename;
            }
        } else {
            setFlash('error', 'Gagal upload foto. Pastikan format JPG/PNG dan maks 5MB.');
            header('Location: about.php'); exit;
        }
    }

    if ($profileImage) {
        $db->prepare("UPDATE about SET name=?,headline1=?,headline2=?,bio_paragraph1=?,bio_paragraph2=?,bio_paragraph3=?,badge_text=?,profile_image=? WHERE id=1")
           ->execute([$name,$headline1,$headline2,$bio1,$bio2,$bio3,$badge_text,$profileImage]);
    } else {
        $db->prepare("UPDATE about SET name=?,headline1=?,headline2=?,bio_paragraph1=?,bio_paragraph2=?,bio_paragraph3=?,badge_text=? WHERE id=1")
           ->execute([$name,$headline1,$headline2,$bio1,$bio2,$bio3,$badge_text]);
    }
    setFlash('success', 'Section Tentang Saya berhasil diperbarui.');
    header('Location: about.php'); exit;
}

$about = $db->query("SELECT * FROM about WHERE id=1")->fetch();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card" style="max-width:760px;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Edit Tentang Saya
    </div>
  </div>

  <form method="POST" action="about.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <!-- Profile Image -->
    <div class="form-group">
      <label>Foto Profil</label>
      <?php if ($about['profile_image']): ?>
        <div style="margin-bottom:.8rem;display:flex;align-items:center;gap:1rem;">
          <img src="../<?= clean($about['profile_image']) ?>" alt="Foto Profil"
               style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);"
               onerror="this.style.display='none'">
          <span style="font-size:.82rem;color:var(--muted);">Foto profil saat ini</span>
        </div>
      <?php endif; ?>
      <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp"
             style="background:var(--surface);border:1px dashed var(--border);border-radius:8px;padding:1rem;cursor:pointer;">
      <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Format: JPG, PNG, WebP. Maks. 5MB. Rekomendasi: proporsi 3:4 (portrait).</p>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" value="<?= clean($about['name'] ?? '') ?>" placeholder="Nataniel Pendong">
      </div>
      <div class="form-group">
        <label>Teks Badge (di foto)</label>
        <input type="text" name="badge_text" value="<?= clean($about['badge_text'] ?? '') ?>" placeholder="Tersedia untuk proyek">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Headline Baris 1</label>
        <input type="text" name="headline1" value="<?= clean($about['headline1'] ?? '') ?>" placeholder="Mahasiswa.">
      </div>
      <div class="form-group">
        <label>Headline Baris 2</label>
        <input type="text" name="headline2" value="<?= clean($about['headline2'] ?? '') ?>" placeholder="Praktisi. Problem Solver.">
      </div>
    </div>

    <div class="form-group">
      <label>Paragraf Bio 1</label>
      <textarea name="bio_paragraph1" rows="3"><?= clean($about['bio_paragraph1'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Paragraf Bio 2</label>
      <textarea name="bio_paragraph2" rows="3"><?= clean($about['bio_paragraph2'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Paragraf Bio 3</label>
      <textarea name="bio_paragraph3" rows="3"><?= clean($about['bio_paragraph3'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      Simpan Perubahan
    </button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
