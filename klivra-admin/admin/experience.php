<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Kelola Pengalaman';
$db = getDB();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $editId && $_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $db->prepare("DELETE FROM experience WHERE id=?")->execute([$editId]);
    setFlash('success', 'Pengalaman berhasil dihapus.');
    header('Location: experience.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','edit']) && verifyCsrf()) {
    $company   = trim($_POST['company'] ?? '');
    $role      = trim($_POST['role'] ?? '');
    $period    = trim($_POST['period'] ?? '');
    $is_current= isset($_POST['is_current']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order= (int)($_POST['sort_order'] ?? 0);

    // Tasks (line separated)
    $tasksRaw = array_filter(array_map('trim', explode("\n", $_POST['tasks'] ?? '')));
    $tasks = json_encode(array_values($tasksRaw));

    // Tags (comma separated)
    $tagsRaw = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
    $tags = json_encode(array_values($tagsRaw));

    if (!$company || !$role) {
        setFlash('error', 'Nama perusahaan dan posisi wajib diisi.');
    } else {
        if ($action === 'add') {
            $db->prepare("INSERT INTO experience (company,role,period,is_current,tasks,tags,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?)")
               ->execute([$company,$role,$period,$is_current,$tasks,$tags,$sort_order,$is_active]);
            setFlash('success', 'Pengalaman kerja baru berhasil ditambahkan.');
        } else {
            $db->prepare("UPDATE experience SET company=?,role=?,period=?,is_current=?,tasks=?,tags=?,sort_order=?,is_active=? WHERE id=?")
               ->execute([$company,$role,$period,$is_current,$tasks,$tags,$sort_order,$is_active,$editId]);
            setFlash('success', 'Pengalaman kerja berhasil diperbarui.');
        }
        header('Location: experience.php'); exit;
    }
}

$editItem = null;
if ($editId) {
    $stmt = $db->prepare("SELECT * FROM experience WHERE id=?");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM experience ORDER BY sort_order, id")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      Pengalaman Kerja (<?= count($items) ?>)
    </div>
    <a href="?action=add" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Pengalaman
    </a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Urutan</th><th>Perusahaan</th><th>Posisi</th><th>Periode</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <td class="text-muted"><?= $item['sort_order'] ?></td>
          <td>
            <strong><?= clean($item['company']) ?></strong>
            <?php if ($item['is_current']): ?>
              <span class="badge badge-active" style="margin-left:.4rem;">Aktif</span>
            <?php endif; ?>
          </td>
          <td style="color:var(--accent);font-size:.875rem;"><?= clean($item['role']) ?></td>
          <td class="text-muted"><?= clean($item['period']) ?></td>
          <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active']?'Tampil':'Disembunyikan' ?></span></td>
          <td>
            <div style="display:flex;gap:.4rem;">
              <a href="?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="?action=delete&id=<?= $item['id'] ?>" onsubmit="return confirm('Hapus pengalaman ini?')" style="display:inline;">
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
</div>

<?php else: ?>
<?php
$tasksText = '';
$tagsText  = '';
if ($editItem) {
    $tasksArr = json_decode($editItem['tasks'] ?? '[]', true) ?: [];
    $tagsArr  = json_decode($editItem['tags'] ?? '[]', true) ?: [];
    $tasksText = implode("\n", $tasksArr);
    $tagsText  = implode(', ', $tagsArr);
}
?>
<div class="card" style="max-width:760px;">
  <div class="card-header">
    <div class="card-title">
      <svg viewBox="0 0 24 24"><?= $action==='add' ? '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>' : '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>' ?></svg>
      <?= $action==='add'?'Tambah Pengalaman Kerja':'Edit Pengalaman Kerja' ?>
    </div>
    <a href="experience.php" class="btn btn-sm btn-secondary">← Kembali</a>
  </div>
  <form method="POST" action="?action=<?= $action ?><?= $editId?'&id='.$editId:'' ?>">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <div class="form-row">
      <div class="form-group">
        <label>Nama Perusahaan *</label>
        <input type="text" name="company" value="<?= clean($editItem['company']??'') ?>" placeholder="PT. Contoh" required>
      </div>
      <div class="form-group">
        <label>Posisi / Jabatan *</label>
        <input type="text" name="role" value="<?= clean($editItem['role']??'') ?>" placeholder="Network Technician" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Periode</label>
        <input type="text" name="period" value="<?= clean($editItem['period']??'') ?>" placeholder="2024 – Sekarang">
      </div>
      <div class="form-group">
        <label>Urutan Tampil</label>
        <input type="number" name="sort_order" value="<?= (int)($editItem['sort_order']??0) ?>" min="0">
      </div>
    </div>
    <div class="form-group">
      <label>Daftar Tugas (satu per baris)</label>
      <textarea name="tasks" rows="5" placeholder="Instalasi CCTV IP & Analog&#10;Pemasangan jaringan LAN/WiFi&#10;Troubleshooting perangkat"><?= clean($tasksText) ?></textarea>
      <p style="font-size:.75rem;color:var(--muted);margin-top:.3rem;">Satu tugas per baris. Akan ditampilkan sebagai poin-poin.</p>
    </div>
    <div class="form-group">
      <label>Tags Teknologi (pisahkan dengan koma)</label>
      <input type="text" name="tags" value="<?= clean($tagsText) ?>" placeholder="CCTV, LAN/WAN, MikroTik, HikVision">
    </div>
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-bottom:1.2rem;">
      <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin:0;">
        <input type="checkbox" name="is_current" value="1" <?= ($editItem['is_current']??0)?'checked':'' ?> style="width:auto;accent-color:var(--accent);">
        Pekerjaan Saat Ini
      </label>
      <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin:0;">
        <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active']??1)?'checked':'' ?> style="width:auto;accent-color:var(--accent);">
        Tampilkan di Website
      </label>
    </div>
    <div style="display:flex;gap:.75rem;">
      <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan
      </button>
      <a href="experience.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
