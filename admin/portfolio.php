<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Kelola Portofolio';
$db = getDB();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// ── DELETE ──
if ($action === 'delete' && $editId && $_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $row = $db->prepare("SELECT image FROM portfolio WHERE id=?")->execute([$editId]) ? $db->query("SELECT image FROM portfolio WHERE id=$editId")->fetch() : null;
    $db->prepare("DELETE FROM portfolio WHERE id=?")->execute([$editId]);
    // Delete image file if in uploads
    if ($row && strpos($row['image'], 'uploads/') !== false) {
        $filePath = __DIR__ . '/../' . $row['image'];
        if (file_exists($filePath)) @unlink($filePath);
    }
    setFlash('success', 'Item portofolio berhasil dihapus.');
    header('Location: portfolio.php');
    exit;
}

// ── TOGGLE ACTIVE ──
if ($action === 'toggle' && $editId && verifyCsrf()) {
    $db->prepare("UPDATE portfolio SET is_active = 1 - is_active WHERE id=?")->execute([$editId]);
    setFlash('success', 'Status item berhasil diperbarui.');
    header('Location: portfolio.php');
    exit;
}

// ── SAVE (add/edit) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','edit']) && verifyCsrf()) {
    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $category     = $_POST['category'] ?? 'cctv';
    $client       = trim($_POST['client'] ?? '');
    $year         = trim($_POST['year'] ?? '');
    $overlay_title= trim($_POST['overlay_title'] ?? '') ?: $title;
    $sort_order   = (int)($_POST['sort_order'] ?? 0);
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    // Validate
    if (!$title || !$category) {
        setFlash('error', 'Judul dan kategori wajib diisi.');
    } else {
        $imageField = null;

        // Handle upload
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            if ($file['size'] > MAX_FILE_SIZE) {
                setFlash('error', 'Ukuran file melebihi 5MB.');
                header('Location: portfolio.php?action=' . $action . ($editId ? '&id=' . $editId : ''));
                exit;
            }
            if (!in_array($file['type'], ALLOWED_TYPES)) {
                setFlash('error', 'Tipe file tidak didukung. Gunakan JPG, PNG, atau WebP.');
                header('Location: portfolio.php?action=' . $action . ($editId ? '&id=' . $editId : ''));
                exit;
            }
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest     = GALLERY_UPLOAD_PATH . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $imageField = 'uploads/gallery/' . $filename;
            } else {
                setFlash('error', 'Gagal mengupload file.');
                header('Location: portfolio.php?action=' . $action . ($editId ? '&id=' . $editId : ''));
                exit;
            }
        }

        if ($action === 'add') {
            if (!$imageField) {
                setFlash('error', 'Gambar wajib diupload untuk portofolio baru.');
                header('Location: portfolio.php?action=add');
                exit;
            }
            $db->prepare("INSERT INTO portfolio (image,title,description,category,client,year,overlay_title,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$imageField, $title, $description, $category, $client, $year, $overlay_title, $sort_order, $is_active]);
            setFlash('success', 'Portofolio baru berhasil ditambahkan.');
        } else {
            if ($imageField) {
                // Delete old image if in uploads
                $old = $db->prepare("SELECT image FROM portfolio WHERE id=?");
                $old->execute([$editId]);
                $oldRow = $old->fetch();
                if ($oldRow && strpos($oldRow['image'], 'uploads/') !== false) {
                    $fp = __DIR__ . '/../' . $oldRow['image'];
                    if (file_exists($fp)) @unlink($fp);
                }
                $db->prepare("UPDATE portfolio SET image=?,title=?,description=?,category=?,client=?,year=?,overlay_title=?,sort_order=?,is_active=? WHERE id=?")
                   ->execute([$imageField, $title, $description, $category, $client, $year, $overlay_title, $sort_order, $is_active, $editId]);
            } else {
                $db->prepare("UPDATE portfolio SET title=?,description=?,category=?,client=?,year=?,overlay_title=?,sort_order=?,is_active=? WHERE id=?")
                   ->execute([$title, $description, $category, $client, $year, $overlay_title, $sort_order, $is_active, $editId]);
            }
            setFlash('success', 'Portofolio berhasil diperbarui.');
        }
        header('Location: portfolio.php');
        exit;
    }
}

// Fetch edit item
$editItem = null;
if ($editId && in_array($action, ['edit','delete'])) {
    $stmt = $db->prepare("SELECT * FROM portfolio WHERE id=?");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
    if (!$editItem) { header('Location: portfolio.php'); exit; }
}

