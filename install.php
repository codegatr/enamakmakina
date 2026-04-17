<?php
/**
 * Enamak Makina - İlk Kurulum Sihirbazı
 * GÜVENLİK: Kurulum bittikten sonra BU DOSYAYI SİLİN veya uzantısını değiştirin.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$adim = (int)($_GET['adim'] ?? 1);
$hata = '';
$mesaj = '';

// Kurulum tamamlandıysa
if (file_exists(__DIR__ . '/.installed')) {
    $adim = 99;
}

// Adim 2: DB config kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adim']) && $_POST['adim'] == 2) {
    $db_host = trim($_POST['db_host'] ?? 'localhost');
    $db_name = trim($_POST['db_name'] ?? '');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = $_POST['db_pass'] ?? '';
    $site_url = rtrim(trim($_POST['site_url'] ?? ''), '/');

    if (!$db_name || !$db_user || !$site_url) {
        $hata = 'Lütfen tüm alanları doldurun.';
        $adim = 2;
    } else {
        // Bağlantıyı test et
        try {
            $test = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $test->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $_SESSION['install_db'] = compact('db_host', 'db_name', 'db_user', 'db_pass', 'site_url');
            header("Location: install.php?adim=3");
            exit;
        } catch (Exception $e) {
            $hata = 'Bağlantı hatası: ' . $e->getMessage();
            $adim = 2;
        }
    }
}

// Adim 3: SQL import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adim']) && $_POST['adim'] == 3) {
    if (empty($_SESSION['install_db'])) {
        header("Location: install.php?adim=2");
        exit;
    }
    $db = $_SESSION['install_db'];
    try {
        $pdo = new PDO("mysql:host={$db['db_host']};dbname={$db['db_name']};charset=utf8mb4", $db['db_user'], $db['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents(__DIR__ . '/kurulum.sql');
        if (!$sql) throw new Exception('kurulum.sql dosyası bulunamadı.');

        // Multi-statement execute (single exec with full content)
        $pdo->exec($sql);

        $mesaj = 'Veritabanı başarıyla kuruldu.';
        $adim = 4;
    } catch (Exception $e) {
        $hata = 'SQL Hatası: ' . $e->getMessage();
        $adim = 3;
    }
}

// Adim 4: Admin şifresi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adim']) && $_POST['adim'] == 4) {
    if (empty($_SESSION['install_db'])) {
        header("Location: install.php?adim=2");
        exit;
    }
    $email = trim($_POST['admin_email'] ?? '');
    $kullanici = trim($_POST['admin_kullanici'] ?? 'admin');
    $sifre = $_POST['admin_sifre'] ?? '';
    $sifre_tekrar = $_POST['admin_sifre_tekrar'] ?? '';

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hata = 'Geçerli bir e-posta girin.';
        $adim = 4;
    } elseif (strlen($sifre) < 8) {
        $hata = 'Şifre en az 8 karakter olmalı.';
        $adim = 4;
    } elseif ($sifre !== $sifre_tekrar) {
        $hata = 'Şifreler eşleşmiyor.';
        $adim = 4;
    } else {
        try {
            $db = $_SESSION['install_db'];
            $pdo = new PDO("mysql:host={$db['db_host']};dbname={$db['db_name']};charset=utf8mb4", $db['db_user'], $db['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $hash = password_hash($sifre, PASSWORD_BCRYPT);

            // Var olan admini güncelle veya yenisini ekle
            $stmt = $pdo->prepare("SELECT id FROM yoneticiler WHERE kullanici_adi = ? OR email = ? LIMIT 1");
            $stmt->execute([$kullanici, $email]);
            $mevcut = $stmt->fetch();

            if ($mevcut) {
                $pdo->prepare("UPDATE yoneticiler SET kullanici_adi=?, email=?, sifre=?, rol='superadmin', aktif=1 WHERE id=?")
                    ->execute([$kullanici, $email, $hash, $mevcut['id']]);
            } else {
                $pdo->prepare("INSERT INTO yoneticiler (kullanici_adi, email, sifre, ad_soyad, rol, aktif) VALUES (?, ?, ?, 'Site Yöneticisi', 'superadmin', 1)")
                    ->execute([$kullanici, $email, $hash]);
            }

            // site_url ayarını güncelle
            $stmt = $pdo->prepare("INSERT INTO ayarlar (anahtar, deger, grup) VALUES ('site_url', ?, 'genel')
                                   ON DUPLICATE KEY UPDATE deger = VALUES(deger)");
            $stmt->execute([$db['site_url']]);

            // config.php yaz
            $config_tpl = '<?php
/**
 * Enamak Makina - Yapılandırma Dosyası
 * (install.php tarafından otomatik oluşturuldu)
 */

