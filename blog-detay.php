<?php
require_once __DIR__ . '/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM bloglar WHERE slug = ? AND aktif = 1 LIMIT 1");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

// Görüntülenme
$pdo->prepare("UPDATE bloglar SET goruntulenme = goruntulenme + 1 WHERE id = ?")->execute([$blog['id']]);

$diger = $pdo->prepare("SELECT * FROM bloglar WHERE aktif = 1 AND id != ? ORDER BY yayin_tarihi DESC LIMIT 4");
$diger->execute([$blog['id']]);
$diger_bloglar = $diger->fetchAll();

$sayfa_baslik = ($blog['meta_baslik'] ?: $blog['baslik']) . ' - ' . ayar('site_baslik');
$sayfa_aciklama = $blog['meta_aciklama'] ?: kisalt(strip_tags($blog['ozet'] ?: $blog['icerik']), 160);
$og_gorsel = resim_url($blog['gorsel']);

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Blog', 'blog.php'],
    [$blog['baslik'], ''],
];

include 'header.php';
?>

<article class="blog-detay">
    <section class="page-header blog-header">
        <div class="container">
            <?= breadcrumb($breadcrumb) ?>
            <div class="blog-meta">
                <?php if (!empty($blog['kategori'])): ?>
                    <span class="meta-kategori"><?= e($blog['kategori']) ?></span>
                <?php endif; ?>
                <span><?= tr_tarih($blog['yayin_tarihi'] ?: $blog['olusturma_tarihi']) ?></span>
                <?php if (!empty($blog['yazar'])): ?>
                    <span>• <?= e($blog['yazar']) ?></span>
                <?php endif; ?>
            </div>
            <h1 class="page-title"><?= e($blog['baslik']) ?></h1>
            <?php if (!empty($blog['ozet'])): ?>
                <p class="page-desc"><?= e($blog['ozet']) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section-top-tight">
        <div class="container">
            <div class="icerik-layout">
                <div class="icerik-ana">
                    <?php if (!empty($blog['gorsel'])): ?>
                        <div class="icerik-gorsel">
                            <img src="<?= e(resim_url($blog['gorsel'])) ?>" alt="<?= e($blog['baslik']) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="icerik-alani blog-icerik">
                        <?= $blog['icerik'] ?>
                    </div>

                    <div class="paylas-bar">
                        <span>Paylaş:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/blog-detay.php?slug=' . $blog['slug']) ?>" target="_blank" rel="noopener" aria-label="Facebook'ta paylaş">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12C2 16.84 5.44 20.87 10 21.8V15H8V12H10V9.5C10 7.57 11.57 6 13.5 6H16V9H14C13.45 9 13 9.45 13 10V12H16V15H13V21.95C18.05 21.45 22 17.19 22 12C22 6.48 17.52 2 12 2Z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/blog-detay.php?slug=' . $blog['slug']) ?>&text=<?= urlencode($blog['baslik']) ?>" target="_blank" rel="noopener" aria-label="Twitter'da paylaş">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M22.46 6C21.69 6.35 20.86 6.58 20 6.69C20.88 6.16 21.56 5.32 21.88 4.31C21.05 4.81 20.13 5.16 19.16 5.36C18.37 4.5 17.26 4 16 4C13.65 4 11.73 5.92 11.73 8.29C11.73 8.63 11.77 8.96 11.84 9.27C8.28 9.09 5.11 7.38 3 4.79C2.63 5.42 2.42 6.16 2.42 6.94C2.42 8.43 3.17 9.75 4.33 10.5C3.62 10.5 2.96 10.3 2.38 10V10.03C2.38 12.11 3.86 13.85 5.82 14.24C5.19 14.41 4.53 14.43 3.89 14.31C4.16 15.14 4.69 15.87 5.4 16.4C6.11 16.92 6.96 17.21 7.83 17.22C6.36 18.39 4.52 19.02 2.64 19C2.28 19 1.92 18.98 1.56 18.94C3.44 20.14 5.68 20.84 8.12 20.84C16 20.84 20.33 14.31 20.33 8.66C20.33 8.47 20.33 8.29 20.32 8.1C21.16 7.5 21.88 6.76 22.46 6Z"/></svg>
                        </a>
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode($blog['baslik'] . ' - ' . SITE_URL . '/blog-detay.php?slug=' . $blog['slug']) ?>" target="_blank" rel="noopener" aria-label="WhatsApp'ta paylaş">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.6 6.32C16.12 4.85 14.16 4.04 12.07 4.04C7.77 4.04 4.27 7.54 4.27 11.84C4.27 13.21 4.63 14.54 5.31 15.71L4.21 19.76L8.35 18.67C9.48 19.29 10.76 19.62 12.07 19.62H12.07C16.37 19.62 19.87 16.12 19.87 11.83C19.87 9.74 19.06 7.79 17.6 6.32ZM15.29 14.32C15.11 14.82 14.22 15.29 13.83 15.35C13.47 15.4 13.05 15.42 12.59 15.26C12.31 15.17 11.95 15.05 11.5 14.85C9.56 14.01 8.3 12.06 8.21 11.93C8.11 11.8 7.44 10.9 7.44 9.97C7.44 9.04 7.91 8.58 8.08 8.4C8.25 8.22 8.44 8.18 8.57 8.18C8.69 8.18 8.81 8.18 8.92 8.19C9.04 8.19 9.2 8.14 9.36 8.52C9.52 8.91 9.91 9.85 9.95 9.93C10 10.01 10.04 10.11 9.98 10.23C9.93 10.34 9.89 10.41 9.79 10.52C9.7 10.62 9.6 10.75 9.52 10.83C9.42 10.93 9.32 11.04 9.43 11.23C9.55 11.42 9.94 12.06 10.52 12.58C11.27 13.24 11.9 13.45 12.09 13.55C12.28 13.64 12.39 13.62 12.5 13.5C12.62 13.37 12.99 12.93 13.12 12.74C13.25 12.55 13.39 12.58 13.57 12.65C13.76 12.71 14.71 13.18 14.9 13.27C15.09 13.37 15.22 13.41 15.27 13.49C15.32 13.57 15.32 13.82 15.29 14.32Z"/></svg>
                        </a>
                    </div>
                </div>

                <aside class="icerik-yan">
                    <div class="sidebar-box">
                        <h3 class="sidebar-title">Son Yazılar</h3>
                        <ul class="sidebar-link-list">
                            <?php foreach ($diger_bloglar as $d): ?>
                                <li>
                                    <a href="blog-detay.php?slug=<?= e($d['slug']) ?>">
                                        <?= e($d['baslik']) ?>
                                        <small><?= tr_tarih($d['yayin_tarihi'] ?: $d['olusturma_tarihi']) ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="sidebar-box sidebar-cta">
                        <h3>Teklif Al</h3>
                        <p>Kumlama makinası ihtiyacınız için bizden fiyat alın.</p>
                        <a href="teklif-al.php" class="btn btn-primary btn-block">Hemen Teklif İste</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</article>

<?php include 'footer.php'; ?>
