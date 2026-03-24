<?php
// Must call requireLogin() before including this file
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlash();
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Dashboard' ?> — Klivra CMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg: #080a0d;
      --surface: #0f1218;
      --card: #141820;
      --card2: #181d27;
      --border: #1e2533;
      --accent: #00d4ff;
      --accent2: #0097b2;
      --text: #e8eaf0;
      --muted: #6b7280;
      --danger: #ef4444;
      --warning: #f59e0b;
      --green: #22c55e;
      --sidebar-w: 260px;
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      line-height: 1.6;
      display: flex;
      min-height: 100vh;
    }

    /* ──────── SIDEBAR ──────── */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 200;
      transition: transform .3s ease;
    }

    .sidebar-logo {
      padding: 1.5rem 1.4rem 1.2rem;
      border-bottom: 1px solid var(--border);
    }

    .sidebar-logo-text {
      font-family: 'Syne', sans-serif;
      font-size: 1.05rem; font-weight: 800;
      color: var(--accent); letter-spacing: .04em;
      display: flex; align-items: center; gap: .55rem;
    }

    .sidebar-logo-text svg {
      width: 22px; height: 22px;
      stroke: var(--accent); stroke-width: 1.75; fill: none;
    }

    .sidebar-version {
      font-size: .72rem; color: var(--muted);
      margin-top: .2rem;
    }

    .sidebar-nav {
      flex: 1;
      padding: 1rem .75rem;
      overflow-y: auto;
    }

    .nav-section-label {
      font-size: .68rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .1em;
      color: var(--muted);
      padding: .5rem .7rem .3rem;
      margin-top: .5rem;
    }

    .nav-item {
      display: flex; align-items: center; gap: .7rem;
      padding: .6rem .85rem;
      border-radius: 9px;
      color: var(--muted);
      text-decoration: none;
      font-size: .875rem; font-weight: 500;
      transition: background .15s, color .15s;
      margin-bottom: .1rem;
    }

    .nav-item svg {
      width: 17px; height: 17px;
      stroke: currentColor; stroke-width: 1.75; fill: none;
      flex-shrink: 0;
    }

    .nav-item:hover {
      background: rgba(0,212,255,.07);
      color: var(--text);
    }

    .nav-item.active {
      background: rgba(0,212,255,.12);
      color: var(--accent);
      font-weight: 600;
    }

    .nav-item .nav-badge {
      margin-left: auto;
      background: rgba(0,212,255,.15);
      color: var(--accent);
      font-size: .68rem; font-weight: 700;
      padding: .1rem .45rem; border-radius: 999px;
    }

    .sidebar-user {
      padding: 1rem 1.2rem;
      border-top: 1px solid var(--border);
      display: flex; align-items: center; gap: .75rem;
    }

    .user-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: rgba(0,212,255,.12);
      border: 1px solid rgba(0,212,255,.2);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Syne', sans-serif; font-weight: 800;
      font-size: .9rem; color: var(--accent);
      flex-shrink: 0;
    }

    .user-info { flex: 1; min-width: 0; }
    .user-name {
      font-size: .82rem; font-weight: 600;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .user-role { font-size: .72rem; color: var(--muted); }

    .logout-btn {
      background: none; border: none; cursor: pointer;
      color: var(--muted); padding: .3rem;
      display: flex; align-items: center;
      transition: color .2s; border-radius: 6px;
    }

    .logout-btn:hover { color: var(--danger); background: rgba(239,68,68,.08); }

    .logout-btn svg {
      width: 17px; height: 17px;
      stroke: currentColor; stroke-width: 1.75; fill: none;
    }

    /* ──────── MAIN CONTENT ──────── */
    .main-content {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ──────── TOP BAR ──────── */
    .topbar {
      position: sticky; top: 0; z-index: 100;
      background: rgba(8,10,13,.9);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
      padding: .9rem 1.8rem;
      display: flex; align-items: center; justify-content: space-between;
    }

    .topbar-left {
      display: flex; align-items: center; gap: .75rem;
    }

    .page-title {
      font-family: 'Syne', sans-serif;
      font-size: 1.1rem; font-weight: 700;
    }

    .page-breadcrumb {
      font-size: .78rem; color: var(--muted);
      display: flex; align-items: center; gap: .35rem;
      margin-top: .1rem;
    }

    .page-breadcrumb svg {
      width: 13px; height: 13px;
      stroke: currentColor; stroke-width: 2; fill: none;
    }

    .topbar-right {
      display: flex; align-items: center; gap: .75rem;
    }

    .topbar-btn {
      display: flex; align-items: center; gap: .45rem;
      padding: .45rem .9rem;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--card);
      color: var(--muted); font-size: .82rem;
      text-decoration: none;
      transition: all .2s;
    }

    .topbar-btn svg {
      width: 15px; height: 15px;
      stroke: currentColor; stroke-width: 1.75; fill: none;
    }

    .topbar-btn:hover {
      border-color: var(--accent2); color: var(--text);
    }

    /* ──────── PAGE CONTENT ──────── */
    .page-content {
      padding: 1.8rem;
      flex: 1;
    }

    /* ──────── ALERTS ──────── */
    .alert {
      border-radius: 10px; padding: .9rem 1.1rem;
      font-size: .875rem; margin-bottom: 1.5rem;
      display: flex; align-items: center; gap: .65rem;
      animation: fadeIn .3s ease;
    }

    @keyframes fadeIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

    .alert svg {
      width: 17px; height: 17px; flex-shrink: 0;
      stroke: currentColor; stroke-width: 2; fill: none;
    }

    .alert-success {
      background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: #86efac;
    }
    .alert-error {
      background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: #fca5a5;
    }
    .alert-info {
      background: rgba(0,212,255,.1); border: 1px solid rgba(0,212,255,.2); color: #67e8f9;
    }

    /* ──────── CARDS ──────── */
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 1.4rem 1.5rem;
    }

    .card-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 1.2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--border);
    }

    .card-title {
      font-family: 'Syne', sans-serif;
      font-size: .95rem; font-weight: 700;
      display: flex; align-items: center; gap: .5rem;
    }

    .card-title svg {
      width: 17px; height: 17px;
      stroke: var(--accent); stroke-width: 1.75; fill: none;
    }

    /* ──────── FORM ELEMENTS ──────── */
    .form-group { margin-bottom: 1.2rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }

    label {
      display: block; font-size: .78rem; font-weight: 500;
      color: var(--muted); margin-bottom: .4rem;
      text-transform: uppercase; letter-spacing: .05em;
    }

    input[type="text"], input[type="number"], input[type="url"],
    textarea, select {
      width: 100%;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: .9rem;
      padding: .65rem .85rem;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }

    input:focus, textarea:focus, select:focus {
      border-color: var(--accent2);
      box-shadow: 0 0 0 3px rgba(0,212,255,.08);
    }

    textarea { min-height: 100px; resize: vertical; }

    select { cursor: pointer; }

    /* ──────── BUTTONS ──────── */
    .btn {
      display: inline-flex; align-items: center; gap: .45rem;
      padding: .6rem 1.2rem; border-radius: 8px;
      font-family: 'DM Sans', sans-serif; font-size: .875rem; font-weight: 600;
      cursor: pointer; border: 1px solid transparent;
      text-decoration: none;
      transition: all .2s;
    }

    .btn svg {
      width: 16px; height: 16px;
      stroke: currentColor; stroke-width: 2; fill: none;
    }

    .btn-primary {
      background: var(--accent); color: #000;
      border-color: var(--accent);
    }
    .btn-primary:hover { background: #33ddff; }

    .btn-secondary {
      background: var(--card2); color: var(--text);
      border-color: var(--border);
    }
    .btn-secondary:hover { border-color: var(--accent2); }

    .btn-danger {
      background: rgba(239,68,68,.12); color: #fca5a5;
      border-color: rgba(239,68,68,.3);
    }
    .btn-danger:hover { background: rgba(239,68,68,.22); }

    .btn-sm {
      padding: .38rem .8rem; font-size: .8rem;
    }

    /* ──────── TABLE ──────── */
    .table-wrap { overflow-x: auto; border-radius: 12px; }

    table { width: 100%; border-collapse: collapse; }

    th {
      background: var(--surface);
      padding: .7rem .9rem; text-align: left;
      font-size: .75rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .08em;
      color: var(--muted); border-bottom: 1px solid var(--border);
    }

    td {
      padding: .8rem .9rem; font-size: .875rem;
      border-bottom: 1px solid rgba(30,37,51,.6);
      vertical-align: middle;
    }

    tr:last-child td { border-bottom: none; }

    tr:hover td { background: rgba(0,212,255,.02); }

    /* ──────── BADGES ──────── */
    .badge {
      display: inline-flex; align-items: center; gap: .3rem;
      padding: .2rem .6rem; border-radius: 999px;
      font-size: .72rem; font-weight: 700; letter-spacing: .04em;
    }

    .badge-cctv { background: rgba(251,191,36,.12); color: #fbbf24; border: 1px solid rgba(251,191,36,.2); }
    .badge-networking { background: rgba(0,212,255,.12); color: #00d4ff; border: 1px solid rgba(0,212,255,.2); }
    .badge-programming { background: rgba(139,92,246,.12); color: #a78bfa; border: 1px solid rgba(139,92,246,.2); }
    .badge-active { background: rgba(34,197,94,.12); color: #86efac; border: 1px solid rgba(34,197,94,.2); }
    .badge-inactive { background: rgba(107,114,128,.12); color: #9ca3af; border: 1px solid rgba(107,114,128,.2); }

    /* ──────── STAT CARDS ──────── */
    .stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 1.2rem 1.4rem;
      display: flex; align-items: center; gap: 1rem;
      transition: border-color .2s;
    }

    .stat-card:hover { border-color: var(--accent2); }

    .stat-icon {
      width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
    }

    .stat-icon svg {
      width: 22px; height: 22px;
      stroke: currentColor; stroke-width: 1.75; fill: none;
    }

    .stat-icon-blue { background: rgba(0,212,255,.1); color: #00d4ff; }
    .stat-icon-green { background: rgba(34,197,94,.1); color: #22c55e; }
    .stat-icon-purple { background: rgba(139,92,246,.1); color: #a78bfa; }
    .stat-icon-yellow { background: rgba(251,191,36,.1); color: #fbbf24; }

    .stat-num {
      font-family: 'Syne', sans-serif; font-size: 1.6rem; font-weight: 800;
    }

    .stat-label { font-size: .78rem; color: var(--muted); }

    /* ──────── MOBILE TOGGLE ──────── */
    .mobile-menu-btn {
      display: none;
      background: none; border: none; cursor: pointer;
      color: var(--text); padding: .3rem;
    }
    .mobile-menu-btn svg {
      width: 22px; height: 22px;
      stroke: currentColor; stroke-width: 2; fill: none;
    }

    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,.6);
      z-index: 150;
    }

    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .main-content { margin-left: 0; }
      .mobile-menu-btn { display: flex; }
      .sidebar-overlay.show { display: block; }
      .form-row, .form-row-3 { grid-template-columns: 1fr; }
    }

    /* ──────── MISC ──────── */
    .text-muted { color: var(--muted); }
    .text-accent { color: var(--accent); }
    .text-danger { color: #fca5a5; }
    .text-success { color: #86efac; }
    .mt-1 { margin-top: .5rem; }
    .mt-2 { margin-top: 1rem; }
    .gap-1 { gap: .5rem; }
    .flex { display: flex; align-items: center; }
    .justify-between { justify-content: space-between; }
    .img-thumb {
      width: 52px; height: 40px; object-fit: cover;
      border-radius: 6px; border: 1px solid var(--border);
    }

    /* Drag handle */
    .drag-handle { cursor: grab; color: var(--muted); }
    .drag-handle svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }
  </style>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-text">
      <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Klivra CMS
    </div>
    <div class="sidebar-version">Admin Panel v1.0</div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Utama</div>

    <a href="dashboard.php" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="nav-section-label">Konten</div>

    <a href="hero.php" class="nav-item <?= $currentPage === 'hero' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Hero Section
    </a>

    <a href="about.php" class="nav-item <?= $currentPage === 'about' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Tentang Saya
    </a>

    <a href="services.php" class="nav-item <?= $currentPage === 'services' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      Layanan
    </a>

    <a href="experience.php" class="nav-item <?= $currentPage === 'experience' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      Pengalaman
    </a>

    <a href="portfolio.php" class="nav-item <?= $currentPage === 'portfolio' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Portofolio
    </a>

    <a href="skills.php" class="nav-item <?= $currentPage === 'skills' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Skills / Tags
    </a>

    <div class="nav-section-label">Sistem</div>

    <a href="settings.php" class="nav-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Pengaturan
    </a>

    <a href="../index.php" target="_blank" class="nav-item">
      <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Lihat Website
    </a>
  </nav>

  <div class="sidebar-user">
    <div class="user-avatar"><?= strtoupper(substr($adminUsername, 0, 1)) ?></div>
    <div class="user-info">
      <div class="user-name"><?= clean($adminUsername) ?></div>
      <div class="user-role">Super Admin</div>
    </div>
    <a href="logout.php" class="logout-btn" title="Logout">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </a>
  </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="mobile-menu-btn" onclick="openSidebar()">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div>
        <div class="page-title"><?= $pageTitle ?? 'Dashboard' ?></div>
        <div class="page-breadcrumb">
          <span>Klivra CMS</span>
          <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
          <span><?= $pageTitle ?? 'Dashboard' ?></span>
        </div>
      </div>
    </div>
    <div class="topbar-right">
      <a href="../../index.php" target="_blank" class="topbar-btn">
        <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Preview Site
      </a>
    </div>
  </div>

  <!-- FLASH MESSAGE -->
  <div id="flash-container" style="padding: 0 1.8rem; margin-top: 1.2rem;">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <?php if ($flash['type'] === 'success'): ?>
          <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        <?php else: ?>
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <?php endif; ?>
        <?= clean($flash['message']) ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- PAGE CONTENT START -->
  <div class="page-content">

<script>
  function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('show');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
  }
  // Auto-hide flash after 4s
  setTimeout(() => {
    const fc = document.getElementById('flash-container');
    if (fc) fc.style.transition = 'opacity .5s', fc.style.opacity = '0';
    setTimeout(() => fc && (fc.style.display='none'), 500);
  }, 4000);
</script>
