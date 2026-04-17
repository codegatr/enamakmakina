<?php
require_once __DIR__ . '/functions.php';

$hata = '';
$basarili = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iletisim_gonder'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_dogrula($token)) {
        $hata = 'Güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.';
    } else {
        $ad_soyad = trim($_POST['ad_soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');
        $konu = trim($_POST['konu'] ?? '');
        $mesaj = trim($_POST['mesaj'] ?? '');
        $kvkk = isset($_POST['kvkk']);

        if ($ad_soyad === '' || $email === '' || $mesaj === '') {
            $hata = 'Lütfen zorunlu alanları doldurun.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $hata = 'Geçerli bir e-posta adresi giriniz.';
        } elseif (!$kvkk) {
            $hata = 'KVKK aydınlatma metnini onaylamalısınız.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO iletisim_mesajlari (ad_soyad, email, telefon, konu, mesaj, ip_adresi, tarayici, durum)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, 'yeni')");
            $stmt->execute([$ad_soyad, $email, $telefon, $konu, $mesaj, ip_adresi(), substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)]);

            // Admin mail
            $mail_konu = 'Yeni İletişim Mesajı: ' . ($konu ?: 'Konu Yok');
            $mail_body = "Ad Soyad: $ad_soyad\nE-posta: $email\nTelefon: $telefon\nKonu: $konu\n\nMesaj:\n$mesaj";
            @mail_gonder(ayar('email'), $mail_konu, $mail_body);

            $basarili = true;
        }
    }
}

$sayfa_baslik = 'İletişim - ' . ayar('site_baslik');
$sayfa_aciklama = 'Enamak Makina iletişim bilgileri, adres, telefon ve iletişim formu.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['İletişim', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">İletişim</h1>
        <p class="page-desc">Sorularınız, talepleriniz ve iş birlikleri için bize ulaşın.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <div class="iletisim-grid">
            <!-- Form -->
            <div class="iletisim-form-wrap">
                <h2>Bize Mesaj Gönderin</h2>
                <p class="form-desc">Formu doldurun, en kısa sürede size dönelim.</p>

                <?php if ($basarili): ?>
                    <div class="alert alert-success">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12A10 10 0 1 1 16.18 4"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Mesajınız başarıyla gönderildi. En kısa sürede dönüş yapacağız.
                    </div>
                <?php elseif ($hata): ?>
                    <div class="alert alert-danger"><?= e($hata) ?></div>
                <?php endif; ?>

                <form method="post" action="iletisim.php" class="form-wrap">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="iletisim_gonder" value="1">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Ad Soyad <span class="zorunlu">*</span></label>
                            <input type="text" name="ad_soyad" class="form-control" required value="<?= e($_POST['ad_soyad'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>E-Posta <span class="zorunlu">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Telefon</label>
                            <input type="tel" name="telefon" class="form-control" value="<?= e($_POST['telefon'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Konu</label>
                            <input type="text" name="konu" class="form-control" value="<?= e($_POST['konu'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mesajınız <span class="zorunlu">*</span></label>
                        <textarea name="mesaj" class="form-control" rows="6" required><?= e($_POST['mesaj'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group form-check">
                        <label>
                            <input type="checkbox" name="kvkk" value="1" required>
                            <span><a href="sayfa.php?slug=kvkk" target="_blank">KVKK Aydınlatma Metni</a>'ni okudum, onaylıyorum.</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        Mesajı Gönder
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                    </button>
                </form>
            </div>

            <!-- Bilgiler -->
            <div class="iletisim-bilgi">
                <h2>İletişim Bilgileri</h2>

                <div class="bilgi-kutu">
                    <div class="bilgi-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <h4>Adres</h4>
                        <p><?= nl2br(e(ayar('adres'))) ?></p>
                    </div>
                </div>

                <div class="bilgi-kutu">
                    <div class="bilgi-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92V19.92C22 20.47 21.55 20.92 21 20.92H20C10.06 20.92 2 12.86 2 3V2C2 1.45 2.45 1 3 1H6C6.55 1 7 1.45 7 2V6.5C7 7.05 6.55 7.5 6 7.5H4.5C5.94 11.91 8.09 14.06 12.5 15.5V14C12.5 13.45 12.95 13 13.5 13H18C18.55 13 19 13.45 19 14V17"/></svg>
                    </div>
                    <div>
                        <h4>Telefon</h4>
                        <p>
                            <a href="tel:<?= e(str_replace(' ', '', ayar('telefon'))) ?>"><?= e(ayar('telefon')) ?></a>
                            <?php if (ayar('telefon_2')): ?>
                                <br><a href="tel:<?= e(str_replace(' ', '', ayar('telefon_2'))) ?>"><?= e(ayar('telefon_2')) ?></a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="bilgi-kutu">
                    <div class="bilgi-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <h4>E-Posta</h4>
                        <p>
                            <a href="mailto:<?= e(ayar('email')) ?>"><?= e(ayar('email')) ?></a>
                        </p>
                    </div>
                </div>

                <div class="bilgi-kutu">
                    <div class="bilgi-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <h4>Çalışma Saatleri</h4>
                        <p>Pazartesi - Cumartesi: 08:30 - 18:30<br>Pazar: Kapalı</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Harita -->
<?php $harita = ayar('harita_iframe'); if ($harita): ?>
<section class="harita-section">
    <div class="harita-wrap">
        <?= $harita ?>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
