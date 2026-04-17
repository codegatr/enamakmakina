<?php
require_once __DIR__ . '/functions.php';

$kategori_slug = $_GET['kategori'] ?? '';
$arama = trim($_GET['arama'] ?? '');
$sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
$sayfa_basi = 12;

// Kategori
$kategori = null;
if ($kategori_slug) {
    $stmt = $pdo->prepare("SELECT * FROM urun_kategoriler WHERE slug = ? AND aktif = 1");
    $stmt->execute([$kategori_slug]);
    $kategori = $stmt->fetch();
}

// Where
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

// Toplam
$stmt = $pdo->prepare("SELECT COUNT(*) FROM urunler u WHERE $where_sql");
$stmt->execute($params);
$toplam = (int)$stmt->fetchColumn();

// Listele
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

// Tüm kategoriler
$tum_kategoriler = $pdo->query("SELECT * FROM urun_kategoriler WHERE aktif = 1 ORDER BY sira, id")->fetchAll();

// SEO
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
        <h1 class="page-title"><?= e($kategori['ad'] ?? 'Kumlama Makinaları') ?></h1>
        <?php if ($kategori && !empty($kategori['aciklama'])): ?>
            <p class="page-desc"><?= e(kisalt($kategori['aciklama'], 220)) ?></p>
        <?php elseif (!$kategori): ?>
            <p class="page-desc">Farklı parça tipleri ve üretim kapasitelerine uygun profesyonel kumlama çözümleri.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <div class="urunler-wrap">
            <!-- Sidebar -->
            <aside class="urun-sidebar">
                <div class="sidebar-box">
                    <h3 class="sidebar-title">Ürün Kategorileri</h3>
                    <ul class="kategori-menu">
                        <li><a href="urunler.php" class="<?= !$kategori ? 'active' : '' ?>">Tüm Ürünler</a></li>
                        <?php foreach ($tum_kategoriler as $k): ?>
                            <li>
                                <a href="urunler.php?kategori=<?= e($k['slug']) ?>" class="<?= $kategori && $kategori['id'] == $k['id'] ? 'active' : '' ?>">
                                    <?= e($k['ad']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sidebar-box">
                    <h3 class="sidebar-title">Arama</h3>
                    <form method="get" action="urunler.php" class="sidebar-form">
                        <?php if ($kategori): ?>
                            <input type="hidden" name="kategori" value="<?= e($kategori_slug) ?>">
                        <?php endif; ?>
                        <input type="text" name="arama" value="<?= e($arama) ?>" placeholder="Ürün ara..." class="form-control">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Ara</button>
                    </form>
                </div>
                <div class="sidebar-box sidebar-cta">
                    <h3>Özel İmalat</h3>
                    <p>Projenize özel kumlama makinası için teklif alın.</p>
                    <a href="teklif-al.php" class="btn btn-primary btn-block">Teklif İste</a>
                </div>
            </aside>

            <!-- Liste -->
            <div class="urun-liste">
                <?php if ($urunler): ?>
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
                                    <p class="urun-desc"><?= e(kisalt($u['kisa_aciklama'], 100)) ?></p>
                                <?php endif; ?>
                                <div class="urun-actions">
                                    <a href="urun.php?slug=<?= e($u['slug']) ?>" class="btn btn-sm btn-outline">İncele</a>
                                    <a href="teklif-al.php?urun=<?= (int)$u['id'] ?>" class="btn btn-sm btn-primary">Teklif</a>
                                </div>
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
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
