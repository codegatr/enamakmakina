<?php
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM hizmetler WHERE slug = ? AND aktif = 1 LIMIT 1");
$stmt->execute([$slug]);
$hizmet = $stmt->fetch();

if (!$hizmet) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

$diger = $pdo->prepare("SELECT * FROM hizmetler WHERE aktif = 1 AND id != ? ORDER BY sira LIMIT 5");
$diger->execute([$hizmet['id']]);
$diger_hizmetler = $diger->fetchAll();

$sayfa_baslik = ($hizmet['meta_baslik'] ?: $hizmet['ad']) . ' - ' . ayar('site_baslik');
$sayfa_aciklama = $hizmet['meta_aciklama'] ?: kisalt($hizmet['kisa_aciklama'] ?: $hizmet['aciklama'], 160);
$og_gorsel = resim_url($hizmet['gorsel']);

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Hizmetler', 'hizmetler.php'],
    [$hizmet['ad'], ''],
];

include 'header.php';
?>

<!-- HERO -->
<section class="hizmet-detay-hero">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <div class="hizmet-detay-hero-grid">
            <div class="hizmet-detay-hero-text">
                <span class="section-eyebrow">HİZMET</span>
                <h1 class="hizmet-detay-title"><?= e($hizmet['ad']) ?></h1>
                <?php if (!empty($hizmet['kisa_aciklama'])): ?>
                    <p class="hizmet-detay-lead"><?= e($hizmet['kisa_aciklama']) ?></p>
                <?php endif; ?>
                <div class="hizmet-detay-actions">
                    <a href="teklif-al.php" class="btn btn-primary btn-lg">Teklif Al</a>
                    <a href="iletisim.php" class="btn btn-outline btn-lg">İletişime Geç</a>
                </div>
            </div>
            <?php if (!empty($hizmet['gorsel'])): ?>
            <div class="hizmet-detay-hero-image">
                <img src="<?= e(resim_url($hizmet['gorsel'])) ?>" alt="<?= e($hizmet['ad']) ?>" loading="eager">
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- İÇERİK + SIDEBAR -->
<section class="section">
    <div class="container">
        <div class="icerik-layout">
            <article class="icerik-ana">
                <div class="icerik-alani hizmet-icerik">
                    <?= $hizmet['aciklama'] ?>
                </div>

                <div class="cta-inline">
                    <div>
                        <h3>Bu hizmetten yararlanmak ister misiniz?</h3>
                        <p>Ekibimiz ihtiyaçlarınızı değerlendirip size özel çözüm sunar.</p>
                    </div>
                    <a href="teklif-al.php" class="btn btn-primary btn-lg">Teklif Al</a>
                </div>
            </article>

            <aside class="icerik-yan">
                <div class="sidebar-box">
                    <h3 class="sidebar-title">Diğer Hizmetler</h3>
                    <ul class="sidebar-link-list">
                        <?php foreach ($diger_hizmetler as $d): ?>
                            <li>
                                <a href="hizmet.php?slug=<?= e($d['slug']) ?>">
                                    <?= e($d['ad']) ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sidebar-box sidebar-cta">
                    <h3>Hızlı İletişim</h3>
                    <p><strong>Telefon</strong><br><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', ayar('telefon'))) ?>"><?= e(ayar('telefon')) ?></a></p>
                    <p><strong>E-Posta</strong><br><a href="mailto:<?= e(ayar('email')) ?>"><?= e(ayar('email')) ?></a></p>
                    <a href="teklif-al.php" class="btn btn-primary btn-block">Teklif Al</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
