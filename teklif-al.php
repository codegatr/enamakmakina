<?php
require_once __DIR__ . '/functions.php';

$hata = '';
$basarili = false;

// URL'den ürün seçildiyse
$secili_urun = null;
if (!empty($_GET['urun'])) {
    $stmt = $pdo->prepare("SELECT u.*, k.ad AS kategori_ad FROM urunler u LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id WHERE u.id = ? AND u.aktif = 1");
    $stmt->execute([(int)$_GET['urun']]);
    $secili_urun = $stmt->fetch();
}

$urunler = $pdo->query("SELECT u.id, u.ad, k.ad AS kategori_ad FROM urunler u LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id WHERE u.aktif = 1 ORDER BY k.sira, u.sira, u.ad")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teklif_gonder'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_dogrula($token)) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ad_soyad = trim($_POST['ad_soyad'] ?? '');
        $firma = trim($_POST['firma'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');
        $sehir = trim($_POST['sehir'] ?? '');
        $urun_id = !empty($_POST['urun_id']) ? (int)$_POST['urun_id'] : null;
        $parca_tipi = trim($_POST['parca_tipi'] ?? '');
        $parca_boyut = trim($_POST['parca_boyut'] ?? '');
        $gunluk_uretim = trim($_POST['gunluk_uretim'] ?? '');
        $mevcut_durum = trim($_POST['mevcut_durum'] ?? '');
        $mesaj = trim($_POST['mesaj'] ?? '');
        $kvkk = isset($_POST['kvkk']);

        if ($ad_soyad === '' || $email === '' || $telefon === '') {
            $hata = 'Lütfen zorunlu alanları doldurun.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $hata = 'Geçerli bir e-posta adresi giriniz.';
        } elseif (!$kvkk) {
            $hata = 'KVKK metnini onaylamalısınız.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO teklif_talepleri
                (ad_soyad, firma, email, telefon, sehir, urun_id, parca_tipi, parca_boyut, gunluk_uretim, mevcut_durum, mesaj, ip_adresi, durum)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'yeni')");
            $stmt->execute([$ad_soyad, $firma, $email, $telefon, $sehir, $urun_id, $parca_tipi, $parca_boyut, $gunluk_uretim, $mevcut_durum, $mesaj, ip_adresi()]);

            $urun_adi = '';
            if ($urun_id) {
                foreach ($urunler as $u) {
                    if ($u['id'] == $urun_id) { $urun_adi = $u['ad']; break; }
                }
            }
            $mail_konu = 'Yeni Teklif Talebi - ' . $ad_soyad;
            $mail_body = "Ad Soyad: $ad_soyad\nFirma: $firma\nE-posta: $email\nTelefon: $telefon\nŞehir: $sehir\nÜrün: $urun_adi\n\n"
                       . "Parça Tipi: $parca_tipi\nParça Boyutu: $parca_boyut\nGünlük Üretim: $gunluk_uretim\nMevcut Durum: $mevcut_durum\n\nMesaj:\n$mesaj";
            @mail_gonder(ayar('email'), $mail_konu, $mail_body);

            $basarili = true;
            // Seçili ürünü sıfırla ki form boş gelsin
            $_POST = [];
        }
    }
}

