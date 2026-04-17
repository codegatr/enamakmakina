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
        $telefon_p = trim($_POST['telefon'] ?? '');
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
            $stmt->execute([$ad_soyad, $email, $telefon_p, $konu, $mesaj, ip_adresi(), substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)]);

            $mail_konu = 'Yeni İletişim Mesajı: ' . ($konu ?: 'Konu Yok');
            $mail_body = "Ad Soyad: $ad_soyad\nE-posta: $email\nTelefon: $telefon_p\nKonu: $konu\n\nMesaj:\n$mesaj";
            @mail_gonder(ayar('email'), $mail_konu, $mail_body);

            $basarili = true;
        }
    }
}

$sayfa_baslik = 'İletişim - ' . ayar('site_baslik');
$sayfa_aciklama = 'Enamak Makina iletişim bilgileri, adres, telefon ve iletişim formu. Konya merkez.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['İletişim', ''],
];

$telefon = ayar('telefon');
$telefon_link = preg_replace('/[^0-9+]/', '', $telefon);
$email_adr = ayar('email');
$whatsapp = ayar('whatsapp') ?: $telefon_link;

include 'header.php';
?>

<!-- HERO -->
<section class="iletisim-hero">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <div class="iletisim-hero-inner">
            <span class="section-eyebrow">İLETİŞİM</span>
            <h1 class="iletisim-hero-title">Bir projeniz mi var? <br>Hemen konuşalım.</h1>
            <p class="iletisim-hero-lead">
                Satış, teknik destek, yedek parça veya servis — en uygun kanaldan bize ulaşın. Mühendislik ekibimiz mesai saatlerinde 2 saat içinde, dışında ertesi iş günü dönüş yapar.
            </p>
        </div>
    </div>
</section>

<!-- HIZLI İLETİŞİM KARTLARI -->
<section class="section-tight">
    <div class="container">
        <div class="iletisim-kanal-grid">

            <a href="tel:<?= e($telefon_link) ?>" class="iletisim-kanal">
                <div class="iletisim-kanal-ikon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div class="iletisim-kanal-body">
                    <span class="iletisim-kanal-label">Telefon</span>
                    <strong class="iletisim-kanal-value"><?= e($telefon) ?></strong>
                    <span class="iletisim-kanal-hint">Pzt-Cmt 08:30-18:30</span>
                </div>
            </a>

            <a href="https://wa.me/<?= e(ltrim($whatsapp, '+')) ?>" target="_blank" rel="noopener" class="iletisim-kanal iletisim-kanal-whatsapp">
                <div class="iletisim-kanal-ikon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                </div>
                <div class="iletisim-kanal-body">
                    <span class="iletisim-kanal-label">WhatsApp</span>
                    <strong class="iletisim-kanal-value">Anında Yanıt</strong>
                    <span class="iletisim-kanal-hint">7/24 erişilebilir</span>
                </div>
            </a>

            <a href="mailto:<?= e($email_adr) ?>" class="iletisim-kanal">
                <div class="iletisim-kanal-ikon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div class="iletisim-kanal-body">
                    <span class="iletisim-kanal-label">E-posta</span>
                    <strong class="iletisim-kanal-value"><?= e($email_adr) ?></strong>
                    <span class="iletisim-kanal-hint">24 saat içinde yanıt</span>
                </div>
            </a>

            <a href="teklif-al.php" class="iletisim-kanal iletisim-kanal-primary">
                <div class="iletisim-kanal-ikon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div class="iletisim-kanal-body">
                    <span class="iletisim-kanal-label">Teklif Formu</span>
                    <strong class="iletisim-kanal-value">Detaylı Teklif</strong>
                    <span class="iletisim-kanal-hint">2 iş gününde fiyat</span>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- FORM + BİLGİLER -->
