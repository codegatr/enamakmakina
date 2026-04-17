<?php
if (!function_exists('ayar')) {
    require_once __DIR__ . '/functions.php';
}
if (!headers_sent()) {
    header("HTTP/1.0 404 Not Found");
}
$sayfa_baslik = '404 - Sayfa Bulunamadı - ' . ayar('site_baslik', 'Enamak Makina');
$sayfa_aciklama = 'Aradığınız sayfa bulunamadı.';
include __DIR__ . '/header.php';
?>

<section class="section error-section">
    <div class="container">
        <div class="error-inner">
            <div class="error-code">404</div>
            <h1>Sayfa Bulunamadı</h1>
            <p>Aradığınız sayfa taşınmış, silinmiş veya hiç var olmamış olabilir.</p>
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary btn-lg">Anasayfaya Dön</a>
                <a href="urunler.php" class="btn btn-outline btn-lg">Ürünler</a>
                <a href="iletisim.php" class="btn btn-outline btn-lg">İletişim</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
