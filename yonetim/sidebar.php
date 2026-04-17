<?php
$mevcut = basename($_SERVER['PHP_SELF'], '.php');
function aktif_class($s, $mevcut) {
    $list = is_array($s) ? $s : [$s];
    return in_array($mevcut, $list) ? 'active' : '';
}
?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="panel.php">
            <strong>Enamak</strong> <span>Makina</span>
        </a>
        <small>Yönetim Paneli</small>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-group">
            <div class="nav-group-title">Ana</div>
            <a href="panel.php" class="nav-link <?= aktif_class('panel', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9L12 2L21 9V20A2 2 0 0 1 19 22H5A2 2 0 0 1 3 20Z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Kontrol Paneli
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">İçerik</div>
            <a href="slider.php" class="nav-link <?= aktif_class('slider', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15L16 10L5 21"/></svg>
                Slider
            </a>
            <a href="kategoriler.php" class="nav-link <?= aktif_class('kategoriler', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Kategoriler
            </a>
            <a href="urunler.php" class="nav-link <?= aktif_class('urunler', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8A2 2 0 0 0 20 6.27L13 2.27A2 2 0 0 0 11 2.27L4 6.27A2 2 0 0 0 3 8V16A2 2 0 0 0 4 17.73L11 21.73A2 2 0 0 0 13 21.73L20 17.73A2 2 0 0 0 21 16Z"/></svg>
                Ürünler
            </a>
            <a href="hizmetler.php" class="nav-link <?= aktif_class('hizmetler', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3A1 1 0 0 0 15 7.6L18.4 11A1 1 0 0 0 19.7 11.3L22 9L15 2L13 4"/><path d="M7 17L2 22"/><path d="M11 13L3 21"/></svg>
                Hizmetler
            </a>
            <a href="sayfalar.php" class="nav-link <?= aktif_class('sayfalar', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6A2 2 0 0 0 4 4V20A2 2 0 0 0 6 22H18A2 2 0 0 0 20 20V8Z"/><polyline points="14 2 14 8 20 8"/></svg>
                Sayfalar
            </a>
            <a href="bloglar.php" class="nav-link <?= aktif_class('bloglar', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20V22H6.5A2.5 2.5 0 0 1 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                Blog
            </a>
            <a href="referanslar.php" class="nav-link <?= aktif_class('referanslar', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15L8.5 12L5 15L8.5 18Z"/><path d="M19 15L15.5 12L12 15L15.5 18Z"/><path d="M5 9H19V5H5Z"/></svg>
                Referanslar
            </a>
            <a href="galeri.php" class="nav-link <?= aktif_class('galeri', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15L16 10L5 21"/></svg>
                Galeri
            </a>
            <a href="sss.php" class="nav-link <?= aktif_class('sss', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9A3 3 0 0 1 15 10C15 12 12 13 12 13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                SSS
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">İletişim</div>
            <a href="mesajlar.php" class="nav-link <?= aktif_class('mesajlar', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15A2 2 0 0 1 19 17H7L3 21V5A2 2 0 0 1 5 3H19A2 2 0 0 1 21 5Z"/></svg>
                Mesajlar
                <?php if ($yeni_mesaj > 0): ?><span class="nav-badge"><?= $yeni_mesaj ?></span><?php endif; ?>
            </a>
            <a href="teklifler.php" class="nav-link <?= aktif_class('teklifler', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11H15M9 15H15M17 21H7A2 2 0 0 1 5 19V5A2 2 0 0 1 7 3H12L19 10V19A2 2 0 0 1 17 21Z"/></svg>
                Teklif Talepleri
                <?php if ($yeni_teklif > 0): ?><span class="nav-badge"><?= $yeni_teklif ?></span><?php endif; ?>
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">Sistem</div>
            <a href="ayarlar.php" class="nav-link <?= aktif_class('ayarlar', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15A1.65 1.65 0 0 0 19.73 16.82L19.79 16.88A2 2 0 0 1 19.79 19.71A2 2 0 0 1 16.96 19.71L16.9 19.65A1.65 1.65 0 0 0 15.08 19.32A1.65 1.65 0 0 0 14 20.84V21A2 2 0 0 1 12 23A2 2 0 0 1 10 21V20.91A1.65 1.65 0 0 0 8.9 19.4A1.65 1.65 0 0 0 7.08 19.73L7.02 19.79A2 2 0 0 1 4.19 19.79A2 2 0 0 1 4.19 16.96L4.25 16.9A1.65 1.65 0 0 0 4.58 15.08A1.65 1.65 0 0 0 3.06 14H3A2 2 0 0 1 1 12A2 2 0 0 1 3 10H3.09A1.65 1.65 0 0 0 4.6 8.9A1.65 1.65 0 0 0 4.27 7.08L4.21 7.02A2 2 0 0 1 4.21 4.19A2 2 0 0 1 7.04 4.19L7.1 4.25A1.65 1.65 0 0 0 8.92 4.58H9A1.65 1.65 0 0 0 10 3.06V3A2 2 0 0 1 12 1A2 2 0 0 1 14 3V3.09A1.65 1.65 0 0 0 15 4.6A1.65 1.65 0 0 0 16.82 4.27L16.88 4.21A2 2 0 0 1 19.71 4.21A2 2 0 0 1 19.71 7.04L19.65 7.1A1.65 1.65 0 0 0 19.32 8.92V9A1.65 1.65 0 0 0 20.84 10H21A2 2 0 0 1 23 12A2 2 0 0 1 21 14H20.91A1.65 1.65 0 0 0 19.4 15Z"/></svg>
                Site Ayarları
            </a>
            <?php if ($_SESSION['admin_rol'] === 'superadmin'): ?>
            <a href="kullanicilar.php" class="nav-link <?= aktif_class('kullanicilar', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21V19A4 4 0 0 0 13 15H5A4 4 0 0 0 1 19V21"/><circle cx="9" cy="7" r="4"/><path d="M23 21V19A4 4 0 0 0 19.75 15.13"/><path d="M16 3.13A4 4 0 0 1 16 10.87"/></svg>
                Yöneticiler
            </a>
            <a href="denetim.php" class="nav-link <?= aktif_class('denetim', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22S2 16 2 9V5L12 2L22 5V9C22 16 12 22 12 22Z"/><polyline points="9 12 11 14 15 10"/></svg>
                Denetim Kaydı
            </a>
            <a href="guncelleme.php" class="nav-link <?= aktif_class('guncelleme', $mevcut) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9A9 9 0 0 1 18.36 5.64L23 10M1 14L5.64 18.36A9 9 0 0 0 20.49 15"/></svg>
                Güncelleme
            </a>
            <a href="icerik-yukle.php" class="nav-link <?= aktif_class('icerik-yukle', $mevcut) ?>" style="color:#f59e0b;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                İçerik Yükle
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="cikis.php" class="nav-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5A2 2 0 0 1 3 19V5A2 2 0 0 1 5 3H9"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Çıkış
        </a>
    </div>
</aside>
