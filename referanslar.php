<?php
require_once __DIR__ . '/functions.php';

$referanslar = $pdo->query("SELECT * FROM referanslar WHERE aktif = 1 ORDER BY sira ASC, id ASC")->fetchAll();

$sayfa_baslik = 'Referanslarımız - ' . ayar('site_baslik');
$sayfa_aciklama = 'Enamak Makina olarak hizmet verdiğimiz kurumsal firmalar ve referanslarımız.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Referanslar', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Referanslarımız</h1>
        <p class="page-desc">Güvenen tüm firmalara teşekkür ederiz.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <?php if ($referanslar): ?>
        <div class="referans-buyuk-grid">
            <?php foreach ($referanslar as $r): ?>
                <div class="referans-buyuk">
                    <div class="referans-logo">
                        <img src="<?= e(resim_url($r['logo'], 'assets/img/placeholder-logo.svg')) ?>" alt="<?= e($r['firma_adi']) ?>" loading="lazy">
                    </div>
                    <h3><?= e($r['firma_adi']) ?></h3>
                    <?php if (!empty($r['sektor'])): ?>
                        <span class="referans-sektor"><?= e($r['sektor']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($r['aciklama'])): ?>
                        <p><?= e(kisalt($r['aciklama'], 130)) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>Henüz referans eklenmedi</h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
