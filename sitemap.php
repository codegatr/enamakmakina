<?php
require_once __DIR__ . '/functions.php';
header('Content-Type: application/xml; charset=utf-8');

$base = rtrim(SITE_URL, '/');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

$statik = [
    ['', '1.0', 'daily'],
    ['/urunler.php', '0.9', 'weekly'],
    ['/hizmetler.php', '0.9', 'weekly'],
    ['/hakkimizda.php', '0.8', 'monthly'],
    ['/referanslar.php', '0.7', 'monthly'],
    ['/galeri.php', '0.7', 'monthly'],
    ['/blog.php', '0.8', 'weekly'],
    ['/sss.php', '0.7', 'monthly'],
    ['/iletisim.php', '0.7', 'monthly'],
    ['/teklif-al.php', '0.8', 'monthly'],
];

foreach ($statik as [$yol, $oncelik, $sik]) {
    echo "<url>\n";
    echo "  <loc>" . htmlspecialchars($base . $yol) . "</loc>\n";
    echo "  <changefreq>$sik</changefreq>\n";
    echo "  <priority>$oncelik</priority>\n";
    echo "</url>\n";
}

// Kategoriler
foreach ($pdo->query("SELECT slug, guncelleme_tarihi FROM urun_kategoriler WHERE aktif = 1") as $k) {
    echo "<url>\n";
    echo "  <loc>" . htmlspecialchars($base . '/urunler.php?kategori=' . $k['slug']) . "</loc>\n";
    echo "  <lastmod>" . substr($k['guncelleme_tarihi'] ?: date('Y-m-d'), 0, 10) . "</lastmod>\n";
    echo "  <changefreq>weekly</changefreq>\n";
    echo "  <priority>0.8</priority>\n";
    echo "</url>\n";
}

// Ürünler
foreach ($pdo->query("SELECT slug, guncelleme_tarihi FROM urunler WHERE aktif = 1") as $u) {
    echo "<url>\n";
    echo "  <loc>" . htmlspecialchars($base . '/urun.php?slug=' . $u['slug']) . "</loc>\n";
    echo "  <lastmod>" . substr($u['guncelleme_tarihi'] ?: date('Y-m-d'), 0, 10) . "</lastmod>\n";
    echo "  <changefreq>monthly</changefreq>\n";
    echo "  <priority>0.8</priority>\n";
    echo "</url>\n";
}

// Hizmetler
foreach ($pdo->query("SELECT slug FROM hizmetler WHERE aktif = 1") as $h) {
    echo "<url>\n";
    echo "  <loc>" . htmlspecialchars($base . '/hizmet.php?slug=' . $h['slug']) . "</loc>\n";
    echo "  <changefreq>monthly</changefreq>\n";
    echo "  <priority>0.7</priority>\n";
    echo "</url>\n";
}

// Blog
foreach ($pdo->query("SELECT slug, guncelleme_tarihi FROM bloglar WHERE aktif = 1") as $b) {
    echo "<url>\n";
    echo "  <loc>" . htmlspecialchars($base . '/blog-detay.php?slug=' . $b['slug']) . "</loc>\n";
    echo "  <lastmod>" . substr($b['guncelleme_tarihi'] ?: date('Y-m-d'), 0, 10) . "</lastmod>\n";
    echo "  <changefreq>monthly</changefreq>\n";
    echo "  <priority>0.6</priority>\n";
    echo "</url>\n";
}

// Sayfalar
foreach ($pdo->query("SELECT slug FROM sayfalar WHERE aktif = 1") as $s) {
    echo "<url>\n";
    echo "  <loc>" . htmlspecialchars($base . '/sayfa.php?slug=' . $s['slug']) . "</loc>\n";
    echo "  <changefreq>monthly</changefreq>\n";
    echo "  <priority>0.5</priority>\n";
    echo "</url>\n";
}

echo '</urlset>';
