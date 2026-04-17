<?php
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($slug) {
    $stmt = $pdo->prepare("SELECT u.*, k.ad AS kategori_ad, k.slug AS kategori_slug
                           FROM urunler u LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id
                           WHERE u.slug = ? AND u.aktif = 1 LIMIT 1");
    $stmt->execute([$slug]);
} else {
    $stmt = $pdo->prepare("SELECT u.*, k.ad AS kategori_ad, k.slug AS kategori_slug
                           FROM urunler u LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id
                           WHERE u.id = ? AND u.aktif = 1 LIMIT 1");
    $stmt->execute([$id]);
}
$urun = $stmt->fetch();

if (!$urun) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

// Görüntülenme sayısı
$pdo->prepare("UPDATE urunler SET goruntulenme = goruntulenme + 1 WHERE id = ?")->execute([$urun['id']]);

// Teknik özellikler (JSON)
$teknik = [];
if (!empty($urun['teknik_ozellikler'])) {
    $tmp = json_decode($urun['teknik_ozellikler'], true);
    if (is_array($tmp)) $teknik = $tmp;
}

// Galeri (JSON)
$galeri = [];
if (!empty($urun['galeri'])) {
    $tmp = json_decode($urun['galeri'], true);
    if (is_array($tmp)) $galeri = $tmp;
}

// İlgili ürünler
$ilgili = [];
if (!empty($urun['kategori_id'])) {
    $stmt = $pdo->prepare("SELECT u.*, k.ad AS kategori_ad FROM urunler u
                           LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id
                           WHERE u.kategori_id = ? AND u.id != ? AND u.aktif = 1
                           ORDER BY u.sira ASC, u.id DESC LIMIT 4");
    $stmt->execute([$urun['kategori_id'], $urun['id']]);
    $ilgili = $stmt->fetchAll();
}

// SEO
$sayfa_baslik = ($urun['meta_baslik'] ?: $urun['ad']) . ' - ' . ayar('site_baslik');
$sayfa_aciklama = $urun['meta_aciklama'] ?: kisalt($urun['kisa_aciklama'] ?: $urun['aciklama'], 160);
$sayfa_anahtar = $urun['meta_anahtar'] ?: '';
$og_gorsel = resim_url($urun['gorsel']);

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Ürünler', 'urunler.php'],
];
if (!empty($urun['kategori_ad'])) {
    $breadcrumb[] = [$urun['kategori_ad'], 'urunler.php?kategori=' . $urun['kategori_slug']];
}
$breadcrumb[] = [$urun['ad'], ''];

// Product schema (SEO - Rich Snippet)
$schema_ek = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $urun['ad'],
    'description' => $urun['kisa_aciklama'] ?: kisalt(strip_tags($urun['aciklama']), 300),
    'image' => resim_url($urun['gorsel']),
    'sku' => $urun['model_kodu'] ?: $urun['slug'],
    'mpn' => $urun['model_kodu'] ?: '',
    'brand' => [
        '@type' => 'Brand',
        'name' => 'ENA-MAK'
    ],
    'manufacturer' => [
        '@type' => 'Organization',
        'name' => ayar('firma_adi', 'Enamak Makina'),
        'url' => SITE_URL
    ],
    'category' => $urun['kategori_ad'] ?? 'Kumlama Makineleri',
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'TRY',
        'availability' => 'https://schema.org/InStock',
        'priceValidUntil' => date('Y-12-31'),
        'url' => SITE_URL . '/urun.php?slug=' . $urun['slug'],
        'seller' => [
            '@type' => 'Organization',
            'name' => ayar('firma_adi', 'Enamak Makina')
        ]
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

include 'header.php';
?>

<section class="urun-detay-section">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>

        <div class="urun-detay-grid">
            <!-- Görsel -->
            <div class="urun-detay-gorsel">
                <div class="urun-ana-gorsel">
                    <img id="anaGorsel" src="<?= e(resim_url($urun['gorsel'])) ?>" alt="<?= e($urun['ad']) ?>">
                </div>
                <?php if ($galeri): ?>
                <div class="urun-galeri-thumbs">
                    <button class="thumb active" onclick="document.getElementById('anaGorsel').src='<?= e(resim_url($urun['gorsel'])) ?>'; thumbAktif(this)">
                        <img src="<?= e(resim_url($urun['gorsel'])) ?>" alt="">
                    </button>
                    <?php foreach ($galeri as $g): ?>
                    <button class="thumb" onclick="document.getElementById('anaGorsel').src='<?= e(resim_url($g)) ?>'; thumbAktif(this)">
                        <img src="<?= e(resim_url($g)) ?>" alt="">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bilgi -->
            <div class="urun-detay-bilgi">
                <?php if (!empty($urun['kategori_ad'])): ?>
                    <a href="urunler.php?kategori=<?= e($urun['kategori_slug']) ?>" class="urun-kategori-link"><?= e($urun['kategori_ad']) ?></a>
                <?php endif; ?>
                <h1 class="urun-detay-baslik"><?= e($urun['ad']) ?></h1>

                <?php if (!empty($urun['model_kodu'])): ?>
                    <div class="urun-model">Model: <strong><?= e($urun['model_kodu']) ?></strong></div>
                <?php endif; ?>

                <?php if (!empty($urun['kisa_aciklama'])): ?>
                    <div class="urun-kisa-aciklama">
                        <?= nl2br(e($urun['kisa_aciklama'])) ?>
                    </div>
                <?php endif; ?>

                <?php if ($teknik): ?>
                    <div class="urun-teknik-ozet">
                        <h3>Teknik Özellikler</h3>
                        <table class="teknik-tablo">
                            <tbody>
                                <?php foreach ($teknik as $item):
                                    $etiket = $item['etiket'] ?? ($item[0] ?? '');
                                    $deger = $item['deger'] ?? ($item[1] ?? '');
                                ?>
                                <tr>
                                    <td><?= e($etiket) ?></td>
                                    <td><?= e($deger) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="urun-detay-actions">
                    <a href="teklif-al.php?urun=<?= (int)$urun['id'] ?>" class="btn btn-primary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Teklif Al
                    </a>
                    <a href="tel:<?= e(str_replace(' ', '', ayar('telefon'))) ?>" class="btn btn-outline btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92V19.92C22 20.47 21.55 20.92 21 20.92H20C10.06 20.92 2 12.86 2 3V2C2 1.45 2.45 1 3 1H6C6.55 1 7 1.45 7 2V6.5C7 7.05 6.55 7.5 6 7.5H4.5C5.94 11.91 8.09 14.06 12.5 15.5V14C12.5 13.45 12.95 13 13.5 13H18C18.55 13 19 13.45 19 14V17"/></svg>
                        Hemen Ara
                    </a>
                </div>

                <div class="urun-guvenceler">
                    <div class="guvence">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22S2 16 2 9V5L12 2L22 5V9C22 16 12 22 12 22Z"/></svg>
                        2 Yıl Garanti
                    </div>
                    <div class="guvence">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3H5A2 2 0 0 0 3 5V19A2 2 0 0 0 5 21H19A2 2 0 0 0 21 19V8Z"/><polyline points="16 3 16 8 21 8"/></svg>
                        CE Sertifikalı
                    </div>
                    <div class="guvence">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41L13.42 20.58A2 2 0 0 1 10.59 20.58L2 12V2H12L20.59 10.59A2 2 0 0 1 20.59 13.41Z"/></svg>
                        Ömür Boyu Parça
                    </div>
                </div>
            </div>
        </div>

        <!-- Detaylı Açıklama -->
        <?php if (!empty($urun['aciklama'])): ?>
        <div class="urun-detay-icerik">
            <h2>Ürün Açıklaması</h2>
            <div class="icerik-alani">
                <?= $urun['aciklama'] ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- İlgili Ürünler -->
<?php if ($ilgili): ?>
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Benzer Ürünler</h2>
        </div>
        <div class="urun-grid">
            <?php foreach ($ilgili as $u): ?>
            <article class="urun-card">
                <a href="urun.php?slug=<?= e($u['slug']) ?>" class="urun-img-link">
                    <div class="urun-img"><img src="<?= e(resim_url($u['gorsel'])) ?>" alt="<?= e($u['ad']) ?>" loading="lazy"></div>
                </a>
                <div class="urun-body">
                    <?php if (!empty($u['kategori_ad'])): ?>
                        <span class="urun-kategori"><?= e($u['kategori_ad']) ?></span>
                    <?php endif; ?>
                    <h3 class="urun-title"><a href="urun.php?slug=<?= e($u['slug']) ?>"><?= e($u['ad']) ?></a></h3>
                    <div class="urun-actions">
                        <a href="urun.php?slug=<?= e($u['slug']) ?>" class="btn btn-sm btn-outline">İncele</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function thumbAktif(btn) {
    document.querySelectorAll('.urun-galeri-thumbs .thumb').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
}
</script>

<?php include 'footer.php'; ?>
