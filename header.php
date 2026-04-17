<?php
require_once __DIR__ . '/functions.php';
ob_start();

// Sayfa değişkenleri (ana sayfadan gelir)
$sayfa_baslik = $sayfa_baslik ?? ayar('site_baslik');
$sayfa_aciklama = $sayfa_aciklama ?? ayar('site_aciklama');
$sayfa_anahtar = $sayfa_anahtar ?? ayar('site_anahtar');
$og_gorsel = $og_gorsel ?? resim_url('assets/img/og-image.jpg');
$canonical = $canonical ?? (SITE_URL . ($_SERVER['REQUEST_URI'] ?? ''));

// Ziyaret kaydet
ziyaret_kaydet();

$ga = ayar('google_analytics');
$gsc = ayar('google_search_console');
?>
<!DOCTYPE html>
<html lang="tr" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#1e40af">
<title><?= e($sayfa_baslik) ?></title>
<meta name="description" content="<?= e($sayfa_aciklama) ?>">
<meta name="keywords" content="<?= e($sayfa_anahtar) ?>">
<meta name="author" content="Enamak Makina">
<meta name="robots" content="index,follow">
<link rel="canonical" href="<?= e($canonical) ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="Enamak Makina">
<meta property="og:title" content="<?= e($sayfa_baslik) ?>">
<meta property="og:description" content="<?= e($sayfa_aciklama) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($og_gorsel) ?>">
<meta property="og:locale" content="tr_TR">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($sayfa_baslik) ?>">
<meta name="twitter:description" content="<?= e($sayfa_aciklama) ?>">
<meta name="twitter:image" content="<?= e($og_gorsel) ?>">

<?php if (!empty($gsc)): ?>
<meta name="google-site-verification" content="<?= e($gsc) ?>">
<?php endif; ?>

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="<?= e(resim_url(ayar('favicon', 'assets/img/favicon.svg'))) ?>">
<link rel="alternate icon" href="<?= e(SITE_URL) ?>/favicon.ico">
<link rel="apple-touch-icon" href="<?= e(resim_url(ayar('favicon', 'assets/img/favicon.svg'))) ?>">

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Stylesheets -->
<link rel="stylesheet" href="<?= e(SITE_URL) ?>/assets/css/style.css?v=<?= e(ayar('versiyon', '1.0.0')) ?>">

<!-- Schema.org LocalBusiness -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "<?= e(SITE_URL) ?>/#organization",
  "name": "<?= e(ayar('firma_adi', 'Enamak Makina')) ?>",
  "legalName": "<?= e(ayar('firma_unvan')) ?>",
  "url": "<?= e(SITE_URL) ?>",
  "logo": {
    "@type": "ImageObject",
    "url": "<?= e(resim_url(ayar('logo', 'assets/img/logo.svg'))) ?>",
    "width": 1200,
    "height": 320
  },
  "image": "<?= e(resim_url('assets/img/og-image.svg')) ?>",
  "description": "<?= e($sayfa_aciklama) ?>",
  "telephone": "<?= e(ayar('telefon')) ?>",
  "email": "<?= e(ayar('email')) ?>",
  "priceRange": "$$-$$$",
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "TR",
    "addressLocality": "Konya",
    "addressRegion": "Konya",
    "streetAddress": "<?= e(ayar('adres')) ?>"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "37.8715",
    "longitude": "32.4846"
  },
  "openingHoursSpecification": [{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
    "opens": "08:30",
    "closes": "18:30"
  }],
  "sameAs": [
    <?php
    $social = array_filter([ayar('facebook'), ayar('instagram'), ayar('linkedin'), ayar('youtube'), ayar('twitter')]);
    echo implode(",\n    ", array_map(function($u){ return '"' . addslashes($u) . '"'; }, $social));
    ?>
  ],
  "areaServed": {
    "@type": "Country",
    "name": "Türkiye"
  },
  "knowsAbout": ["Kumlama Makineleri", "Askılı Kumlama", "Tamburlu Kumlama", "Basınçlı Kumlama", "Tünel Tipi Kumlama", "Yüzey Hazırlık", "Sandblasting Machines"]
}
</script>

