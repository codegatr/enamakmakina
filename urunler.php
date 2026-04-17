<?php
require_once __DIR__ . '/functions.php';

$kategori_slug = $_GET['kategori'] ?? '';
$arama = trim($_GET['arama'] ?? '');
$sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
$sayfa_basi = 12;

$kategori = null;
if ($kategori_slug) {
    $stmt = $pdo->prepare("SELECT * FROM urun_kategoriler WHERE slug = ? AND aktif = 1");
    $stmt->execute([$kategori_slug]);
    $kategori = $stmt->fetch();
}

$where = ["u.aktif = 1"];
$params = [];
if ($kategori) {
    $where[] = "u.kategori_id = :kid";
    $params[':kid'] = $kategori['id'];
}
if ($arama !== '') {
    $where[] = "(u.ad LIKE :q OR u.kisa_aciklama LIKE :q OR u.aciklama LIKE :q)";
    $params[':q'] = '%' . $arama . '%';
}
$where_sql = implode(' AND ', $where);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM urunler u WHERE $where_sql");
$stmt->execute($params);
$toplam = (int)$stmt->fetchColumn();

$offset = ($sayfa - 1) * $sayfa_basi;
$limit_sql = " LIMIT " . (int)$sayfa_basi . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare("SELECT u.*, k.ad AS kategori_ad, k.slug AS kategori_slug
                       FROM urunler u
                       LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id
                       WHERE $where_sql
                       ORDER BY u.sira ASC, u.id DESC
                       $limit_sql");
$stmt->execute($params);
$urunler = $stmt->fetchAll();

$tum_kategoriler = $pdo->query("SELECT * FROM urun_kategoriler WHERE aktif = 1 ORDER BY sira, id")->fetchAll();

if ($kategori) {
    $sayfa_baslik = $kategori['ad'] . ' - ' . ayar('site_baslik');
    $sayfa_aciklama = $kategori['meta_aciklama'] ?: kisalt($kategori['aciklama'], 160);
} else {
    $sayfa_baslik = 'Kumlama Makinaları - ' . ayar('site_baslik');
    $sayfa_aciklama = 'Askılı, tamburlu, basınçlı, tünel tip, vakumlu ve özel tasarım kumlama makinalarımız. Endüstriyel kumlama çözümleri.';
}

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Ürünler', 'urunler.php'],
];
if ($kategori) {
    $breadcrumb[] = [$kategori['ad'], ''];
}

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title"><?= e($kategori['ad'] ?? 'Ürünlerimiz') ?></h1>
        <?php if ($kategori && !empty($kategori['aciklama'])): ?>
            <p class="page-desc"><?= e(kisalt($kategori['aciklama'], 220)) ?></p>
        <?php elseif (!$kategori): ?>
            <p class="page-desc">Her sektöre özel tasarlanan, Türkiye'de imal edilen profesyonel kumlama çözümleri. <?= count($tum_kategoriler) ?> farklı kategoride, <?= $toplam ?> makine modeli.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Filter Bar -->
<section class="urun-filter-bar">
    <div class="container">
        <div class="filter-pills">
            <a href="urunler.php" class="filter-pill <?= !$kategori ? 'active' : '' ?>">
                Tümü
                <span class="filter-pill-count"><?= (int)$pdo->query("SELECT COUNT(*) FROM urunler WHERE aktif=1")->fetchColumn() ?></span>
            </a>
            <?php foreach ($tum_kategoriler as $k):
                $sayi = (int)$pdo->query("SELECT COUNT(*) FROM urunler WHERE aktif=1 AND kategori_id=" . (int)$k['id'])->fetchColumn();
            ?>
                <a href="urunler.php?kategori=<?= e($k['slug']) ?>" class="filter-pill <?= $kategori && $kategori['id'] == $k['id'] ? 'active' : '' ?>">
                    <?= e($k['ad']) ?>
                    <?php if ($sayi): ?><span class="filter-pill-count"><?= $sayi ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <form method="get" action="urunler.php" class="filter-search">
            <?php if ($kategori): ?>
                <input type="hidden" name="kategori" value="<?= e($kategori_slug) ?>">
            <?php endif; ?>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="arama" value="<?= e($arama) ?>" placeholder="Ürün ara...">
            <?php if ($arama): ?>
                <button type="submit" class="btn btn-sm btn-primary">Ara</button>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Products -->
<section class="section urun-list-section">
    <div class="container">
        <?php if ($urunler): ?>
            <div class="urun-grid-v2">
                <?php foreach ($urunler as $u): ?>
                <article class="urun-card-v2 fade-in">
                    <a href="urun.php?slug=<?= e($u['slug']) ?>" class="urun-card-v2-img">
                        <img src="<?= e(resim_url($u['gorsel'])) ?>" alt="<?= e($u['ad']) ?>" loading="lazy">
                    </a>
                    <div class="urun-card-v2-body">
                        <?php if (!empty($u['kategori_ad'])): ?>
                            <span class="urun-card-v2-cat"><?= e($u['kategori_ad']) ?></span>
                        <?php endif; ?>
                        <h3 class="urun-card-v2-title">
                            <a href="urun.php?slug=<?= e($u['slug']) ?>"><?= e($u['ad']) ?></a>
                        </h3>
                        <?php if (!empty($u['kisa_aciklama'])): ?>
                            <p class="urun-card-v2-desc"><?= e(kisalt($u['kisa_aciklama'], 90)) ?></p>
                        <?php endif; ?>
                        <a href="urun.php?slug=<?= e($u['slug']) ?>" class="urun-card-v2-btn">
                            İncele <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php
            $url_pattern = 'urunler.php?' . http_build_query(array_filter(['kategori' => $kategori_slug, 'arama' => $arama])) . ($kategori_slug || $arama ? '&' : '') . 'sayfa=%d';
            echo sayfalama($toplam, $sayfa_basi, $sayfa, $url_pattern);
            ?>
        <?php else: ?>
            <div class="empty-state">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                <h3>Sonuç Bulunamadı</h3>
                <p>Aradığınız kriterlere uygun ürün bulunamadı. Farklı arama yapabilir veya bizimle iletişime geçebilirsiniz.</p>
                <a href="urunler.php" class="btn btn-primary">Tüm Ürünleri Gör</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Özel İmalat CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div class="cta-text">
                <h2>Aradığınızı Bulamadınız mı?</h2>
                <p>Parça tipinize, üretim kapasitenize ve mekan koşullarınıza göre özel kumlama makinası tasarlıyoruz. 3D CAD, mühendislik hesapları, FAT testi ve devreye alma dahil.</p>
            </div>
            <div class="cta-actions">
                <a href="teklif-al.php" class="btn btn-primary btn-lg">Özel Teklif Al</a>
                <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', ayar('telefon'))) ?>" class="btn btn-outline btn-lg">Ara: <?= e(ayar('telefon')) ?></a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
