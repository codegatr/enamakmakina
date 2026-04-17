<?php
require_once __DIR__ . '/functions.php';

$galeri = $pdo->query("SELECT * FROM galeri WHERE aktif = 1 ORDER BY sira ASC, id DESC")->fetchAll();

$sayfa_baslik = 'Galeri - ' . ayar('site_baslik');
$sayfa_aciklama = 'Üretim tesisimiz, imal ettiğimiz makinalar ve saha uygulamaları.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Galeri', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Galeri</h1>
        <p class="page-desc">Üretimimizden ve sahadan kareler.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <?php if ($galeri): ?>
        <div class="galeri-grid">
            <?php foreach ($galeri as $g): ?>
                <a class="galeri-item" href="<?= e(resim_url($g['gorsel'])) ?>" data-caption="<?= e($g['baslik']) ?>" target="_blank">
                    <img src="<?= e(resim_url($g['gorsel'])) ?>" alt="<?= e($g['baslik']) ?>" loading="lazy">
                    <?php if (!empty($g['baslik'])): ?>
                        <div class="galeri-caption"><?= e($g['baslik']) ?></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>Henüz galeri içeriği yok</h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