define(\'DB_HOST\', ' . var_export($db['db_host'], true) . ');
define(\'DB_NAME\', ' . var_export($db['db_name'], true) . ');
define(\'DB_USER\', ' . var_export($db['db_user'], true) . ');
define(\'DB_PASS\', ' . var_export($db['db_pass'], true) . ');
define(\'DB_CHARSET\', \'utf8mb4\');

define(\'SITE_URL\', ' . var_export($db['site_url'], true) . ');
define(\'SITE_PATH\', __DIR__);

define(\'DEBUG_MODE\', false);
define(\'SESSION_TIMEOUT\', 7200);
define(\'CSRF_SECRET\', ' . var_export(bin2hex(random_bytes(32)), true) . ');

define(\'GITHUB_REPO\', \'codegatr/enamakmakina\');
define(\'GITHUB_TOKEN\', \'\'); // İstenirse admin panel ayarlarından girilebilir

// Upload ayarları
define(\'UPLOAD_MAX\', 5 * 1024 * 1024); // 5MB
define(\'UPLOAD_ALLOWED\', [\'jpg\', \'jpeg\', \'png\', \'gif\', \'webp\', \'svg\']);

// Oturum güvenliği
ini_set(\'session.cookie_httponly\', 1);
ini_set(\'session.use_only_cookies\', 1);
ini_set(\'session.cookie_samesite\', \'Lax\');
if (!empty($_SERVER[\'HTTPS\'])) ini_set(\'session.cookie_secure\', 1);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set(\'display_errors\', 1);
} else {
    error_reporting(0);
    ini_set(\'display_errors\', 0);
}

date_default_timezone_set(\'Europe/Istanbul\');
mb_internal_encoding(\'UTF-8\');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        \'mysql:host=\' . DB_HOST . \';dbname=\' . DB_NAME . \';charset=\' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        die(\'DB Error: \' . $e->getMessage());
    }
    die(\'Veritabanı bağlantı hatası.\');
}
';

            file_put_contents(__DIR__ . '/config.php', $config_tpl);

            // Kurulum işareti
            file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s') . "\n");

            unset($_SESSION['install_db']);
            $adim = 5;
        } catch (Exception $e) {
            $hata = 'Hata: ' . $e->getMessage();
            $adim = 4;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enamak Makina - Kurulum</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#0a0b0d; color:#e5e7eb; min-height:100vh; padding:40px 20px; }
    .wrap { max-width: 640px; margin: 0 auto; }
    .card { background:#111317; border:1px solid #222; border-radius:16px; padding:40px; }
    h1 { font-size:28px; margin-bottom:10px; color:#fff; }
    h1 span { color:#ff6b1a; }
    .alt { color:#9ca3af; margin-bottom:30px; }
    .steps { display:flex; gap:10px; margin-bottom:30px; }
    .step { flex:1; text-align:center; padding:10px; background:#1a1d23; border-radius:8px; font-size:13px; color:#6b7280; }
    .step.active { background:#ff6b1a; color:#fff; font-weight:600; }
    .step.done { background:#16a34a; color:#fff; }
    label { display:block; margin-bottom:8px; color:#d1d5db; font-weight:500; }
    input, textarea { width:100%; padding:12px 14px; background:#0a0b0d; border:1px solid #374151; color:#fff; border-radius:8px; font-size:14px; margin-bottom:20px; }
    input:focus, textarea:focus { outline:none; border-color:#ff6b1a; }
    button, .btn { display:inline-block; padding:12px 24px; background:#ff6b1a; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:15px; font-weight:600; text-decoration:none; }
    button:hover, .btn:hover { background:#e55a0e; }
    .alert { padding:14px; border-radius:8px; margin-bottom:20px; }
    .alert-error { background: rgba(220, 38, 38, 0.1); border:1px solid #dc2626; color:#fca5a5; }
    .alert-success { background: rgba(22, 163, 74, 0.1); border:1px solid #16a34a; color:#86efac; }
    .info { background:#0a0b0d; border:1px solid #374151; padding:16px; border-radius:8px; margin-bottom:20px; font-size:14px; line-height:1.6; }
    .info strong { color:#ff6b1a; }
    code { background:#0a0b0d; padding:2px 6px; border-radius:4px; font-size:13px; color:#ff6b1a; }
    .row { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
    @media(max-width:600px){ .row { grid-template-columns: 1fr; } .steps { flex-wrap:wrap; } .step { min-width:45%; } }
</style>
</head>
<body>
<div class="wrap">
<div class="card">
    <h1>Enamak <span>Makina</span> Kurulum</h1>
    <p class="alt">Siteyi adım adım yapılandırın</p>

    <div class="steps">
        <div class="step <?= $adim == 1 ? 'active' : ($adim > 1 ? 'done' : '') ?>">1. Hoşgeldin</div>
        <div class="step <?= $adim == 2 ? 'active' : ($adim > 2 ? 'done' : '') ?>">2. Veritabanı</div>
        <div class="step <?= $adim == 3 ? 'active' : ($adim > 3 ? 'done' : '') ?>">3. Kurulum</div>
        <div class="step <?= $adim == 4 ? 'active' : ($adim > 4 ? 'done' : '') ?>">4. Yönetici</div>
        <div class="step <?= $adim == 5 ? 'active' : '' ?>">5. Tamam</div>
    </div>

    <?php if ($hata): ?><div class="alert alert-error"><?= htmlspecialchars($hata) ?></div><?php endif; ?>
    <?php if ($mesaj): ?><div class="alert alert-success"><?= htmlspecialchars($mesaj) ?></div><?php endif; ?>

    <?php if ($adim == 99): ?>
        <div class="alert alert-error">
            <strong>⚠️ Kurulum zaten yapılmış.</strong><br>
            Bu kurulum sihirbazını güvenlik açısından silmenizi öneririz.
        </div>
        <p>Yeniden kurmak istiyorsanız <code>.installed</code> dosyasını silin.</p>
        <br>
        <a href="index.php" class="btn">Siteye Git</a>
        <a href="yonetim/" class="btn" style="background:#374151;">Yönetim Paneli</a>

    <?php elseif ($adim == 1): ?>
        <div class="info">
            <strong>Başlamadan önce kontrol edin:</strong>
            <ul style="padding-left:20px; margin-top:10px;">
                <li>MySQL/MariaDB veritabanı oluşturuldu mu?</li>
                <li>Veritabanı kullanıcısına tam yetki verildi mi?</li>
                <li>PHP 8.0+ ve PDO uzantısı aktif mi?</li>
                <li>uploads/ klasörü yazılabilir mi (chmod 755)?</li>
            </ul>
        </div>
        <a href="install.php?adim=2" class="btn">Kuruluma Başla →</a>

    <?php elseif ($adim == 2): ?>
        <form method="post">
            <input type="hidden" name="adim" value="2">
            <div class="row">
                <div>
                    <label>Veritabanı Sunucusu</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>
                <div>
                    <label>Veritabanı Adı *</label>
                    <input type="text" name="db_name" required placeholder="enamak_db">
                </div>
            </div>
            <div class="row">
                <div>
                    <label>Kullanıcı Adı *</label>
                    <input type="text" name="db_user" required>
                </div>
                <div>
                    <label>Şifre</label>
                    <input type="password" name="db_pass">
                </div>
            </div>
            <label>Site URL'si *</label>
            <input type="url" name="site_url" value="https://<?= $_SERVER['HTTP_HOST'] ?? 'enamakmakina.com' ?>" required placeholder="https://enamakmakina.com">
            <button type="submit">Bağlan ve Devam Et →</button>
        </form>

    <?php elseif ($adim == 3): ?>
        <div class="info">
            Veritabanına <strong>kurulum.sql</strong> dosyası yüklenecek. Bu işlem:
            <ul style="padding-left:20px; margin-top:10px;">
                <li>16 tablo oluşturur</li>
                <li>Varsayılan ayarları ekler</li>
                <li>Örnek ürün kategorileri ve hizmetleri ekler</li>
            </ul>
        </div>
        <form method="post">
            <input type="hidden" name="adim" value="3">
            <button type="submit">Veritabanını Oluştur →</button>
        </form>

    <?php elseif ($adim == 4): ?>
        <form method="post">
            <input type="hidden" name="adim" value="4">
            <label>Yönetici Kullanıcı Adı</label>
            <input type="text" name="admin_kullanici" value="admin" required>
            <label>E-Posta Adresi *</label>
            <input type="email" name="admin_email" required placeholder="admin@enamakmakina.com">
            <div class="row">
                <div>
                    <label>Şifre (min 8 karakter) *</label>
                    <input type="password" name="admin_sifre" required minlength="8">
                </div>
                <div>
                    <label>Şifre Tekrar *</label>
                    <input type="password" name="admin_sifre_tekrar" required minlength="8">
                </div>
            </div>
            <button type="submit">Yöneticiyi Oluştur →</button>
        </form>

    <?php elseif ($adim == 5): ?>
        <div class="alert alert-success">
            <strong>🎉 Kurulum tamamlandı!</strong><br>
            Site başarıyla kuruldu. Şimdi yönetim paneline giriş yapabilirsiniz.
        </div>
        <div class="info">
            <strong>⚠️ GÜVENLİK UYARISI:</strong><br>
            <ol style="padding-left:20px; margin-top:10px;">
                <li><code>install.php</code> dosyasını FTP/SSH üzerinden silin veya yeniden adlandırın</li>
                <li><code>kurulum.sql</code> dosyasını silin</li>
                <li><code>uploads/</code> klasör izinlerini kontrol edin (755)</li>
            </ol>
        </div>
        <a href="yonetim/" class="btn">Yönetim Paneline Git</a>
        <a href="index.php" class="btn" style="background:#374151;">Siteye Git</a>
    <?php endif; ?>
</div>
</div>
</body>
</html>