<section class="section">
    <div class="container">
        <div class="iletisim-grid">

            <!-- Form -->
            <div class="iletisim-form-wrap">
                <div class="iletisim-form-head">
                    <span class="section-eyebrow">MESAJ GÖNDERİN</span>
                    <h2>Detaylı görüşme için formu doldurun</h2>
                    <p>Proje detaylarını paylaşın, en uygun çözümü sunalım. Mesajınız direkt mühendislik ekibine ulaşır.</p>
                </div>

                <?php if ($basarili): ?>
                    <div class="alert alert-success">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12A10 10 0 1 1 16.18 4"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <div>
                            <strong>Mesajınız alındı!</strong><br>
                            En kısa sürede dönüş yapacağız. Acil durumlarda bize <a href="tel:<?= e($telefon_link) ?>">direkt ulaşabilirsiniz</a>.
                        </div>
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
                            <input type="text" name="ad_soyad" class="form-control" required value="<?= e($_POST['ad_soyad'] ?? '') ?>" placeholder="Adınız Soyadınız">
                        </div>
                        <div class="form-group">
                            <label>E-Posta <span class="zorunlu">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="ornek@firma.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Telefon</label>
                            <input type="tel" name="telefon" class="form-control" value="<?= e($_POST['telefon'] ?? '') ?>" placeholder="+90 5XX XXX XX XX">
                        </div>
                        <div class="form-group">
                            <label>Konu</label>
                            <input type="text" name="konu" class="form-control" value="<?= e($_POST['konu'] ?? $_GET['konu'] ?? '') ?>" placeholder="Teklif / Servis / Yedek Parça">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mesajınız <span class="zorunlu">*</span></label>
                        <textarea name="mesaj" class="form-control" rows="6" required placeholder="Proje detaylarınızı kısaca açıklayın..."><?= e($_POST['mesaj'] ?? '') ?></textarea>
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
            <aside class="iletisim-sidebar">

                <div class="iletisim-bilgi">
                    <h3>İletişim Bilgileri</h3>

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
                                <a href="tel:<?= e($telefon_link) ?>"><?= e($telefon) ?></a>
                                <?php if (ayar('telefon_2')): ?>
                                    <br><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', ayar('telefon_2'))) ?>"><?= e(ayar('telefon_2')) ?></a>
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
                            <p><a href="mailto:<?= e($email_adr) ?>"><?= e($email_adr) ?></a></p>
                        </div>
                    </div>

                    <div class="bilgi-kutu">
                        <div class="bilgi-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <h4>Çalışma Saatleri</h4>
                            <p>Pazartesi - Cumartesi<br><strong>08:30 - 18:30</strong><br>Pazar: Kapalı</p>
                        </div>
                    </div>
                </div>

                <!-- Departmanlar -->
                <div class="iletisim-bilgi">
                    <h3>Departmanlar</h3>
                    <div class="departman-liste">
                        <div class="departman-item">
                            <strong>Satış &amp; Teklif</strong>
                            <span>Yeni makine siparişi, fiyatlandırma, teslimat planı</span>
                            <a href="mailto:<?= e($email_adr) ?>"><?= e($email_adr) ?></a>
                        </div>
                        <div class="departman-item">
                            <strong>Teknik Servis</strong>
                            <span>Arıza, bakım anlaşması, saha ziyareti talebi</span>
                            <a href="tel:<?= e($telefon_link) ?>"><?= e($telefon) ?></a>
                        </div>
                        <div class="departman-item">
                            <strong>Yedek Parça</strong>
                            <span>Türbin, astar, filtre, aşındırıcı tedariği</span>
                            <a href="tel:<?= e($telefon_link) ?>"><?= e($telefon) ?></a>
                        </div>
                    </div>
                </div>

                <!-- Sosyal -->
                <?php
                $sosyal = array_filter([
                    'Facebook' => ayar('facebook'),
                    'Instagram' => ayar('instagram'),
                    'LinkedIn' => ayar('linkedin'),
                    'YouTube' => ayar('youtube'),
                ]);
                if ($sosyal):
                ?>
                <div class="iletisim-bilgi">
                    <h3>Sosyal Medya</h3>
                    <div class="sosyal-liste">
                        <?php foreach ($sosyal as $ad => $url): ?>
                            <a href="<?= e($url) ?>" target="_blank" rel="noopener"><?= e($ad) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </aside>
        </div>
    </div>
</section>

<!-- HARİTA -->
<?php $harita = ayar('harita_iframe'); if ($harita): ?>
<section class="harita-section">
    <div class="container">
        <div class="harita-head">
            <div>
                <span class="section-eyebrow">KONUM</span>
                <h2>Bizi ziyaret edin</h2>
                <p>Konya organize sanayi bölgesinde, ana otoyola 5 dakika mesafedeyiz. Randevu ile gelmeniz tavsiye edilir.</p>
            </div>
            <a href="https://www.google.com/maps/search/<?= urlencode(ayar('adres')) ?>" target="_blank" rel="noopener" class="btn btn-outline">
                Google Haritalar'da Aç
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M17 7H8M17 7V16"/></svg>
            </a>
        </div>
        <div class="harita-wrap">
            <?= $harita ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
