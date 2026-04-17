<?php
require_once __DIR__ . '/functions.php';

$sayfa_baslik = ayar('site_baslik') . ' - ' . ayar('site_slogan', 'Kumlama Makinası İmalat ve Bakım');
$sayfa_aciklama = ayar('site_aciklama');
$sayfa_anahtar = ayar('site_anahtar');
$canonical = SITE_URL;

// Slider
$slider = $pdo->query("SELECT * FROM slider WHERE aktif = 1 ORDER BY sira ASC, id ASC")->fetchAll();

// Öne çıkan ürünler
$urunler = $pdo->query("SELECT u.*, k.ad AS kategori_ad, k.slug AS kategori_slug
                        FROM urunler u
                        LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id
                        WHERE u.aktif = 1 AND u.one_cikan = 1
                        ORDER BY u.sira ASC, u.id DESC LIMIT 6")->fetchAll();

// Ürün kategorileri
$kategoriler = $pdo->query("SELECT * FROM urun_kategoriler WHERE aktif = 1 ORDER BY sira ASC, id ASC LIMIT 8")->fetchAll();

// Hizmetler
$hizmetler = $pdo->query("SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY sira ASC, id ASC LIMIT 6")->fetchAll();

// Blog yazıları
$bloglar = $pdo->query("SELECT * FROM bloglar WHERE aktif = 1 ORDER BY yayin_tarihi DESC, id DESC LIMIT 3")->fetchAll();

// Referanslar
$referanslar = $pdo->query("SELECT * FROM referanslar WHERE aktif = 1 ORDER BY sira ASC, id ASC LIMIT 12")->fetchAll();

include 'header.php';
?>

<!-- HERO SLIDER -->
<?php if ($slider): ?>
<section class="hero-slider" aria-label="Ana Sayfa Slider">
    <div class="slider-wrap">
        <?php foreach ($slider as $i => $s): ?>
        <div class="slide <?= $i === 0 ? 'active' : '' ?>" <?= $s['gorsel'] ? 'style="background-image: url(\'' . e(resim_url($s['gorsel'])) . '\')"' : '' ?>>
            <div class="slide-overlay"></div>
            <div class="container slide-inner">
                <div class="slide-content">
                    <?php if (!empty($s['ust_baslik'])): ?>
                        <span class="slide-eyebrow"><?= e($s['ust_baslik']) ?></span>
                    <?php endif; ?>
                    <h1 class="slide-title"><?= e($s['baslik']) ?></h1>
                    <?php if (!empty($s['aciklama'])): ?>
                        <p class="slide-desc"><?= e($s['aciklama']) ?></p>
                    <?php endif; ?>
                    <div class="slide-actions">
                        <?php if (!empty($s['buton_link'])): ?>
                            <a href="<?= e($s['buton_link']) ?>" class="btn btn-primary btn-lg"><?= e($s['buton_metin'] ?: 'Detaylı Bilgi') ?></a>
                        <?php endif; ?>
                        <a href="teklif-al.php" class="btn btn-outline btn-lg">Teklif Al</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($slider) > 1): ?>
    <button class="slider-btn prev" aria-label="Önceki" onclick="sliderPrev()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button class="slider-btn next" aria-label="Sonraki" onclick="sliderNext()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <div class="slider-dots">
        <?php foreach ($slider as $i => $s): ?>
            <button class="slider-dot <?= $i === 0 ? 'active' : '' ?>" onclick="sliderGo(<?= $i ?>)" aria-label="Slayt <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- ÖZELLİKLER -->
