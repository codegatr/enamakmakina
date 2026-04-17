<?php
require_once __DIR__ . '/functions.php';

$hizmetler = $pdo->query("SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY sira ASC, id ASC")->fetchAll();

$sayfa_baslik = 'Hizmetlerimiz - ' . ayar('site_baslik');
$sayfa_aciklama = 'Kumlama makinası imalatı, bakım, revizyon, yedek parça, saha servisi ve danışmanlık hizmetlerimiz.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Hizmetler', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Hizmetlerimiz</h1>
        <p class="page-desc">Kumlama teknolojisinin her aşamasında uçtan uca profesyonel destek.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <div class="hizmet-grid hizmet-grid-lg">
            <?php foreach ($hizmetler as $h): ?>
            <a href="hizmet.php?slug=<?= e($h['slug']) ?>" class="hizmet-card fade-in">
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
    </div>
</section>

<?php include 'footer.php'; ?>
