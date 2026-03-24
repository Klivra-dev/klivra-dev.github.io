<?php
require_once __DIR__ . '/includes/config.php';
$db = getDB();

// ── FETCH ALL CONTENT ──
$hero       = $db->query("SELECT * FROM hero WHERE id=1")->fetch() ?: [];
$about      = $db->query("SELECT * FROM about WHERE id=1")->fetch() ?: [];
$skills     = $db->query("SELECT name FROM skills WHERE is_active=1 ORDER BY sort_order, id")->fetchAll();
$services   = $db->query("SELECT * FROM services WHERE is_active=1 ORDER BY sort_order, id")->fetchAll();
$experience = $db->query("SELECT * FROM experience WHERE is_active=1 ORDER BY sort_order, id")->fetchAll();
$portfolio  = $db->query("SELECT * FROM portfolio WHERE is_active=1 ORDER BY sort_order, id")->fetchAll();

// Settings
$siteTitle   = getSetting('site_title', 'Nataniel Pendong – Teknisi Jaringan CCTV & Web Development');
$navLogo     = getSetting('nav_logo_text', 'Secure visions');
$waNumber    = getSetting('whatsapp_number', '6289504211494');
$waMessage   = getSetting('whatsapp_default_message', 'Halo Nataniel, saya ingin konsultasi gratis mengenai layanan CCTV/Jaringan');
$metaDesc    = getSetting('meta_description', '');
$footerText  = getSetting('footer_text', '© 2025 Nataniel Pendong.');
$contactEmail= getSetting('contact_email', '');
$contactLoc  = getSetting('contact_location', 'Manado, Sulawesi Utara');

$waLink = "https://wa.me/" . urlencode($waNumber) . "?text=" . urlencode($waMessage);
$waConsultLink = "https://wa.me/" . urlencode($waNumber) . "?text=" . urlencode("Halo Nataniel, saya ingin konsultasi gratis mengenai layanan CCTV/Jaringan");

// Portfolio counts for filter badges
$catCounts = ['all'=>0,'networking'=>0,'cctv'=>0,'programming'=>0];
foreach ($portfolio as $p) {
    $catCounts['all']++;
    if (isset($catCounts[$p['category']])) $catCounts[$p['category']]++;
}

