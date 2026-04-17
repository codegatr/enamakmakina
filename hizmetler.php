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
                İmalattan devreye almaya, bakımdan yedek parçaya kadar kumlama sistemlerinin her aşamasında mühendis desteği. Marka bağımsız, 7/24 erişilebilir.
            </p>
            <div class="hizmet-hero-chips">
                <span class="chip"><strong>24-48s</strong> saha müdahale</span>
                <span class="chip"><strong>2 Yıl</strong> garanti</span>
                <span class="chip"><strong>Marka bağımsız</strong> servis</span>
            </div>
        </div>
    </div>
</section>

<!-- HİZMETLER -->
<section class="section">
    <div class="container">
        <?php if ($hizmetler): ?>
            <div class="hizmet-grid hizmet-grid-lg">
                <?php foreach ($hizmetler as $h): ?>
                <a href="hizmet.php?slug=<?= e($h['slug']) ?>" class="hizmet-card">
                    <div class="hizmet-icon">
                        <?php if (!empty($h['ikon'])): ?>
                            <?= $h['ikon'] ?>
                        <?php else: ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3A1 1 0 0 0 15 7.6L18.4 11A1 1 0 0 0 19.7 11.3L22 9L15 2L12.7 4.3A1 1 0 0 0 13 5.6L14.7 6.3Z"/><path d="M7 17L2 22"/><path d="M10.5 14.5L3.5 21.5"/></svg>
                        <?php endif; ?>
                    </div>
                    <h2 class="hizmet-title"><?= e($h['ad']) ?></h2>
                    <?php if (!empty($h['kisa_aciklama'])): ?>
                        <p class="hizmet-desc"><?= e(kisalt($h['kisa_aciklama'], 140)) ?></p>
                    <?php endif; ?>
                    <span class="hizmet-link">
                        Detaylı bilgi
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
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

<!-- SÜREÇ -->
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
                <p>Telefon, e-posta veya WhatsApp üzerinden talebinizi iletin. İlk 2 saat içinde geri dönüş.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">02</div>
                <h3>Analiz</h3>
                <p>İhtiyacınız uzaktan veya sahada incelenir. Fotoğraf, video ve teknik doküman toplanır.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">03</div>
                <h3>Teklif</h3>
                <p>Yazılı teklif: iş kapsamı, süresi, garanti koşulları — net ve şeffaf.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">04</div>
                <h3>Uygulama</h3>
                <p>Belirtilen süre içinde, kalite standartlarımıza uygun şekilde tamamlanır.</p>
            </div>
            <div class="surec-adim">
                <div class="surec-num">05</div>
                <h3>Takip</h3>
                <p>İş sonrası kalite takibi ve uzun dönem destek. Garanti kapsamında izlenir.</p>
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
