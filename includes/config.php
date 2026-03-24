<?php
// ── DATABASE CONFIGURATION ──
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Ganti dengan username MySQL Anda
define('DB_PASS', '');            // Ganti dengan password MySQL Anda
define('DB_NAME', 'klivra_cms');

// ── SESSION CONFIG ──
define('SESSION_NAME', 'klivra_admin_session');
define('SESSION_TIMEOUT', 3600); // 1 jam

// ── PATH CONFIG ──
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '../uploads/');
define('GALLERY_UPLOAD_PATH', __DIR__ . '/../uploads/gallery/');
define('PROFILE_UPLOAD_PATH', __DIR__ . '/../uploads/profile/');

// ── ALLOWED IMAGE TYPES ──
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// ── DATABASE CONNECTION ──
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:monospace;background:#1a0000;color:#ff6b6b;padding:2rem;border-radius:8px;margin:2rem;">
                <strong>Database Error:</strong><br>' . htmlspecialchars($e->getMessage()) . '<br><br>
                Pastikan MySQL berjalan dan konfigurasi di <code>includes/config.php</code> sudah benar.
            </div>');
        }
    }
    return $pdo;
}

// ── SESSION HELPER ──
function startAdminSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

// ── AUTH CHECK ──
function requireLogin(): void {
    startAdminSession();
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
    // Session timeout check
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// ── FLASH MESSAGES ──
function setFlash(string $type, string $message): void {
    startAdminSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    startAdminSession();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ── SETTINGS HELPER ──
function getSetting(string $key, string $default = ''): string {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? ($row['setting_value'] ?? $default) : $default;
}

// ── SANITIZE ──
function clean(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

// ── CSRF TOKEN ──
function csrfToken(): string {
    startAdminSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): bool {
    startAdminSession();
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