// Profile image path
$profileImg = $about['profile_image'] ?? 'pp.jpeg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <?php if ($metaDesc): ?>
  <meta name="description" content="<?= clean($metaDesc) ?>">
  <?php endif; ?>
  <title><?= clean($siteTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.383.0/umd/lucide.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg: #0b0d10;
      --surface: #111318;
      --card: #161a21;
      --border: #1e2330;
      --accent: #00d4ff;
      --accent2: #0097b2;
      --text: #e8eaf0;
      --muted: #6b7280;
      --green: #22c55e;
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      line-height: 1.6;
      overflow-x: hidden;
    }

    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      display: flex; justify-content: space-between; align-items: center;
      padding: 1.1rem 6vw;
      background: rgba(11,13,16,.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
    }
    .nav-logo {
      font-family: 'Syne', sans-serif;
      font-weight: 800; font-size: 1.1rem;
      color: var(--accent); letter-spacing: .04em;
    }
    .nav-links { display: flex; gap: 2rem; list-style: none; }
    .nav-links a {
      color: var(--muted); text-decoration: none; font-size: .9rem;
      transition: color .2s;
    }
    .nav-links a:hover { color: var(--accent); }

    #hero {
      min-height: 100vh;
      display: flex; flex-direction: column; justify-content: center;
      padding: 8rem 6vw 5rem;
      position: relative; overflow: hidden;
    }
    .hero-grid-bg {
      position: absolute; inset: 0; z-index: 0;
      background-image:
        linear-gradient(var(--border) 1px, transparent 1px),
        linear-gradient(90deg, var(--border) 1px, transparent 1px);
      background-size: 48px 48px;
      opacity: .35;
    }
    .hero-glow {
      position: absolute; top: -120px; left: -80px; z-index: 0;
      width: 500px; height: 500px; border-radius: 50%;
      background: radial-gradient(circle, rgba(0,212,255,.12) 0%, transparent 70%);
      pointer-events: none;
    }
    .hero-content { position: relative; z-index: 1; max-width: 760px; }
    .hero-badge {
      display: inline-flex; align-items: center; gap: .5rem;
      background: rgba(0,212,255,.08); border: 1px solid rgba(0,212,255,.2);
      color: var(--accent); border-radius: 999px;
      font-size: .8rem; font-weight: 500; padding: .35rem .9rem;
      margin-bottom: 1.6rem;
      animation: fadeUp .6s ease both;
    }
    .hero-badge span.dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: var(--green); box-shadow: 0 0 6px var(--green);
      animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

    h1 {
      font-family: 'Syne', sans-serif;
      font-size: clamp(2.4rem, 6vw, 4.2rem);
      font-weight: 800; line-height: 1.1;
      margin-bottom: 1.2rem;
      animation: fadeUp .7s .1s ease both;
    }
    h1 span { color: var(--accent); }
    .hero-sub {
      color: var(--muted); font-size: 1.05rem; max-width: 560px;
      margin-bottom: 2.2rem;
      animation: fadeUp .7s .2s ease both;
    }
    .hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; animation: fadeUp .7s .3s ease both; }
    .btn-primary {
      background: var(--accent); color: #000;
      font-family: 'Syne', sans-serif; font-weight: 700;
      padding: .75rem 1.8rem; border-radius: 6px;
      text-decoration: none; font-size: .95rem;
      transition: background .2s, transform .15s;
    }
    .btn-primary:hover { background: #33ddff; transform: translateY(-2px); }
    .btn-konsultasi { animation: ctaPulse 2.5s infinite; }
    @keyframes ctaPulse {
      0%   { box-shadow: 0 0 0 0 rgba(0,212,255,.45); }
      70%  { box-shadow: 0 0 0 10px rgba(0,212,255,0); }
      100% { box-shadow: 0 0 0 0 rgba(0,212,255,0); }
    }
    .float-konsultasi {
      position: fixed; bottom: 1.8rem; right: 1.8rem; z-index: 99;
      display: flex; align-items: center; gap: .6rem;
      background: #25d366; color: #000;
      font-family: 'Syne', sans-serif; font-weight: 700; font-size: .88rem;
      padding: .75rem 1.4rem; border-radius: 999px;
      text-decoration: none;
      box-shadow: 0 6px 24px rgba(37,211,102,.4);
      transition: transform .2s, box-shadow .2s;
      animation: floatIn .5s .8s ease both;
    }
    .float-konsultasi:hover { transform: translateY(-3px) scale(1.04); }
    .float-konsultasi svg { width:20px;height:20px;flex-shrink:0; }
    .float-konsultasi .float-label { white-space: nowrap; }
    @keyframes floatIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .float-konsultasi::before {
      content:''; position:absolute; inset:-4px;
      border-radius:999px; border:2px solid rgba(37,211,102,.5);
      animation: ringPulse 2s infinite;
    }
    @keyframes ringPulse { 0%{transform:scale(1);opacity:.8} 100%{transform:scale(1.2);opacity:0} }
    .btn-outline {
      border: 1px solid var(--border); color: var(--text);
      padding: .75rem 1.8rem; border-radius: 6px;
      text-decoration: none; font-size: .95rem;
      transition: border-color .2s, transform .15s;
    }
    .btn-outline:hover { border-color: var(--accent); transform: translateY(-2px); }
    .hero-stats { display: flex; gap: 2.5rem; margin-top: 3.5rem; animation: fadeUp .7s .45s ease both; }
    .stat-num { font-family: 'Syne', sans-serif; font-size: 1.8rem; font-weight: 800; color: var(--accent); }
    .stat-label { font-size: .8rem; color: var(--muted); margin-top: .1rem; }

    section { padding: 5rem 6vw; }
    .section-label { font-size: .75rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--accent); margin-bottom: .6rem; }
    h2 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: clamp(1.7rem, 3.5vw, 2.5rem); margin-bottom: 1rem; }
    .section-desc { color: var(--muted); max-width: 520px; margin-bottom: 3rem; }

    #services { background: var(--surface); }
    .services-grid { display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
    .service-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 12px; padding: 1.8rem;
      transition: border-color .2s, transform .2s;
    }
    .service-card:hover { border-color: var(--accent2); transform: translateY(-4px); }
    .service-icon {
      width: 44px; height: 44px; border-radius: 10px;
      background: rgba(0,212,255,.1); border: 1px solid rgba(0,212,255,.2);
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 1rem;
    }
    .service-icon svg { width: 20px; height: 20px; stroke: var(--accent); stroke-width: 1.75; fill: none; }
    .service-card h3 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; margin-bottom: .5rem; }
    .service-card p { color: var(--muted); font-size: .88rem; line-height: 1.6; }

    #about .about-flex { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
    .about-img-wrap {
      position: relative; border-radius: 20px; overflow: hidden;
      border: 1px solid var(--border);
      box-shadow: 0 0 0 5px rgba(0,212,255,.06), 0 32px 64px rgba(0,0,0,.55);
      aspect-ratio: 3/4;
    }
    .about-img-wrap img { width: 100%; height: 100%; object-fit: cover; object-position: center top; display: block; transition: transform .5s ease; }
    .about-img-wrap:hover img { transform: scale(1.03); }
    .about-img-wrap::before { content:''; position:absolute; top:-2px; left:-2px; right:-2px; bottom:-2px; border-radius:22px; z-index:-1; background: linear-gradient(135deg, rgba(0,212,255,.25) 0%, transparent 50%); }
    .about-img-wrap::after { content:''; position:absolute; bottom:0; left:0; right:0; height:40%; background: linear-gradient(to top, rgba(11,13,16,.7) 0%, transparent 100%); border-radius:0 0 20px 20px; pointer-events:none; }
    .about-img-badge {
      position:absolute; bottom:1.2rem; left:1.2rem; z-index:2;
      display:flex; align-items:center; gap:.5rem;
      background:rgba(11,13,16,.8); backdrop-filter:blur(10px);
      border:1px solid rgba(0,212,255,.25); border-radius:999px;
      padding:.4rem 1rem; font-size:.78rem; font-weight:500; color:var(--accent);
    }
    .about-img-badge .dot { width:6px;height:6px;border-radius:50%;background:var(--green);box-shadow:0 0 6px var(--green); animation:pulse 2s infinite; }
    .about-text h2 { margin-bottom: 1rem; }
    .about-text p { color: var(--muted); margin-bottom: 1rem; font-size: .95rem; }
    .tag-list { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.2rem; }
    .tag { background: rgba(0,212,255,.07); border: 1px solid rgba(0,212,255,.15); color: var(--accent); border-radius: 6px; padding: .25rem .7rem; font-size: .78rem; font-weight: 500; }

    #gallery { background: var(--surface); }
    .gallery-filters { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 2rem; }
    .filter-btn { display: flex; align-items: center; gap: .4rem; padding: .45rem 1.1rem; border-radius: 999px; border: 1px solid var(--border); background: transparent; color: var(--muted); font-family: 'DM Sans', sans-serif; font-size: .85rem; font-weight: 500; cursor: pointer; transition: all .2s; }
    .filter-btn:hover { border-color: var(--accent2); color: var(--text); }
    .filter-btn.active { background: var(--accent); border-color: var(--accent); color: #000; font-weight: 700; }
    .filter-btn .filter-count { background: rgba(0,0,0,.15); border-radius: 999px; font-size: .7rem; padding: .05rem .45rem; font-weight: 700; }
    .filter-btn.active .filter-count { background: rgba(0,0,0,.2); }
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
    .gallery-item { background: var(--card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; position: relative; cursor: pointer; transition: border-color .25s, transform .25s, box-shadow .25s; aspect-ratio: 4/3; }
    .gallery-item.hidden { display: none; }
    .gallery-item:hover { border-color: var(--accent); transform: translateY(-5px); box-shadow: 0 16px 40px rgba(0,212,255,.13); }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .45s ease; }
    .gallery-item:hover img { transform: scale(1.07); }
    .gallery-cat-badge { position: absolute; top: .7rem; left: .7rem; z-index: 2; font-size: .68rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; padding: .22rem .65rem; border-radius: 999px; backdrop-filter: blur(8px); }
    .gallery-cat-badge.networking { background: rgba(0,212,255,.18); color: #00d4ff; border: 1px solid rgba(0,212,255,.3); }
    .gallery-cat-badge.cctv       { background: rgba(251,191,36,.18); color: #fbbf24;  border: 1px solid rgba(251,191,36,.3); }
    .gallery-cat-badge.programming{ background: rgba(139,92,246,.18); color: #a78bfa;  border: 1px solid rgba(139,92,246,.3); }
    .gallery-overlay { position:absolute; inset:0; background: linear-gradient(to top, rgba(8,10,13,.92) 0%, rgba(8,10,13,.3) 60%, transparent 100%); opacity:0; transition: opacity .3s; display:flex; flex-direction:column; justify-content:flex-end; padding:1rem; }
    .gallery-item:hover .gallery-overlay { opacity:1; }
    .gallery-overlay-title { font-family:'Syne',sans-serif; font-weight:700; font-size:.88rem; margin-bottom:.6rem; color:#fff; }
    .gallery-overlay-btns { display:flex; gap:.5rem; flex-wrap:wrap; }
    .gov-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .75rem; border-radius:6px; font-size:.75rem; font-weight:600; cursor:pointer; border:none; transition:background .15s; font-family:'DM Sans',sans-serif; }
    .gov-btn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2; }
    .gov-btn.zoom { background:rgba(255,255,255,.15); color:#fff; }
    .gov-btn.zoom:hover { background:rgba(255,255,255,.25); }
    .gov-btn.detail { background:rgba(0,212,255,.2); color:var(--accent); }
    .gov-btn.detail:hover { background:rgba(0,212,255,.35); }

    #experience { background: var(--bg); }
    .exp-list { display: flex; flex-direction: column; gap: 1.5rem; max-width: 820px; }
    .exp-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 1.8rem 2rem; display: grid; grid-template-columns: auto 1fr; gap: 0 1.6rem; align-items: start; transition: border-color .2s, transform .2s; position: relative; overflow: hidden; }
    .exp-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; background:var(--accent); opacity:0; transition:opacity .2s; }
    .exp-card:hover { border-color: var(--accent2); transform: translateX(4px); }
    .exp-card:hover::before { opacity: 1; }
    .exp-logo { width:48px; height:48px; border-radius:10px; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:.2rem; }
    .exp-logo svg { width:22px; height:22px; stroke:var(--accent); stroke-width:1.75; fill:none; }
    .exp-top { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.4rem; margin-bottom:.35rem; }
    .exp-company { font-family:'Syne',sans-serif; font-weight:700; font-size:1rem; color:var(--text); }
    .exp-period { font-size:.78rem; color:var(--muted); background:rgba(255,255,255,.04); border:1px solid var(--border); border-radius:999px; padding:.2rem .75rem; white-space:nowrap; }
    .exp-role { font-size:.88rem; color:var(--accent); font-weight:500; margin-bottom:.7rem; }
    .exp-tasks { list-style:none; display:flex; flex-direction:column; gap:.4rem; }
    .exp-tasks li { font-size:.87rem; color:var(--muted); padding-left:1.1rem; position:relative; }
    .exp-tasks li::before { content:''; position:absolute; left:0; top:.55em; width:5px; height:5px; border-radius:50%; background:var(--accent); opacity:.6; }
    .exp-tags { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.9rem; }
    .exp-tag { font-size:.72rem; font-weight:500; background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.12); color:var(--accent); border-radius:5px; padding:.18rem .6rem; }
    .exp-status-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; color:var(--green); font-weight:500; }
    .exp-status-badge .dot-green { width:6px; height:6px; border-radius:50%; background:var(--green); box-shadow:0 0 5px var(--green); }

    /* CONTACT */
    #contact { background: var(--surface); }
    .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start; }
    .contact-item { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.2rem; }
    .contact-icon { width:42px; height:42px; border-radius:10px; background:rgba(0,212,255,.1); border:1px solid rgba(0,212,255,.2); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .contact-icon svg { width:18px; height:18px; stroke:var(--accent); stroke-width:1.75; fill:none; }
    .contact-label { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
    .contact-value { font-size:.9rem; font-weight:500; }
    .contact-wa { display:flex; align-items:center; gap:.7rem; background:rgba(37,211,102,.1); border:1px solid rgba(37,211,102,.25); border-radius:12px; padding:1.2rem 1.5rem; text-decoration:none; color:var(--green); font-family:'Syne',sans-serif; font-weight:700; font-size:1rem; transition:background .2s, transform .15s; }
    .contact-wa:hover { background:rgba(37,211,102,.18); transform:translateY(-2px); }
    .contact-wa svg { width:22px; height:22px; fill:var(--green); flex-shrink:0; }

    /* LIGHTBOX */
    #lightbox { display:none; position:fixed; inset:0; z-index:999; background:rgba(0,0,0,.92); backdrop-filter:blur(8px); }
    #lightbox.active { display:flex; }
    .lb-inner { display:flex; width:100%; height:100%; position:relative; }
    .lb-img-side { flex:1; display:flex; align-items:center; justify-content:center; padding:1.5rem; position:relative; overflow:hidden; cursor:zoom-in; }
    .lb-img-side.zoomed { cursor:zoom-out; }
    #lb-img { max-width:100%; max-height:90vh; object-fit:contain; border-radius:8px; transition:transform .3s ease; user-select:none; pointer-events:none; }
    .lb-img-side.zoomed #lb-img { transform:scale(2); }
    .lb-controls { position:absolute; top:1rem; right:1rem; display:flex; gap:.5rem; z-index:10; }
    .lb-btn { width:38px; height:38px; border-radius:8px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s; }
    .lb-btn:hover { background:rgba(255,255,255,.2); }
    .lb-btn svg { width:17px; height:17px; stroke:currentColor; fill:none; stroke-width:2; }
    .lb-nav { position:absolute; top:50%; transform:translateY(-50%); width:44px; height:44px; border-radius:50%; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:10; transition:background .15s, opacity .2s; }
    .lb-nav:hover { background:rgba(255,255,255,.2); }
    .lb-nav svg { width:20px; height:20px; stroke:currentColor; fill:none; stroke-width:2.5; }
    #lb-prev { left:1rem; }
    #lb-next { right:1rem; }
    .lb-detail { width:320px; min-width:280px; background:rgba(22,26,33,.95); border-left:1px solid var(--border); display:flex; flex-direction:column; padding:1.5rem; overflow-y:auto; transform:translateX(100%); transition:transform .3s ease; }
    .lb-detail.open { transform:translateX(0); }
    .lb-detail-cat { display:inline-block; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:.2rem .65rem; border-radius:999px; margin-bottom:.8rem; }
    .lb-detail-cat.networking { background:rgba(0,212,255,.18); color:#00d4ff; border:1px solid rgba(0,212,255,.3); }
    .lb-detail-cat.cctv { background:rgba(251,191,36,.18); color:#fbbf24; border:1px solid rgba(251,191,36,.3); }
    .lb-detail-cat.programming { background:rgba(139,92,246,.18); color:#a78bfa; border:1px solid rgba(139,92,246,.3); }
    #lb-det-title { font-family:'Syne',sans-serif; font-size:1.05rem; font-weight:800; margin-bottom:.6rem; }
    #lb-det-desc { font-size:.85rem; color:var(--muted); line-height:1.6; margin-bottom:1rem; }
    .lb-meta-row { display:flex; align-items:center; gap:.6rem; font-size:.82rem; color:var(--muted); margin-bottom:.5rem; }
    .lb-meta-row svg { width:14px; height:14px; stroke:var(--accent); fill:none; stroke-width:2; flex-shrink:0; }
    .lb-meta-row strong { color:var(--text); }
    .lb-detail-divider { border:none; border-top:1px solid var(--border); margin:1rem 0; }
    .lb-detail-wa { display:flex; align-items:center; gap:.6rem; background:rgba(37,211,102,.12); border:1px solid rgba(37,211,102,.25); border-radius:10px; padding:.8rem 1rem; text-decoration:none; color:var(--green); font-size:.82rem; font-weight:600; margin-top:auto; transition:background .2s; }
    .lb-detail-wa:hover { background:rgba(37,211,102,.22); }
    .lb-detail-wa svg { width:18px; height:18px; flex-shrink:0; }
    #lb-thumb-wrap { padding:1rem 1.5rem 0; }
    #lb-thumb { width:80px; height:60px; object-fit:cover; border-radius:6px; border:2px solid var(--accent); display:block; }
    #lb-counter { position:absolute; bottom:1rem; left:50%; transform:translateX(-50%); background:rgba(0,0,0,.6); color:var(--muted); font-size:.78rem; padding:.3rem .8rem; border-radius:999px; }

    footer { padding:2rem 6vw; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; color:var(--muted); font-size:.82rem; }
    footer a { color:var(--accent); text-decoration:none; }

    @media (max-width: 768px) {
      #about .about-flex { grid-template-columns: 1fr; gap: 2rem; }
      .about-img-wrap { aspect-ratio: 4/3; max-height: 320px; }
      .hero-stats { gap: 1.5rem; }
      nav .nav-links { display:none; }
      .float-konsultasi .float-label { display: none; }
      .float-konsultasi { padding: .85rem; border-radius: 50%; }
      .contact-grid { grid-template-columns: 1fr; }
      .lb-detail { display:none; }
    }
    @media (max-width: 480px) {
      .about-img-wrap { aspect-ratio: 1/1; max-height: 280px; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-logo"><?= clean($navLogo) ?></div>
  <ul class="nav-links">
    <li><a href="#services">Layanan</a></li>
    <li><a href="#about">Tentang</a></li>
    <li><a href="#experience">Pengalaman</a></li>
    <li><a href="#gallery">Portofolio</a></li>
    <li><a href="#contact">Kontak</a></li>
  </ul>
</nav>

<!-- HERO -->
<section id="hero">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow"></div>
  <div class="hero-content">
    <div class="hero-badge">
      <span class="dot"></span>
      <?= clean($hero['badge_text'] ?? 'Tersedia untuk proyek baru') ?>
    </div>
    <h1>
      <?= clean($hero['title_line1'] ?? 'Teknisi') ?>
      <span><?= clean($hero['title_highlight'] ?? 'Jaringan CCTV') ?></span><br>
      <?= clean($hero['title_line2'] ?? '& Web Development Profesional') ?>
    </h1>
    <p class="hero-sub"><?= clean($hero['subtitle'] ?? '') ?></p>
    <div class="hero-cta">
      <a href="<?= htmlspecialchars($waLink) ?>" target="_blank" class="btn-primary btn-konsultasi">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:17px;height:17px;display:inline;vertical-align:middle;margin-right:.45rem;fill:#000;stroke:none"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.121 1.529 5.845L.057 23.428a.75.75 0 0 0 .916.914l5.453-1.453A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.686-.53-5.204-1.446l-.374-.224-3.88 1.035 1.012-3.75-.242-.386A9.958 9.958 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        Hubungi Saya
      </a>
      <a href="#gallery" class="btn-outline">Lihat Portofolio</a>
    </div>
    <div class="hero-stats">
      <div>
        <div class="stat-num"><?= clean($hero['stat_projects'] ?? '10+') ?></div>
        <div class="stat-label">Proyek Selesai</div>
      </div>
      <div>
        <div class="stat-num"><?= clean($hero['stat_years'] ?? '2+') ?></div>
        <div class="stat-label">Tahun Pengalaman</div>
      </div>
      <div>
        <div class="stat-num"><?= clean($hero['stat_satisfaction'] ?? '100%') ?></div>
        <div class="stat-label">Kepuasan Klien</div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES -->
<?php if ($services): ?>
<section id="services">
  <div class="section-label">Apa yang saya kerjakan</div>
  <h2>Layanan Unggulan</h2>
  <p class="section-desc">Solusi lengkap untuk keamanan dan infrastruktur jaringan Anda — dari pemasangan hingga maintenance.</p>
  <div class="services-grid">
    <?php foreach ($services as $svc): ?>
    <div class="service-card">
      <?php if ($svc['icon_svg']): ?>
      <div class="service-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><?= $svc['icon_svg'] ?></svg>
      </div>
      <?php endif; ?>
      <h3><?= clean($svc['title']) ?></h3>
      <p><?= clean($svc['description']) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ABOUT -->
<section id="about">
  <div class="about-flex">
    <div class="about-img-wrap">
      <img src="<?= clean($profileImg) ?>" alt="Foto Profil <?= clean($about['name'] ?? 'Nataniel Pendong') ?>">
      <div class="about-img-badge">
        <span class="dot"></span>
        <?= clean($about['badge_text'] ?? 'Tersedia untuk proyek') ?>
      </div>
    </div>
    <div class="about-text">
      <div class="section-label">Tentang Saya</div>
      <h2><?= clean($about['headline1'] ?? 'Mahasiswa.') ?><br><?= clean($about['headline2'] ?? 'Praktisi. Problem Solver.') ?></h2>
      <?php if ($about['bio_paragraph1']): ?><p><?= clean($about['bio_paragraph1']) ?></p><?php endif; ?>
      <?php if ($about['bio_paragraph2']): ?><p><?= clean($about['bio_paragraph2']) ?></p><?php endif; ?>
      <?php if ($about['bio_paragraph3']): ?><p><?= clean($about['bio_paragraph3']) ?></p><?php endif; ?>
      <?php if ($skills): ?>
      <div class="tag-list">
        <?php foreach ($skills as $sk): ?>
          <span class="tag"><?= clean($sk['name']) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- GALLERY -->
<?php if ($portfolio): ?>
<section id="gallery">
  <div class="section-label">Hasil Kerja Saya</div>
  <h2>Portofolio</h2>
  <p class="section-desc">Dokumentasi proyek nyata. Klik foto untuk memperbesar atau lihat detail.</p>
  <div class="gallery-filters">
    <button class="filter-btn active" data-filter="all">
      All <span class="filter-count" id="count-all"><?= $catCounts['all'] ?></span>
    </button>
    <button class="filter-btn" data-filter="networking">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
      Networking <span class="filter-count" id="count-networking"><?= $catCounts['networking'] ?></span>
    </button>
    <button class="filter-btn" data-filter="cctv">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
      CCTV <span class="filter-count" id="count-cctv"><?= $catCounts['cctv'] ?></span>
    </button>
    <button class="filter-btn" data-filter="programming">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
      Programming <span class="filter-count" id="count-programming"><?= $catCounts['programming'] ?></span>
    </button>
  </div>
  <div class="gallery-grid" id="gallery-grid">
    <?php foreach ($portfolio as $item): ?>
    <div class="gallery-item"
         data-category="<?= clean($item['category']) ?>"
         data-title="<?= clean($item['title']) ?>"
         data-desc="<?= clean($item['description'] ?? '') ?>"
         data-client="<?= clean($item['client'] ?? '') ?>"
         data-year="<?= clean($item['year'] ?? '') ?>">
      <img src="<?= clean($item['image']) ?>" alt="<?= clean($item['title']) ?>"
           loading="lazy"
           onerror="this.style.background='var(--card)';this.style.display='none'">
      <span class="gallery-cat-badge <?= clean($item['category']) ?>"><?= ucfirst(clean($item['category'])) ?></span>
      <div class="gallery-overlay">
        <div class="gallery-overlay-title"><?= clean($item['overlay_title'] ?? $item['title']) ?></div>
        <div class="gallery-overlay-btns">
          <button class="gov-btn zoom" onclick="event.stopPropagation();openLb(this.closest('.gallery-item'),false)">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg> Zoom
          </button>
          <button class="gov-btn detail" onclick="event.stopPropagation();openLb(this.closest('.gallery-item'),true)">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Lihat Detail
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- EXPERIENCE -->
<?php if ($experience): ?>
<section id="experience">
  <div class="section-label">Jejak Karir</div>
  <h2>Pengalaman Kerja</h2>
  <p class="section-desc">Perjalanan profesional saya di dunia jaringan, CCTV, dan pengembangan web.</p>
  <div class="exp-list">
    <?php foreach ($experience as $exp):
      $tasks = json_decode($exp['tasks'] ?? '[]', true) ?: [];
      $tags  = json_decode($exp['tags']  ?? '[]', true) ?: [];
    ?>
    <div class="exp-card">
      <div class="exp-logo">
        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div class="exp-body">
        <div class="exp-top">
          <span class="exp-company"><?= clean($exp['company']) ?></span>
          <span class="exp-period"><?= clean($exp['period']) ?></span>
        </div>
        <div class="exp-role">
          <?= clean($exp['role']) ?>
          <?php if ($exp['is_current']): ?>
            <span class="exp-status-badge" style="margin-left:.6rem;">
              <span class="dot-green"></span> Aktif
            </span>
          <?php endif; ?>
        </div>
        <?php if ($tasks): ?>
        <ul class="exp-tasks">
          <?php foreach ($tasks as $task): ?>
            <li><?= clean($task) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ($tags): ?>
        <div class="exp-tags">
          <?php foreach ($tags as $tag): ?>
            <span class="exp-tag"><?= clean($tag) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- CONTACT -->
<section id="contact">
  <div class="section-label">Hubungi Saya</div>
  <h2>Mari Bekerja Sama</h2>
  <p class="section-desc">Punya proyek atau butuh bantuan teknis? Saya siap membantu Anda.</p>
  <div class="contact-grid">
    <div>
      <?php if ($contactLoc): ?>
      <div class="contact-item">
        <div class="contact-icon"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <div><div class="contact-label">Lokasi</div><div class="contact-value"><?= clean($contactLoc) ?></div></div>
      </div>
      <?php endif; ?>
      <?php if ($contactEmail): ?>
      <div class="contact-item">
        <div class="contact-icon"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
        <div><div class="contact-label">Email</div><div class="contact-value"><?= clean($contactEmail) ?></div></div>
      </div>
      <?php endif; ?>
      <div class="contact-item">
        <div class="contact-icon"><svg viewBox="0 0 24 24" style="fill:none;stroke:var(--accent);stroke-width:1.75;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.68 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
        <div><div class="contact-label">WhatsApp</div><div class="contact-value">+<?= clean($waNumber) ?></div></div>
      </div>
    </div>
    <div>
      <a href="<?= htmlspecialchars($waConsultLink) ?>" target="_blank" class="contact-wa">
        <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.121 1.529 5.845L.057 23.428a.75.75 0 0 0 .916.914l5.453-1.453A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.686-.53-5.204-1.446l-.374-.224-3.88 1.035 1.012-3.75-.242-.386A9.958 9.958 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        Konsultasi Gratis via WhatsApp
      </a>
      <p style="margin-top:1rem;font-size:.82rem;color:var(--muted);">Respon cepat, biasanya dalam 1 jam. Konsultasi awal gratis tanpa syarat.</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <span><?= clean($footerText) ?></span>
  <a href="admin/login.php">Admin</a>
</footer>

<!-- FLOATING WA BUTTON -->
<a class="float-konsultasi" href="<?= htmlspecialchars($waConsultLink) ?>" target="_blank">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill:#000;stroke:none;flex-shrink:0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.121 1.529 5.845L.057 23.428a.75.75 0 0 0 .916.914l5.453-1.453A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.686-.53-5.204-1.446l-.374-.224-3.88 1.035 1.012-3.75-.242-.386A9.958 9.958 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
  <span class="float-label">Konsultasi Gratis</span>
</a>

<!-- LIGHTBOX -->
<div id="lightbox">
  <div class="lb-inner">
    <div class="lb-img-side" id="lb-img-side">
      <div class="lb-controls">
        <button class="lb-btn" id="lb-zoom-btn" title="Zoom"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg></button>
        <button class="lb-btn" id="lb-detail-btn" title="Detail"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></button>
        <button class="lb-btn" id="lb-close" title="Tutup"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>
      <button class="lb-nav" id="lb-prev"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
      <img id="lb-img" src="" alt="">
      <button class="lb-nav" id="lb-next"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
    <div class="lb-detail" id="lb-detail">
      <div id="lb-thumb-wrap"><img id="lb-thumb" src="" alt=""></div>
      <div style="padding:1rem 0 0;">
        <span class="lb-detail-cat" id="lb-det-cat"></span>
        <div id="lb-det-title"></div>
        <div id="lb-det-desc"></div>
        <div class="lb-meta-row">
          <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span>Klien: <strong id="lb-det-client"></strong></span>
        </div>
        <div class="lb-meta-row">
          <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span>Tahun: <strong id="lb-det-year"></strong></span>
        </div>
        <hr class="lb-detail-divider">
        <a class="lb-detail-wa" href="<?= htmlspecialchars($waConsultLink) ?>" target="_blank">
          <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.121 1.529 5.845L.057 23.428a.75.75 0 0 0 .916.914l5.453-1.453A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.686-.53-5.204-1.446l-.374-.224-3.88 1.035 1.012-3.75-.242-.386A9.958 9.958 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
          Konsultasi Proyek Ini
        </a>
      </div>
    </div>
  </div>
  <div id="lb-counter"></div>
</div>

<script>
  // Gallery Filter
  const allItems   = Array.from(document.querySelectorAll('#gallery-grid .gallery-item'));
  const filterBtns = document.querySelectorAll('.filter-btn');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const f = btn.dataset.filter;
      allItems.forEach(it => {
        it.classList.toggle('hidden', f !== 'all' && it.dataset.category !== f);
      });
    });
  });

  // Lightbox
  const lb        = document.getElementById('lightbox');
  const lbImg     = document.getElementById('lb-img');
  const lbThumb   = document.getElementById('lb-thumb');
  const lbImgSide = document.getElementById('lb-img-side');
  const lbDetail  = document.getElementById('lb-detail');
  const lbCounter = document.getElementById('lb-counter');
  let lbIndex = 0, lbItems = [], isZoomed = false, detailOpen = false;

  function openLb(el, showDetail) {
    lbItems = allItems.filter(it => it.querySelector('img') && !it.classList.contains('hidden'));
    lbIndex = lbItems.indexOf(el);
    if (lbIndex < 0) lbIndex = 0;
    renderLb();
    if (showDetail) openDetail(); else closeDetail();
    lb.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function renderLb() {
    const it  = lbItems[lbIndex];
    const img = it.querySelector('img');
    lbImg.src = img.src;
    lbThumb.src = img.src;
    resetZoom();
    document.getElementById('lb-det-title').textContent  = it.dataset.title  || '';
    document.getElementById('lb-det-desc').textContent   = it.dataset.desc   || '';
    document.getElementById('lb-det-client').textContent = it.dataset.client || '';
    document.getElementById('lb-det-year').textContent   = it.dataset.year   || '';
    const cat = it.dataset.category || '';
    const catEl = document.getElementById('lb-det-cat');
    catEl.textContent = cat.charAt(0).toUpperCase() + cat.slice(1);
    catEl.className = 'lb-detail-cat ' + cat;
    lbCounter.textContent = (lbIndex + 1) + ' / ' + lbItems.length;
    document.getElementById('lb-prev').style.opacity = lbIndex === 0 ? '.3' : '1';
    document.getElementById('lb-next').style.opacity = lbIndex === lbItems.length - 1 ? '.3' : '1';
  }

  function closeLb() { lb.classList.remove('active'); document.body.style.overflow = ''; closeDetail(); resetZoom(); }
  function openDetail() { detailOpen=true; lbDetail.classList.add('open'); }
  function closeDetail() { detailOpen=false; lbDetail.classList.remove('open'); }
  function resetZoom() { isZoomed=false; lbImgSide.classList.remove('zoomed'); }
  function toggleZoom() { isZoomed=!isZoomed; lbImgSide.classList.toggle('zoomed',isZoomed); }

  document.getElementById('lb-close').addEventListener('click', closeLb);
  document.getElementById('lb-zoom-btn').addEventListener('click', toggleZoom);
  lbImgSide.addEventListener('click', e => { if(e.target===lbImgSide||e.target===lbImg) toggleZoom(); });
  document.getElementById('lb-detail-btn').addEventListener('click', () => { detailOpen ? closeDetail() : openDetail(); });
  document.getElementById('lb-prev').addEventListener('click', e => { e.stopPropagation(); if(lbIndex>0){lbIndex--;renderLb();} });
  document.getElementById('lb-next').addEventListener('click', e => { e.stopPropagation(); if(lbIndex<lbItems.length-1){lbIndex++;renderLb();} });
  lb.addEventListener('click', e => { if(e.target===lb) closeLb(); });

  document.addEventListener('keydown', e => {
    if (!lb.classList.contains('active')) return;
    if (e.key==='Escape') closeLb();
    if (e.key==='ArrowLeft' && lbIndex>0) { lbIndex--; renderLb(); }
    if (e.key==='ArrowRight' && lbIndex<lbItems.length-1) { lbIndex++; renderLb(); }
    if (e.key==='i') { detailOpen ? closeDetail() : openDetail(); }
  });

  let tx = 0;
  lb.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, {passive:true});
  lb.addEventListener('touchend', e => {
    const d = tx - e.changedTouches[0].clientX;
    if (Math.abs(d) < 40) return;
    if (d > 0 && lbIndex < lbItems.length-1) { lbIndex++; renderLb(); }
    if (d < 0 && lbIndex > 0) { lbIndex--; renderLb(); }
  });

  document.addEventListener('contextmenu', e => { if(e.target.tagName==='IMG') e.preventDefault(); });
</script>
</body>
</html>
