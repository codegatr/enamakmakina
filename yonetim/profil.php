<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Profilim';
$hata = '';
$basarili = '';

$stmt = $pdo->prepare("SELECT * FROM yoneticiler WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$benim = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ad_soyad = trim($_POST['ad_soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $eski_sifre = $_POST['eski_sifre'] ?? '';
        $yeni_sifre = $_POST['yeni_sifre'] ?? '';
        $yeni_sifre_tekrar = $_POST['yeni_sifre_tekrar'] ?? '';

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $hata = 'Geçersiz e-posta.';
        else {
            if ($yeni_sifre) {
                if (!$eski_sifre || !password_verify($eski_sifre, $benim['sifre'])) {
                    $hata = 'Mevcut şifre yanlış.';
                } elseif (strlen($yeni_sifre) < 8) {
                    $hata = 'Yeni şifre en az 8 karakter olmalı.';
                } elseif ($yeni_sifre !== $yeni_sifre_tekrar) {
                    $hata = 'Yeni şifreler eşleşmiyor.';
                } else {
                    $hash = password_hash($yeni_sifre, PASSWORD_BCRYPT);
                    $pdo->prepare("UPDATE yoneticiler SET ad_soyad=?, email=?, sifre=? WHERE id=?")
                        ->execute([$ad_soyad, $email, $hash, $benim['id']]);
                    $_SESSION['admin_ad_soyad'] = $ad_soyad;
                    $_SESSION['admin_email'] = $email;
                    denetim_kaydet('profil_sifre_degistirildi', 'yoneticiler', $benim['id']);
                    $basarili = 'Profil ve şifre güncellendi.';
                }
            } else {
                $pdo->prepare("UPDATE yoneticiler SET ad_soyad=?, email=? WHERE id=?")
                    ->execute([$ad_soyad, $email, $benim['id']]);
                $_SESSION['admin_ad_soyad'] = $ad_soyad;
                $_SESSION['admin_email'] = $email;
                denetim_kaydet('profil_guncellendi', 'yoneticiler', $benim['id']);
                $basarili = 'Profil güncellendi.';
            }
            if (!$hata) {
                $stmt = $pdo->prepare("SELECT * FROM yoneticiler WHERE id = ?");
                $stmt->execute([$benim['id']]);
                $benim = $stmt->fetch();
            }
        }
    }
}

include 'header.php';
?>
<?php if ($basarili): ?><div class="alert alert-success"><?= e($basarili) ?></div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<div class="form-card" style="max-width:600px; margin:0 auto;">
    <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px;">
        <span class="avatar" style="width:70px; height:70px; font-size:28px;"><?= e(mb_substr($benim['ad_soyad'] ?: $benim['kullanici_adi'], 0, 1)) ?></span>
        <div>
            <h3 style="color:#fff; font-size:18px;"><?= e($benim['ad_soyad'] ?: $benim['kullanici_adi']) ?></h3>
            <p style="color:var(--text-2); font-size:13px;">@<?= e($benim['kullanici_adi']) ?> · <?= e($benim['rol']) ?></p>
        </div>
    </div>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <h3 style="color:#fff; font-size:15px; margin-bottom:14px;">Bilgilerim</h3>
        <div class="form-group"><label>Ad Soyad</label><input type="text" name="ad_soyad" class="form-control" value="<?= e($benim['ad_soyad']) ?>"></div>
        <div class="form-group"><label>E-Posta</label><input type="email" name="email" class="form-control" required value="<?= e($benim['email']) ?>"></div>

        <h3 style="color:#fff; font-size:15px; margin:24px 0 14px;">Şifre Değiştir</h3>
        <p style="color:var(--text-3); font-size:13px; margin-bottom:14px;">Şifrenizi değiştirmek istemiyorsanız, boş bırakın.</p>
        <div class="form-group"><label>Mevcut Şifre</label><input type="password" name="eski_sifre" class="form-control" autocomplete="off"></div>
        <div class="form-grid-2">
            <div class="form-group"><label>Yeni Şifre</label><input type="password" name="yeni_sifre" class="form-control" autocomplete="new-password" minlength="8"></div>
            <div class="form-group"><label>Yeni Şifre (Tekrar)</label><input type="password" name="yeni_sifre_tekrar" class="form-control" autocomplete="new-password" minlength="8"></div>
        </div>

        <div class="form-actions"><button class="btn btn-primary">Kaydet</button></div>
    </form>

    <div style="margin-top:24px; padding-top:24px; border-top:1px solid var(--border); font-size:12px; color:var(--text-3);">
        Son giriş: <?= $benim['son_giris'] ? tr_tarih($benim['son_giris'], true) : '—' ?><br>
        Son giriş IP: <?= e($benim['son_giris_ip'] ?: '—') ?>
    </div>
</div>

<?php include 'footer.php'; ?>
