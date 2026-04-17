<?php
require_once __DIR__ . '/functions.php';

$sss = $pdo->query("SELECT * FROM sss WHERE aktif = 1 ORDER BY kategori, sira ASC, id ASC")->fetchAll();

// Gruplar
$gruplar = [];
foreach ($sss as $s) {
    $k = $s['kategori'] ?: 'Genel';
    $gruplar[$k][] = $s;
}

$sayfa_baslik = 'Sıkça Sorulan Sorular - ' . ayar('site_baslik');
$sayfa_aciklama = 'Kumlama makinaları, bakım, servis ve satış ile ilgili en çok sorulan sorular.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['SSS', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Sıkça Sorulan Sorular</h1>
        <p class="page-desc">Aklınıza takılanların yanıtları burada.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container container-narrow">
        <?php if ($gruplar): ?>
            <?php foreach ($gruplar as $kategori => $sorular): ?>
                <div class="sss-grup">
                    <h2 class="sss-grup-baslik"><?= e($kategori) ?></h2>
                    <div class="sss-liste">
                        <?php foreach ($sorular as $s): ?>
                            <details class="sss-item">
                                <summary>
                                    <span><?= e($s['soru']) ?></span>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                </summary>
                                <div class="sss-cevap"><?= nl2br(e($s['cevap'])) ?></div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <h3>Henüz SSS eklenmedi</h3>
            </div>
        <?php endif; ?>

        <div class="sss-iletisim">
            <h3>Cevap bulamadınız mı?</h3>
            <p>Sorularınızı doğrudan bize iletebilirsiniz. Ekibimiz en kısa sürede yanıt verir.</p>
            <div class="sss-iletisim-actions">
                <a href="iletisim.php" class="btn btn-primary">İletişime Geç</a>
                <a href="tel:<?= e(str_replace(' ', '', ayar('telefon'))) ?>" class="btn btn-outline">Hemen Ara</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