<section class="section ozellikler-section">
    <div class="container">
        <div class="ozellikler-grid">
            <div class="ozellik">
                <div class="ozellik-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7L12 12L22 7L12 2Z"/><path d="M2 17L12 22L22 17"/><path d="M2 12L12 17L22 12"/></svg>
                </div>
                <div class="ozellik-text">
                    <h3>Özel İmalat</h3>
                    <p>Her sektöre özel projelendirme ve anahtar teslim üretim</p>
                </div>
            </div>
            <div class="ozellik">
                <div class="ozellik-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12L11 14L15 10"/><path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z"/></svg>
                </div>
                <div class="ozellik-text">
                    <h3>2 Yıl Garanti</h3>
                    <p>Tüm makinalarımız 2 yıl garanti ve ömür boyu yedek parça desteği</p>
                </div>
            </div>
            <div class="ozellik">
                <div class="ozellik-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="ozellik-text">
                    <h3>Hızlı Teslimat</h3>
                    <p>Standart modellerde 30-45 gün, özel projelerde 60-90 gün teslim</p>
                </div>
            </div>
            <div class="ozellik">
                <div class="ozellik-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="ozellik-text">
                    <h3>Saha Servisi</h3>
                    <p>Türkiye'nin her noktasına mobil servis ekibi ve bakım desteği</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ÜRÜN KATEGORİLERİ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Ürün Grupları</span>
            <h2 class="section-title">Kumlama Makinası Çeşitlerimiz</h2>
            <p class="section-desc">İhtiyacınıza göre tasarlanan farklı tip ve kapasitedeki kumlama makinalarımızla üretim süreçlerinizi güçlendirin.</p>
        </div>

        <div class="kategori-grid">
            <?php foreach ($kategoriler as $k): ?>
            <a href="urunler.php?kategori=<?= e($k['slug']) ?>" class="kategori-card fade-in">
                <div class="kategori-img">
                    <img src="<?= e(resim_url($k['gorsel'])) ?>" alt="<?= e($k['ad']) ?>" loading="lazy">
                </div>
                <div class="kategori-body">
                    <h3 class="kategori-title"><?= e($k['ad']) ?></h3>
                    <?php if (!empty($k['aciklama'])): ?>
                        <p class="kategori-desc"><?= e(kisalt($k['aciklama'], 90)) ?></p>
                    <?php endif; ?>
                    <span class="kategori-link">
                        Detaylı İncele
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ÖNE ÇIKAN ÜRÜNLER -->
<?php if ($urunler): ?>
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Öne Çıkan Modeller</span>
            <h2 class="section-title">En Çok Tercih Edilen Makinalarımız</h2>
        </div>

        <div class="urun-grid">
            <?php foreach ($urunler as $u): ?>
            <article class="urun-card fade-in">
                <a href="urun.php?slug=<?= e($u['slug']) ?>" class="urun-img-link">
                    <div class="urun-img">
                        <img src="<?= e(resim_url($u['gorsel'])) ?>" alt="<?= e($u['ad']) ?>" loading="lazy">
                    </div>
                </a>
                <div class="urun-body">
                    <?php if (!empty($u['kategori_ad'])): ?>
                        <span class="urun-kategori"><?= e($u['kategori_ad']) ?></span>
                    <?php endif; ?>
                    <h3 class="urun-title">
                        <a href="urun.php?slug=<?= e($u['slug']) ?>"><?= e($u['ad']) ?></a>
                    </h3>
                    <?php if (!empty($u['kisa_aciklama'])): ?>
                        <p class="urun-desc"><?= e(kisalt($u['kisa_aciklama'], 110)) ?></p>
                    <?php endif; ?>
                    <div class="urun-actions">
                        <a href="urun.php?slug=<?= e($u['slug']) ?>" class="btn btn-sm btn-outline">İncele</a>
                        <a href="teklif-al.php?urun=<?= (int)$u['id'] ?>" class="btn btn-sm btn-primary">Teklif Al</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="section-footer">
            <a href="urunler.php" class="btn btn-outline btn-lg">Tüm Ürünleri Görüntüle</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- HİZMETLER -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Hizmetlerimiz</span>
            <h2 class="section-title">Uçtan Uca Hizmet Anlayışı</h2>
            <p class="section-desc">İmalattan bakıma, yedek parçadan saha servisine kadar kumlama teknolojisinin her aşamasında yanınızdayız.</p>
        </div>

        <div class="hizmet-grid">
            <?php foreach ($hizmetler as $h): ?>
            <a href="hizmet.php?slug=<?= e($h['slug']) ?>" class="hizmet-card fade-in">
                <div class="hizmet-icon">
                    <?php if (!empty($h['ikon'])): ?>
                        <?= $h['ikon'] ?>
                    <?php else: ?>
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3A1 1 0 0 0 15 7.6L18.4 11A1 1 0 0 0 19.7 11.3L22 9L15 2L12.7 4.3A1 1 0 0 0 13 5.6L14.7 6.3Z"/><path d="M7 17L2 22"/><path d="M10.5 14.5L3.5 21.5"/></svg>
                    <?php endif; ?>
                </div>
                <h3 class="hizmet-title"><?= e($h['ad']) ?></h3>
                <?php if (!empty($h['kisa_aciklama'])): ?>
                    <p class="hizmet-desc"><?= e(kisalt($h['kisa_aciklama'], 120)) ?></p>
                <?php endif; ?>
                <span class="hizmet-link">
                    Daha Fazla
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div class="cta-text">
                <h2>Projelerinize Özel Kumlama Çözümleri</h2>
                <p>Üretim hattınız için en uygun makinayı birlikte seçelim. Uzman ekibimiz ihtiyaç analizi, projelendirme ve anahtar teslim kurulum sunar.</p>
            </div>
            <div class="cta-actions">
                <a href="teklif-al.php" class="btn btn-primary btn-lg">Ücretsiz Teklif Al</a>
                <a href="tel:<?= e(str_replace(' ', '', ayar('telefon'))) ?>" class="btn btn-outline btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92V19.92C22 20.47 21.55 20.92 21 20.92H20C10.06 20.92 2 12.86 2 3V2C2 1.45 2.45 1 3 1H6C6.55 1 7 1.45 7 2V6.5C7 7.05 6.55 7.5 6 7.5H4.5C5.94 11.91 8.09 14.06 12.5 15.5V14C12.5 13.45 12.95 13 13.5 13H18C18.55 13 19 13.45 19 14V17C22 17 22 16.92 22 16.92Z"/></svg>
                    <?= e(ayar('telefon')) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- REFERANSLAR -->
