<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Kelola Layanan';
$db = getDB();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $editId && $_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $db->prepare("DELETE FROM services WHERE id=?")->execute([$editId]);
    setFlash('success', 'Layanan berhasil dihapus.');
    header('Location: services.php'); exit;
}

if ($action === 'toggle' && $editId && verifyCsrf()) {
    $db->prepare("UPDATE services SET is_active = 1 - is_active WHERE id=?")->execute([$editId]);
    header('Location: services.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','edit']) && verifyCsrf()) {
    $title      = trim($_POST['title'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $icon_svg   = trim($_POST['icon_svg'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active  = isset($_POST['is_active']) ? 1 : 0;

    if (!$title) { setFlash('error', 'Nama layanan wajib diisi.'); }
    else {
        if ($action === 'add') {
            $db->prepare("INSERT INTO services (icon_svg,title,description,sort_order,is_active) VALUES (?,?,?,?,?)")
               ->execute([$icon_svg,$title,$desc,$sort_order,$is_active]);
            setFlash('success', 'Layanan baru berhasil ditambahkan.');
        } else {
            $db->prepare("UPDATE services SET icon_svg=?,title=?,description=?,sort_order=?,is_active=? WHERE id=?")
               ->execute([$icon_svg,$title,$desc,$sort_order,$is_active,$editId]);
            setFlash('success', 'Layanan berhasil diperbarui.');
        }
        header('Location: services.php'); exit;
    }
}

$editItem = null;
if ($editId && in_array($action, ['edit','delete'])) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id=?");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM services ORDER BY sort_order, id")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      Semua Layanan (<?= count($items) ?>)
    </div>
    <a href="?action=add" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Layanan
    </a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>No</th><th>Judul</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($items as $i => $item): ?>
        <tr>
          <td class="text-muted"><?= $item['sort_order'] ?: ($i+1) ?></td>
          <td><strong><?= clean($item['title']) ?></strong></td>
          <td style="max-width:320px;"><span style="color:var(--muted);font-size:.82rem;"><?= clean(substr($item['description']??'',0,80)) ?>...</span></td>
          <td>
            <form method="POST" action="?action=toggle&id=<?= $item['id'] ?>" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <button type="submit" class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>" style="cursor:pointer;border:none;">
                <?= $item['is_active'] ? '✓ Aktif' : '✗ Nonaktif' ?>
              </button>
            </form>
          </td>
          <td>
            <div style="display:flex;gap:.4rem;">
              <a href="?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="?action=delete&id=<?= $item['id'] ?>" onsubmit="return confirm('Hapus layanan ini?')" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <button type="submit" class="btn btn-sm btn-danger">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<div class="card" style="max-width:680px;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><?= $action==='add' ? '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>' : '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>' ?></svg>
      <?= $action==='add' ? 'Tambah Layanan Baru' : 'Edit Layanan' ?>
    </div>
    <a href="services.php" class="btn btn-sm btn-secondary">← Kembali</a>
  </div>
  <form method="POST" action="?action=<?= $action ?><?= $editId?'&id='.$editId:'' ?>">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <div class="form-group">
      <label>Nama Layanan *</label>
      <input type="text" name="title" value="<?= clean($editItem['title']??'') ?>" placeholder="Instalasi CCTV" required>
    </div>
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" rows="3" placeholder="Deskripsi singkat layanan ini..."><?= clean($editItem['description']??'') ?></textarea>
    </div>
    <div class="form-group">
      <label>SVG Icon (path/shape tags saja)</label>
      <textarea name="icon_svg" rows="3" placeholder='<path d="M14.5 4h-5..."/><circle cx="12" cy="13" r="3"/>'><?= clean($editItem['icon_svg']??'') ?></textarea>
      <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Paste konten dalam tag &lt;svg viewBox="0 0 24 24"&gt; dari Lucide Icons atau heroicons.</p>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Urutan</label>
        <input type="number" name="sort_order" value="<?= (int)($editItem['sort_order']??0) ?>" min="0">
      </div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.4rem;">
        <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin:0;">
          <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active']??1)?'checked':'' ?> style="width:auto;accent-color:var(--accent);">
          Tampilkan di Website
        </label>
      </div>
    </div>
    <div style="display:flex;gap:.75rem;">
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <?= $action==='add'?'Tambah Layanan':'Simpan Perubahan' ?>
      </button>
      <a href="services.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
