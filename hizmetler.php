<?php
require_once __DIR__ . '/functions.php';

$hizmetler = $pdo->query("SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY sira ASC, id ASC")->fetchAll();

$sayfa_baslik = 'Hizmetlerimiz - ' . ayar('site_baslik');
$sayfa_aciklama = 'Kumlama makinası imalatı, bakım, revizyon, yedek parça, saha servisi ve danışmanlık hizmetlerimiz. Konya merkezli, Türkiye geneli servis.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Hizmetler', ''],
];

include 'header.php';
?>

<!-- HERO -->
<section class="hizmet-hero">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <div class="hizmet-hero-inner">
            <span class="section-eyebrow">HİZMETLERİMİZ</span>
            <h1 class="hizmet-hero-title">Uçtan uca kumlama teknolojisi desteği</h1>
            <p class="hizmet-hero-lead">
                İmalattan devreye almaya, bakımdan yedek parçaya — kumlama sistemlerinin her aşamasında mühendis desteği. <?= count($hizmetler) ?> farklı hizmet kategorisi, 7/24 teknik servis.
            </p>
            <div class="hizmet-hero-stats">
                <div class="hh-stat">
                    <strong>24-48s</strong>
                    <span>Saha müdahale süresi</span>
                </div>
                <div class="hh-stat">
                    <strong>2 Yıl</strong>
                    <span>Üretici garantisi</span>
                </div>
                <div class="hh-stat">
                    <strong>Marka<br>Bağımsız</strong>
                    <span>Tüm markalara servis</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HIZMET KARTLARI -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">KATEGORİLER</span>
            <h2 class="section-title">Hangi konuda destek istiyorsunuz?</h2>
            <p class="section-desc">Projenize uygun hizmeti seçin, detaylı bilgiye ulaşın veya direkt teklif alın.</p>
        </div>

        <?php if ($hizmetler): ?>
            <div class="hizmet-grid hizmet-grid-lg">
                <?php foreach ($hizmetler as $i => $h): ?>
                <a href="hizmet.php?slug=<?= e($h['slug']) ?>" class="hizmet-card fade-in">
                    <div class="hizmet-card-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></div>
                    <div class="hizmet-icon">
                        <?php if (!empty($h['ikon'])): ?>
                            <?= $h['ikon'] ?>
                        <?php else: ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3A1 1 0 0 0 15 7.6L18.4 11A1 1 0 0 0 19.7 11.3L22 9L15 2L12.7 4.3A1 1 0 0 0 13 5.6L14.7 6.3Z"/><path d="M7 17L2 22"/><path d="M10.5 14.5L3.5 21.5"/></svg>
                        <?php endif; ?>
                    </div>
                    <h2 class="hizmet-title"><?= e($h['ad']) ?></h2>
                    <?php if (!empty($h['kisa_aciklama'])): ?>
                        <p class="hizmet-desc"><?= e(kisalt($h['kisa_aciklama'], 160)) ?></p>
                    <?php endif; ?>
                    <span class="hizmet-link">
                        Detaylı Bilgi
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>Henüz hizmet eklenmedi</h3>
                <p>Yönetim panelinden hizmet ekleyebilirsiniz.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- SÜREÇ / NASIL ÇALIŞIRIZ -->
<section class="section section-bg-alt">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">SÜREÇ</span>
            <h2 class="section-title">Nasıl çalışırız?</h2>
            <p class="section-desc">Talebin alınmasından çözümün teslimine kadar şeffaf, izlenebilir bir süreç.</p>
        </div>

        <div class="surec-grid">
            <div class="surec-adim">
                <div class="surec-num">01</div>
                <h3>Talep Alımı</h3>
                <p>Telefon, e-posta veya WhatsApp üzerinden talebinizi iletin. İlk 2 saat içinde geri dönüş yapılır.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">02</div>
                <h3>Analiz</h3>
                <p>İhtiyacınız uzaktan veya sahada incelenir. Fotoğraf, video ve teknik dokümantasyon toplanır.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">03</div>
                <h3>Teklif</h3>
                <p>Şeffaf, detaylı ve yazılı teklif hazırlanır. İş kapsamı, süresi, garanti koşulları net belirtilir.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">04</div>
                <h3>Uygulama</h3>
                <p>Kabul edilen işler belirtilen süre içinde, kalite standartlarımıza uygun şekilde tamamlanır.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">05</div>
                <h3>Takip</h3>
                <p>İş sonrası kalite takibi ve uzun dönem destek. Memnuniyetiniz takipli garanti kapsamındadır.</p>
            </div>
        </div>
    </div>
</section>

<!-- NEDEN BİZİ SEÇMELİ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">AVANTAJLARIMIZ</span>
            <h2 class="section-title">Neden Enamak?</h2>
        </div>
        <div class="hizmet-avantaj-grid">
            <div class="hizmet-avantaj">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <h4>Hızlı Müdahale</h4>
                <p>Konya ve çevresinde aynı gün, Türkiye genelinde 24-48 saat içinde sahada.</p>
            </div>
            <div class="hizmet-avantaj">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <h4>Marka Bağımsız</h4>
                <p>Endümak, Abana, Strong, ithal ve yerli tüm markalara servis ve yedek parça desteği.</p>
            </div>
            <div class="hizmet-avantaj">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>
                <h4>Yerli Yedek Parça</h4>
                <p>Türbin kanatları, manganlı astarlar, filtre kartuşları — kritik yedekler depomuzda stok.</p>
            </div>
            <div class="hizmet-avantaj">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <h4>Deneyimli Kadro</h4>
                <p>15+ yıl sektör tecrübesi, mühendis gözetiminde her iş. Acemi çalışmaz, sertifikalı teknisyenler.</p>
            </div>
            <div class="hizmet-avantaj">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <h4>7/24 Destek</h4>
                <p>Acil arızalar için WhatsApp hattımız 7 gün 24 saat açık. Mesai dışı da uzaktan tanı mümkün.</p>
            </div>
            <div class="hizmet-avantaj">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <h4>Şeffaf Fiyatlandırma</h4>
                <p>Her kalem yazılı teklife dahil. Sonradan sürpriz yok, gizli maliyet yok.</p>
            </div>
        </div>
    </div>
</section>

<!-- FİNAL CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div class="cta-text">
                <h2>İhtiyacınıza özel çözüm</h2>
                <p>Hangi hizmet size uygun, birlikte belirleyelim. İlk analiz ve teklif ücretsiz.</p>
            </div>
            <div class="cta-actions">
                <a href="teklif-al.php" class="btn btn-primary btn-lg">Teklif Al</a>
                <a href="iletisim.php" class="btn btn-outline btn-lg">İletişim</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