<?php if ($referanslar): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Referanslarımız</span>
            <h2 class="section-title">Güvenen Firmalar</h2>
        </div>
        <div class="referans-grid">
            <?php foreach ($referanslar as $r): ?>
                <div class="referans-item" title="<?= e($r['firma_adi']) ?>">
                    <img src="<?= e(resim_url($r['logo'], 'assets/img/placeholder-logo.svg')) ?>" alt="<?= e($r['firma_adi']) ?>" loading="lazy">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- BLOG -->
<?php if ($bloglar): ?>
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Blog & Haberler</span>
            <h2 class="section-title">Sektörden Haberler ve Teknik Yazılar</h2>
        </div>

        <div class="blog-grid">
            <?php foreach ($bloglar as $b): ?>
            <article class="blog-card fade-in">
                <a href="blog-detay.php?slug=<?= e($b['slug']) ?>" class="blog-img-link">
                    <div class="blog-img">
                        <img src="<?= e(resim_url($b['gorsel'])) ?>" alt="<?= e($b['baslik']) ?>" loading="lazy">
                    </div>
                </a>
                <div class="blog-body">
                    <div class="blog-meta">
                        <span><?= tr_tarih($b['yayin_tarihi'] ?: $b['olusturma_tarihi']) ?></span>
                        <?php if (!empty($b['kategori'])): ?>
                            <span>• <?= e($b['kategori']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="blog-title">
                        <a href="blog-detay.php?slug=<?= e($b['slug']) ?>"><?= e($b['baslik']) ?></a>
                    </h3>
                    <?php if (!empty($b['ozet'])): ?>
                        <p class="blog-desc"><?= e(kisalt($b['ozet'], 140)) ?></p>
                    <?php endif; ?>
                    <a href="blog-detay.php?slug=<?= e($b['slug']) ?>" class="blog-link">
                        Devamını Oku
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="section-footer">
            <a href="blog.php" class="btn btn-outline btn-lg">Tüm Yazılar</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
