<?php
require_once __DIR__ . '/functions.php';

$sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
$sayfa_basi = 9;

$toplam = (int)$pdo->query("SELECT COUNT(*) FROM bloglar WHERE aktif = 1")->fetchColumn();
$offset = ($sayfa - 1) * $sayfa_basi;
$bloglar = $pdo->query("SELECT * FROM bloglar WHERE aktif = 1 ORDER BY yayin_tarihi DESC, id DESC LIMIT " . (int)$sayfa_basi . " OFFSET " . (int)$offset)->fetchAll();

$sayfa_baslik = 'Blog - ' . ayar('site_baslik');
$sayfa_aciklama = 'Kumlama teknolojileri, sektörel haberler ve teknik yazılar.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Blog', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Blog & Haberler</h1>
        <p class="page-desc">Kumlama makinaları ve sektörel gelişmeler hakkında güncel içerikler.</p>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <?php if ($bloglar): ?>
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
                    <h2 class="blog-title">
                        <a href="blog-detay.php?slug=<?= e($b['slug']) ?>"><?= e($b['baslik']) ?></a>
                    </h2>
                    <?php if (!empty($b['ozet'])): ?>
                        <p class="blog-desc"><?= e(kisalt($b['ozet'], 140)) ?></p>
                    <?php endif; ?>
                    <a href="blog-detay.php?slug=<?= e($b['slug']) ?>" class="blog-link">Devamını Oku →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?= sayfalama($toplam, $sayfa_basi, $sayfa, 'blog.php?sayfa=%d') ?>
        <?php else: ?>
            <div class="empty-state">
                <h3>Henüz yazı yok</h3>
                <p>Yakında yeni içerikler sizlerle olacak.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
