<?php
require_once __DIR__ . '/includes/config.php';
startAdminSession();

// Redirect if already logged in
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$error = '';
$timeout = isset($_GET['timeout']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Sesi tidak valid. Silakan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username && $password) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_id']       = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['last_activity']  = time();
                session_regenerate_id(true);
                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error = 'Username atau password salah.';
                // Small delay to prevent brute force
                sleep(1);
            }
        } else {
            $error = 'Masukkan username dan password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Klivra CMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg: #080a0d;
      --surface: #0f1218;
      --card: #141820;
      --border: #1e2533;
      --accent: #00d4ff;
      --accent2: #0097b2;
      --text: #e8eaf0;
      --muted: #6b7280;
      --danger: #ef4444;
      --warning: #f59e0b;
      --green: #22c55e;
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Grid background */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image:
        linear-gradient(rgba(0,212,255,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,212,255,.04) 1px, transparent 1px);
      background-size: 50px 50px;
    }

    /* Glow orbs */
    .glow-1 {
      position: fixed; top: -150px; left: -100px; z-index: 0;
      width: 600px; height: 600px; border-radius: 50%;
      background: radial-gradient(circle, rgba(0,212,255,.08) 0%, transparent 70%);
      pointer-events: none;
    }
    .glow-2 {
      position: fixed; bottom: -150px; right: -100px; z-index: 0;
      width: 500px; height: 500px; border-radius: 50%;
      background: radial-gradient(circle, rgba(0,151,178,.06) 0%, transparent 70%);
      pointer-events: none;
    }

    .login-container {
      position: relative; z-index: 1;
      width: 100%; max-width: 440px;
      padding: 1rem;
      animation: fadeUp .5s ease both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .login-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 2.5rem 2.2rem;
      box-shadow: 0 32px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(0,212,255,.05);
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-logo {
      display: inline-flex; align-items: center; justify-content: center;
      width: 64px; height: 64px;
      background: rgba(0,212,255,.1);
      border: 1px solid rgba(0,212,255,.25);
      border-radius: 16px;
      margin-bottom: 1.2rem;
    }

    .login-logo svg {
      width: 28px; height: 28px;
      stroke: var(--accent); stroke-width: 1.75; fill: none;
    }

    .login-title {
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem; font-weight: 800;
      margin-bottom: .3rem;
    }

    .login-subtitle {
      color: var(--muted); font-size: .875rem;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      font-size: .82rem; font-weight: 500; color: var(--muted);
      margin-bottom: .45rem;
      text-transform: uppercase; letter-spacing: .06em;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap svg {
      position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
      width: 17px; height: 17px;
      stroke: var(--muted); stroke-width: 1.75; fill: none;
      pointer-events: none;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 10px;
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: .95rem;
      padding: .75rem .95rem .75rem 2.6rem;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }

    input:focus {
      border-color: var(--accent2);
      box-shadow: 0 0 0 3px rgba(0,212,255,.1);
    }

    .toggle-pw {
      position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer; padding: .2rem;
      color: var(--muted);
      display: flex; align-items: center;
    }

    .toggle-pw svg {
      width: 17px; height: 17px;
      stroke: currentColor; stroke-width: 1.75; fill: none;
      position: static; transform: none;
    }

    .btn-login {
      width: 100%; margin-top: .4rem;
      background: var(--accent); color: #000;
      font-family: 'Syne', sans-serif; font-weight: 700;
      font-size: 1rem; letter-spacing: .02em;
      padding: .85rem;
      border: none; border-radius: 10px;
      cursor: pointer;
      transition: background .2s, transform .15s, box-shadow .2s;
      display: flex; align-items: center; justify-content: center; gap: .5rem;
    }

    .btn-login:hover {
      background: #33ddff;
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(0,212,255,.3);
    }

    .btn-login:active { transform: translateY(0); }

    .btn-login svg {
      width: 18px; height: 18px;
      stroke: currentColor; stroke-width: 2.5; fill: none;
    }

    .alert {
      border-radius: 10px;
      padding: .85rem 1rem;
      font-size: .875rem;
      margin-bottom: 1.2rem;
      display: flex; align-items: center; gap: .6rem;
    }

    .alert svg {
      width: 17px; height: 17px; flex-shrink: 0;
      stroke: currentColor; stroke-width: 2; fill: none;
    }

    .alert-error {
      background: rgba(239,68,68,.1);
      border: 1px solid rgba(239,68,68,.3);
      color: #fca5a5;
    }

    .alert-warning {
      background: rgba(245,158,11,.1);
      border: 1px solid rgba(245,158,11,.3);
      color: #fcd34d;
    }

    .login-footer {
      margin-top: 1.5rem;
      text-align: center;
      color: var(--muted); font-size: .78rem;
    }

    .login-footer a {
      color: var(--accent); text-decoration: none;
    }

    .back-to-site {
      display: inline-flex; align-items: center; gap: .4rem;
      margin-top: 1.2rem;
      color: var(--muted); font-size: .82rem;
      text-decoration: none;
      transition: color .2s;
    }

    .back-to-site:hover { color: var(--accent); }

    .back-to-site svg {
      width: 14px; height: 14px;
      stroke: currentColor; stroke-width: 2; fill: none;
    }
  </style>
</head>
<body>
  <div class="glow-1"></div>
  <div class="glow-2"></div>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="login-logo">
          <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1 class="login-title">Klivra CMS</h1>
        <p class="login-subtitle">Dashboard Admin — Area Terbatas</p>
      </div>

      <?php if ($timeout): ?>
        <div class="alert alert-warning">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Sesi Anda telah habis. Silakan login kembali.
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <?= clean($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input
              type="text" id="username" name="username"
              placeholder="Masukkan username"
              value="<?= clean($_POST['username'] ?? '') ?>"
              autocomplete="username"
              required
            >
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input
              type="password" id="password" name="password"
              placeholder="Masukkan password"
              autocomplete="current-password"
              required
            >
            <button type="button" class="toggle-pw" onclick="togglePassword()" title="Tampilkan/sembunyikan password">
              <svg id="eye-icon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login">
          <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Masuk ke Dashboard
        </button>
      </form>

      <div class="login-footer">
        <a href="../index.php" class="back-to-site">
          <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          Kembali ke Landing Page
        </a>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('eye-icon');
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      icon.innerHTML = isPass
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
  </script>
</body>
</html>