<?php if (!empty($breadcrumb) && count($breadcrumb) > 1): ?>
<!-- BreadcrumbList -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    <?php
    $items = [];
    foreach ($breadcrumb as $i => $bc) {
        $url = SITE_URL;
        if (!empty($bc[1])) $url .= '/' . ltrim($bc[1], '/');
        elseif ($i === count($breadcrumb) - 1) $url = $canonical;
        $items[] = sprintf(
            '{"@type":"ListItem","position":%d,"name":"%s","item":"%s"}',
            $i + 1,
            addslashes($bc[0]),
            addslashes($url)
        );
    }
    echo implode(",\n    ", $items);
    ?>
  ]
}
</script>
<?php endif; ?>

<?php if (!empty($schema_ek)): ?>
<!-- Sayfaya özel schema (Product, Article vb) -->
<script type="application/ld+json">
<?= $schema_ek ?>
</script>
<?php endif; ?>

<?php if (!empty($ga)): ?>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= e($ga) ?>');
</script>
<?php endif; ?>
</head>
<body>

<!-- Site Header -->
<header class="site-header" id="siteHeader">
    <div class="container">
        <a href="<?= e(SITE_URL) ?>/" class="logo logo-real" aria-label="ENA-MAK Kumlama Teknolojileri">
            <img src="<?= e(SITE_URL) ?>/assets/img/logo.svg" alt="ENA-MAK Kumlama" width="230" height="70">
        </a>

        <nav class="main-nav" aria-label="Ana menü">
            <a href="<?= e(SITE_URL) ?>/" class="<?= aktif_menu('index.php') ?>">Anasayfa</a>
            <a href="<?= e(SITE_URL) ?>/urunler.php" class="<?= aktif_menu('urunler.php') ?><?= aktif_menu('urun.php') ?>">Ürünler</a>
            <a href="<?= e(SITE_URL) ?>/hizmetler.php" class="<?= aktif_menu('hizmetler.php') ?><?= aktif_menu('hizmet.php') ?>">Hizmetler</a>
            <a href="<?= e(SITE_URL) ?>/hakkimizda.php" class="<?= aktif_menu('hakkimizda.php') ?>">Hakkımızda</a>
            <a href="<?= e(SITE_URL) ?>/blog.php" class="<?= aktif_menu('blog.php') ?><?= aktif_menu('blog-detay.php') ?>">Blog</a>
            <a href="<?= e(SITE_URL) ?>/sss.php" class="<?= aktif_menu('sss.php') ?>">SSS</a>
            <a href="<?= e(SITE_URL) ?>/iletisim.php" class="<?= aktif_menu('iletisim.php') ?>">İletişim</a>
        </nav>

        <div class="header-actions">
            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', ayar('telefon'))) ?>" class="header-phone">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span><?= e(ayar('telefon')) ?></span>
            </a>
            <a href="<?= e(SITE_URL) ?>/teklif-al.php" class="btn btn-primary btn-sm" style="display: none;" id="btnTeklif">Teklif Al</a>
            <button class="menu-toggle" aria-label="Menüyü aç" onclick="toggleMobileMenu()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= e(SITE_URL) ?>/" class="<?= aktif_menu('index.php') ?>">Anasayfa</a>
    <a href="<?= e(SITE_URL) ?>/urunler.php" class="<?= aktif_menu('urunler.php') ?>">Ürünler</a>
    <a href="<?= e(SITE_URL) ?>/hizmetler.php" class="<?= aktif_menu('hizmetler.php') ?>">Hizmetler</a>
    <a href="<?= e(SITE_URL) ?>/hakkimizda.php" class="<?= aktif_menu('hakkimizda.php') ?>">Hakkımızda</a>
    <a href="<?= e(SITE_URL) ?>/blog.php" class="<?= aktif_menu('blog.php') ?>">Blog</a>
    <a href="<?= e(SITE_URL) ?>/sss.php" class="<?= aktif_menu('sss.php') ?>">SSS</a>
    <a href="<?= e(SITE_URL) ?>/referanslar.php" class="<?= aktif_menu('referanslar.php') ?>">Referanslar</a>
    <a href="<?= e(SITE_URL) ?>/iletisim.php" class="<?= aktif_menu('iletisim.php') ?>">İletişim</a>
    <a href="<?= e(SITE_URL) ?>/teklif-al.php" class="btn btn-primary" style="margin-top: 1.5rem; width:100%;">Teklif Al</a>
</div>