$sayfa_baslik = 'Teklif Al - ' . ayar('site_baslik');
$sayfa_aciklama = 'Kumlama makinası için ücretsiz teklif alın. İhtiyacınızı belirtin, size özel fiyat sunalım.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Teklif Al', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Ücretsiz Teklif Al</h1>
        <p class="page-desc">İhtiyacınıza en uygun kumlama makinası için formu doldurun, size özel teklif hazırlayalım.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <div class="teklif-wrap">
            <div class="teklif-form-col">
                <?php if ($basarili): ?>
                    <div class="alert alert-success">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12A10 10 0 1 1 16.18 4"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Teklif talebiniz başarıyla iletildi. Uzman ekibimiz en kısa sürede size dönüş yapacak.
                    </div>
                <?php elseif ($hata): ?>
                    <div class="alert alert-danger"><?= e($hata) ?></div>
                <?php endif; ?>

                <form method="post" action="teklif-al.php" class="form-wrap">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="teklif_gonder" value="1">

                    <h3 class="form-baslik">İletişim Bilgileri</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Ad Soyad <span class="zorunlu">*</span></label>
                            <input type="text" name="ad_soyad" class="form-control" required value="<?= e($_POST['ad_soyad'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Firma</label>
                            <input type="text" name="firma" class="form-control" value="<?= e($_POST['firma'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>E-Posta <span class="zorunlu">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Telefon <span class="zorunlu">*</span></label>
                            <input type="tel" name="telefon" class="form-control" required value="<?= e($_POST['telefon'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Şehir</label>
                        <input type="text" name="sehir" class="form-control" value="<?= e($_POST['sehir'] ?? '') ?>">
                    </div>

                    <h3 class="form-baslik">Teknik Bilgiler</h3>
                    <div class="form-group">
                        <label>İlgilendiğiniz Ürün</label>
                        <select name="urun_id" class="form-control">
                            <option value="">-- Ürün Seçin (Opsiyonel) --</option>
                            <?php foreach ($urunler as $u):
                                $sel = ($secili_urun && $secili_urun['id'] == $u['id']) || (!empty($_POST['urun_id']) && $_POST['urun_id'] == $u['id']);
                            ?>
                                <option value="<?= (int)$u['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                                    <?= e($u['ad']) ?><?= $u['kategori_ad'] ? ' (' . e($u['kategori_ad']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Parça / İş Tipi</label>
                            <input type="text" name="parca_tipi" class="form-control" placeholder="Örn: Sac parça, döküm, profil..." value="<?= e($_POST['parca_tipi'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Parça Boyutu</label>
                            <input type="text" name="parca_boyut" class="form-control" placeholder="Ölçüler (en x boy x yükseklik)" value="<?= e($_POST['parca_boyut'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Günlük Üretim Hedefi</label>
                            <input type="text" name="gunluk_uretim" class="form-control" placeholder="Örn: 500 parça/vardiya" value="<?= e($_POST['gunluk_uretim'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Mevcut Durum</label>
                            <select name="mevcut_durum" class="form-control">
                                <option value="">-- Seçiniz --</option>
                                <?php
                                $durumlar = ['Yeni Makina Alımı', 'Mevcut Makinayı Yenileme', 'Bakım/Servis', 'Yedek Parça', 'Fason Hizmet', 'Danışmanlık'];
                                $secili = $_POST['mevcut_durum'] ?? '';
                                foreach ($durumlar as $d):
                                ?>
                                    <option value="<?= e($d) ?>" <?= $secili === $d ? 'selected' : '' ?>><?= e($d) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ek Mesaj / Özel İstekler</label>
                        <textarea name="mesaj" class="form-control" rows="4" placeholder="Projeniz hakkında detay verir misiniz?"><?= e($_POST['mesaj'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group form-check">
                        <label>
                            <input type="checkbox" name="kvkk" value="1" required>
                            <span><a href="sayfa.php?slug=kvkk" target="_blank">KVKK Aydınlatma Metni</a>'ni okudum, onaylıyorum.</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        Teklif Talebini Gönder
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                    </button>
                </form>
            </div>

            <!-- Yan bilgi -->
            <div class="teklif-yan">
                <div class="teklif-neden">
                    <h3>Neden Enamak Makina?</h3>
                    <ul class="neden-liste">
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Özel projelendirme
                        </li>
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            CE belgeli üretim
                        </li>
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            2 yıl garanti
                        </li>
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Ömür boyu yedek parça
                        </li>
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Anahtar teslim kurulum
                        </li>
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Saha servisi & eğitim
                        </li>
                    </ul>
                </div>

                <div class="teklif-iletisim">
                    <h3>Hızlı İletişim</h3>
                    <p>Formu doldurmak yerine bizi direkt arayabilir veya WhatsApp'tan yazabilirsiniz.</p>
                    <a href="tel:<?= e(str_replace(' ', '', ayar('telefon'))) ?>" class="btn btn-outline btn-block">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92V19.92C22 20.47 21.55 20.92 21 20.92H20C10.06 20.92 2 12.86 2 3V2C2 1.45 2.45 1 3 1H6C6.55 1 7 1.45 7 2V6.5C7 7.05 6.55 7.5 6 7.5H4.5C5.94 11.91 8.09 14.06 12.5 15.5V14C12.5 13.45 12.95 13 13.5 13H18C18.55 13 19 13.45 19 14V17"/></svg>
                        <?= e(ayar('telefon')) ?>
                    </a>
                    <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', ayar('whatsapp', ayar('telefon')))) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-block">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.6 6.32C16.12 4.85 14.16 4.04 12.07 4.04C7.77 4.04 4.27 7.54 4.27 11.84C4.27 13.21 4.63 14.54 5.31 15.71L4.21 19.76L8.35 18.67C9.48 19.29 10.76 19.62 12.07 19.62C16.37 19.62 19.87 16.12 19.87 11.83C19.87 9.74 19.06 7.79 17.6 6.32Z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
