<?php
$hash = password_hash('pendongjansen', PASSWORD_BCRYPT, ['cost' => 12]);
echo "Hash baru: " . $hash . "<br><br>";

// Koneksi database
$pdo = new PDO('mysql:host=localhost;dbname=klivra_cms;charset=utf8mb4', 'root', '');
$stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'superadmin-klivra'");
$stmt->execute([$hash]);

if ($stmt->rowCount() > 0) {
    echo "✅ Password berhasil direset!<br>";
    echo "Username: superadmin-klivra<br>";
    echo "Password: pendongjansen<br><br>";
    echo "<a href='admin/login.php'>→ Klik di sini untuk Login</a>";
} else {
    echo "❌ Gagal. Coba cek apakah user 'superadmin-klivra' ada di database.";
    
    // Coba insert jika belum ada
    $hash2 = password_hash('pendongjansen', PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt2 = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES ('superadmin-klivra', ?) ON DUPLICATE KEY UPDATE password = ?");
    $stmt2->execute([$hash2, $hash2]);
    echo "<br>✅ User sudah dibuat ulang. <a href='admin/login.php'>→ Coba Login</a>";
}
?>
```