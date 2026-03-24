<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'Dashboard';

$db = getDB();

// Fetch counts
$totalPortfolio  = $db->query("SELECT COUNT(*) FROM portfolio WHERE is_active=1")->fetchColumn();
$totalServices   = $db->query("SELECT COUNT(*) FROM services WHERE is_active=1")->fetchColumn();
$totalExperience = $db->query("SELECT COUNT(*) FROM experience WHERE is_active=1")->fetchColumn();
$totalSkills     = $db->query("SELECT COUNT(*) FROM skills WHERE is_active=1")->fetchColumn();

// Category breakdown
$catCounts = $db->query("SELECT category, COUNT(*) as c FROM portfolio WHERE is_active=1 GROUP BY category")->fetchAll();
$catMap = [];
foreach ($catCounts as $r) $catMap[$r['category']] = $r['c'];

// Recent portfolio
$recentPortfolio = $db->query("SELECT * FROM portfolio ORDER BY created_at DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon stat-icon-blue">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
    </div>
    <div>
      <div class="stat-num"><?= $totalPortfolio ?></div>
      <div class="stat-label">Item Portofolio</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-icon-yellow">
      <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
    </div>
    <div>
      <div class="stat-num"><?= $totalServices ?></div>
      <div class="stat-label">Layanan Aktif</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-icon-green">
      <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
    </div>
    <div>
      <div class="stat-num"><?= $totalExperience ?></div>
      <div class="stat-label">Pengalaman Kerja</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-icon-purple">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <div>
      <div class="stat-num"><?= $totalSkills ?></div>
      <div class="stat-label">Skill Tags</div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;">

  <!-- Recent Portfolio -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Portofolio Terbaru
      </div>
      <a href="portfolio.php" class="btn btn-sm btn-secondary">Kelola</a>
    </div>
    <?php if ($recentPortfolio): ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Foto</th>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Tahun</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentPortfolio as $item): ?>
            <tr>
              <td>
                <img src="../<?= clean($item['image']) ?>" alt="" class="img-thumb"
                     onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2252%22 height=%2240%22><rect width=%2252%22 height=%2240%22 fill=%22%23141820%22/></svg>'">
              </td>
              <td><?= clean($item['title']) ?></td>
              <td><span class="badge badge-<?= $item['category'] ?>"><?= ucfirst($item['category']) ?></span></td>
              <td class="text-muted"><?= clean($item['year']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-muted" style="text-align:center;padding:2rem 0;">Belum ada portofolio.</p>
    <?php endif; ?>
  </div>

  <!-- Category Summary + Quick Links -->
  <div style="display:flex;flex-direction:column;gap:1.2rem;">
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24"><pie fill="none"/><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
          Portofolio per Kategori
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:.8rem;">
        <?php
        $cats = ['cctv' => ['label'=>'CCTV','color'=>'#fbbf24'], 'networking' => ['label'=>'Networking','color'=>'#00d4ff'], 'programming' => ['label'=>'Programming','color'=>'#a78bfa']];
        foreach ($cats as $key => $info):
          $count = $catMap[$key] ?? 0;
          $pct = $totalPortfolio > 0 ? round(($count / $totalPortfolio) * 100) : 0;
        ?>
        <div>
          <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:.3rem;">
            <span><?= $info['label'] ?></span>
            <span class="text-muted"><?= $count ?> item (<?= $pct ?>%)</span>
          </div>
          <div style="height:6px;background:var(--border);border-radius:999px;overflow:hidden;">
            <div style="height:100%;width:<?= $pct ?>%;background:<?= $info['color'] ?>;border-radius:999px;transition:width .6s ease;"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
          Aksi Cepat
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:.6rem;">
        <a href="portfolio.php?action=add" class="btn btn-primary">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Portofolio Baru
        </a>
        <a href="services.php?action=add" class="btn btn-secondary">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Layanan Baru
        </a>
        <a href="hero.php" class="btn btn-secondary">
          <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Hero Section
        </a>
        <a href="about.php" class="btn btn-secondary">
          <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Tentang Saya
        </a>
        <a href="settings.php" class="btn btn-secondary">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          Pengaturan Umum
        </a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
