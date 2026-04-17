<?php
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM sayfalar WHERE slug = ? AND aktif = 1 LIMIT 1");
$stmt->execute([$slug]);
$sayfa = $stmt->fetch();

if (!$sayfa) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

$sayfa_baslik = ($sayfa['meta_baslik'] ?: $sayfa['baslik']) . ' - ' . ayar('site_baslik');
$sayfa_aciklama = $sayfa['meta_aciklama'] ?: kisalt(strip_tags($sayfa['icerik']), 160);

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    [$sayfa['baslik'], ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title"><?= e($sayfa['baslik']) ?></h1>
    </div>
</section>

<section class="section section-top-tight">
    <div class="container container-narrow">
        <article class="sayfa-icerik">
            <div class="icerik-alani">
                <?= $sayfa['icerik'] ?>
            </div>
        </article>
    </div>
</section>

<?php include 'footer.php'; ?>
