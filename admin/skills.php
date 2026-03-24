<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Kelola Skills & Tags';
$db = getDB();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $editId && $_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $db->prepare("DELETE FROM skills WHERE id=?")->execute([$editId]);
    setFlash('success', 'Skill berhasil dihapus.');
    header('Location: skills.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','edit']) && verifyCsrf()) {
    $name = trim($_POST['name'] ?? '');
    $sort = (int)($_POST['sort_order'] ?? 0);
    if (!$name) { setFlash('error', 'Nama skill wajib diisi.'); }
    else {
        if ($action === 'add') {
            $db->prepare("INSERT INTO skills (name,sort_order,is_active) VALUES (?,?,1)")->execute([$name,$sort]);
            setFlash('success', 'Skill baru ditambahkan.');
        } else {
            $db->prepare("UPDATE skills SET name=?,sort_order=? WHERE id=?")->execute([$name,$sort,$editId]);
            setFlash('success', 'Skill diperbarui.');
        }
        header('Location: skills.php'); exit;
    }
}

$editItem = null;
if ($editId) { $stmt=$db->prepare("SELECT * FROM skills WHERE id=?"); $stmt->execute([$editId]); $editItem=$stmt->fetch(); }
$items = $db->query("SELECT * FROM skills ORDER BY sort_order, id")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;align-items:start;">
  <!-- List -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Skills / Tags (<?= count($items) ?>)
      </div>
    </div>
    <?php if ($items): ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Nama</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php foreach($items as $i=>$item): ?>
          <tr>
            <td class="text-muted"><?= $item['sort_order']?:($i+1) ?></td>
            <td><span class="badge badge-networking"><?= clean($item['name']) ?></span></td>
            <td>
              <div style="display:flex;gap:.4rem;">
                <a href="?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="?action=delete&id=<?= $item['id'] ?>" onsubmit="return confirm('Hapus skill ini?')" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <button class="btn btn-sm btn-danger"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p style="text-align:center;padding:2rem;color:var(--muted);">Belum ada skill.</p>
    <?php endif; ?>
  </div>

  <!-- Add/Edit Form -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <?= $editId ? 'Edit Skill' : 'Tambah Skill Baru' ?>
      </div>
      <?php if ($editId): ?><a href="skills.php" class="btn btn-sm btn-secondary">+ Baru</a><?php endif; ?>
    </div>
    <form method="POST" action="?action=<?= $editId?'edit':'add' ?><?= $editId?'&id='.$editId:'' ?>">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="form-group">
        <label>Nama Skill *</label>
        <input type="text" name="name" value="<?= clean($editItem['name']??'') ?>" placeholder="Contoh: CCTV Analog & IP" required>
      </div>
      <div class="form-group">
        <label>Urutan Tampil</label>
        <input type="number" name="sort_order" value="<?= (int)($editItem['sort_order']??0) ?>" min="0">
      </div>
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
        <?= $editId ? 'Simpan' : 'Tambah Skill' ?>
      </button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
