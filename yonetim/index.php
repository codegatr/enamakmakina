<?php
require_once __DIR__ . '/../functions.php';

// Zaten giriş yaptıysa panele yönlendir
if (!empty($_SESSION['admin_id'])) {
    header('Location: panel.php');
    exit;
}

$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici = trim($_POST['kullanici'] ?? '');
    $sifre = $_POST['sifre'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    // Brute force koruması
    $ip = ip_adresi();
    $_SESSION['giris_deneme'] = $_SESSION['giris_deneme'] ?? [];
    $_SESSION['giris_deneme'][$ip] = $_SESSION['giris_deneme'][$ip] ?? ['sayi' => 0, 'son' => 0];
    $deneme = &$_SESSION['giris_deneme'][$ip];

    if ($deneme['sayi'] >= 5 && (time() - $deneme['son']) < 300) {
        $hata = 'Çok fazla başarısız deneme. Lütfen 5 dakika bekleyin.';
    } elseif (!csrf_dogrula($token)) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } elseif (!$kullanici || !$sifre) {
        $hata = 'Kullanıcı adı ve şifre gerekli.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM yoneticiler WHERE (kullanici_adi = ? OR email = ?) AND aktif = 1 LIMIT 1");
        $stmt->execute([$kullanici, $kullanici]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($sifre, $admin['sifre'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_kullanici'] = $admin['kullanici_adi'];
            $_SESSION['admin_ad_soyad'] = $admin['ad_soyad'];
            $_SESSION['admin_rol'] = $admin['rol'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_giris_zaman'] = time();
            $_SESSION['admin_ip'] = ip_adresi();

            $pdo->prepare("UPDATE yoneticiler SET son_giris = NOW(), son_giris_ip = ? WHERE id = ?")
                ->execute([ip_adresi(), $admin['id']]);

            denetim_kaydet('giris_yapildi');
            unset($_SESSION['giris_deneme'][$ip]);

            header('Location: panel.php');
            exit;
        } else {
            $deneme['sayi']++;
            $deneme['son'] = time();
            $hata = 'Kullanıcı adı veya şifre hatalı.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yönetim Paneli - <?= e(ayar('site_baslik', 'Enamak Makina')) ?></title>
<link rel="stylesheet" href="assets/admin.css">
<link rel="icon" href="../assets/img/favicon.svg" type="image/svg+xml">
</head>
<body class="login-body">
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <h1>Enamak <span>Makina</span></h1>
            <p>Yönetim Paneli</p>
        </div>

        <?php if ($hata): ?>
            <div class="alert alert-danger"><?= e($hata) ?></div>
        <?php endif; ?>

        <form method="post" action="" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-group">
                <label>Kullanıcı Adı veya E-Posta</label>
                <input type="text" name="kullanici" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="sifre" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
        </form>

        <div class="login-footer">
            <a href="../index.php">← Siteye Dön</a>
        </div>
    </div>
</div>
</body>
</html>
