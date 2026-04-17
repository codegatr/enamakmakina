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

$diger = $pdo->prepare("SELECT * FROM hizmetler WHERE aktif = 1 AND id != ? ORDER BY sira LIMIT 4");
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

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title"><?= e($hizmet['ad']) ?></h1>
        <?php if (!empty($hizmet['kisa_aciklama'])): ?>
            <p class="page-desc"><?= e($hizmet['kisa_aciklama']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container">
        <div class="icerik-layout">
            <article class="icerik-ana">
                <?php if (!empty($hizmet['gorsel'])): ?>
                    <div class="icerik-gorsel">
                        <img src="<?= e(resim_url($hizmet['gorsel'])) ?>" alt="<?= e($hizmet['ad']) ?>">
                    </div>
                <?php endif; ?>
                <div class="icerik-alani">
                    <?= $hizmet['aciklama'] ?>
                </div>

                <div class="cta-inline">
                    <div>
                        <h3>Bu hizmetten yararlanmak ister misiniz?</h3>
                        <p>Ekibimiz ihtiyaçlarınızı değerlendirip size özel çözüm sunar.</p>
                    </div>
                    <a href="iletisim.php" class="btn btn-primary btn-lg">İletişime Geç</a>
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
                    <p><strong>Telefon:</strong><br><?= e(ayar('telefon')) ?></p>
                    <p><strong>E-Posta:</strong><br><?= e(ayar('email')) ?></p>
                    <a href="teklif-al.php" class="btn btn-primary btn-block">Teklif Al</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