// Fetch all items for list
$items = $db->query("SELECT * FROM portfolio ORDER BY sort_order, id DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($action === 'list'): ?>

<div class="card" style="margin-bottom:1.2rem;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Semua Item Portofolio (<?= count($items) ?>)
    </div>
    <a href="?action=add" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Baru
    </a>
  </div>

  <?php if ($items): ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Foto</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Klien</th>
          <th>Tahun</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $item): ?>
        <tr>
          <td class="text-muted"><?= $item['sort_order'] ?: ($i+1) ?></td>
          <td>
            <img src="../<?= clean($item['image']) ?>" alt="" class="img-thumb"
                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2252%22 height=%2240%22><rect width=%2252%22 height=%2240%22 fill=%22%23141820%22 rx=%224%22/><text x=%2226%22 y=%2224%22 fill=%22%236b7280%22 font-size=%2210%22 text-anchor=%22middle%22>No img</text></svg>'">
          </td>
          <td><strong><?= clean($item['title']) ?></strong></td>
          <td><span class="badge badge-<?= $item['category'] ?>"><?= ucfirst($item['category']) ?></span></td>
          <td class="text-muted" style="font-size:.82rem;"><?= clean($item['client'] ?? '-') ?></td>
          <td class="text-muted"><?= clean($item['year'] ?? '-') ?></td>
          <td>
            <form method="POST" action="?action=toggle&id=<?= $item['id'] ?>" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <button type="submit" class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>" style="cursor:pointer;border:none;background:none;">
                <?= $item['is_active'] ? '✓ Aktif' : '✗ Nonaktif' ?>
              </button>
            </form>
          </td>
          <td>
            <div style="display:flex;gap:.4rem;">
              <a href="?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary" title="Edit">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <button onclick="confirmDelete(<?= $item['id'] ?>, '<?= addslashes(clean($item['title'])) ?>')" class="btn btn-sm btn-danger" title="Hapus">
                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div style="text-align:center;padding:3rem;color:var(--muted);">
      <svg viewBox="0 0 24 24" style="width:40px;height:40px;stroke:var(--border);fill:none;stroke-width:1.5;margin:0 auto 1rem;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Belum ada item portofolio.<br>
      <a href="?action=add" class="btn btn-primary" style="margin-top:1rem;display:inline-flex;">Tambah Sekarang</a>
    </div>
  <?php endif; ?>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.7);align-items:center;justify-content:center;">
  <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:380px;width:90%;text-align:center;">
    <div style="width:52px;height:52px;background:rgba(239,68,68,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg viewBox="0 0 24 24" style="width:24px;height:24px;stroke:#ef4444;fill:none;stroke-width:1.75;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
    </div>
    <h3 style="font-family:'Syne',sans-serif;font-size:1.1rem;margin-bottom:.5rem;">Hapus Item?</h3>
    <p id="deleteItemName" style="color:var(--muted);font-size:.875rem;margin-bottom:1.5rem;"></p>
    <form id="deleteForm" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div style="display:flex;gap:.75rem;justify-content:center;">
        <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
function confirmDelete(id, title) {
  document.getElementById('deleteModal').style.display = 'flex';
  document.getElementById('deleteItemName').textContent = '"' + title + '" akan dihapus permanen.';
  document.getElementById('deleteForm').action = '?action=delete&id=' + id;
}
function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = 'none';
}
</script>

<?php elseif (in_array($action, ['add','edit'])): ?>

<div class="card" style="max-width:760px;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><?= $action === 'add' ? '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>' : '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>' ?></svg>
      <?= $action === 'add' ? 'Tambah Portofolio Baru' : 'Edit Portofolio' ?>
    </div>
    <a href="portfolio.php" class="btn btn-sm btn-secondary">← Kembali</a>
  </div>

  <form method="POST" action="?action=<?= $action ?><?= $editId ? '&id='.$editId : '' ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

    <!-- Image Upload -->
    <div class="form-group">
      <label>Foto Portofolio <?= $action === 'add' ? '(Wajib)' : '(Kosongkan jika tidak ingin mengganti)' ?></label>
      <?php if ($editItem && $editItem['image']): ?>
        <div style="margin-bottom:.8rem;">
          <img src="../<?= clean($editItem['image']) ?>" alt="" style="height:120px;border-radius:10px;border:1px solid var(--border);object-fit:cover;"
               onerror="this.style.display='none'">
          <p style="font-size:.78rem;color:var(--muted);margin-top:.35rem;">Foto saat ini</p>
        </div>
      <?php endif; ?>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
             style="background:var(--surface);border:1px dashed var(--border);border-radius:8px;padding:1rem;cursor:pointer;">
      <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Format: JPG, PNG, WebP. Maks. 5MB.</p>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Judul Proyek *</label>
        <input type="text" name="title" value="<?= clean($editItem['title'] ?? '') ?>" placeholder="Contoh: Instalasi CCTV PTZ" required>
      </div>
      <div class="form-group">
        <label>Judul Overlay (di kartu)</label>
        <input type="text" name="overlay_title" value="<?= clean($editItem['overlay_title'] ?? '') ?>" placeholder="Judul singkat di hover card">
      </div>
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" placeholder="Deskripsi singkat proyek ini..."><?= clean($editItem['description'] ?? '') ?></textarea>
    </div>

    <div class="form-row-3">
      <div class="form-group">
        <label>Kategori *</label>
        <select name="category">
          <?php foreach(['cctv','networking','programming'] as $cat): ?>
            <option value="<?= $cat ?>" <?= ($editItem['category'] ?? '') === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Klien / Lokasi</label>
        <input type="text" name="client" value="<?= clean($editItem['client'] ?? '') ?>" placeholder="Nama klien">
      </div>
      <div class="form-group">
        <label>Tahun</label>
        <input type="text" name="year" value="<?= clean($editItem['year'] ?? date('Y')) ?>" placeholder="2025" maxlength="4">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Urutan Tampil</label>
        <input type="number" name="sort_order" value="<?= (int)($editItem['sort_order'] ?? 0) ?>" min="0" placeholder="0">
      </div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.4rem;">
        <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin:0;">
          <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?> style="width:auto;accent-color:var(--accent);">
          Tampilkan di Website
        </label>
      </div>
    </div>

    <div style="display:flex;gap:.75rem;margin-top:.5rem;">
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <?= $action === 'add' ? 'Tambah Portofolio' : 'Simpan Perubahan' ?>
      </button>
      <a href="portfolio.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
