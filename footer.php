<?php
$whatsapp = preg_replace('/[^0-9]/', '', ayar('whatsapp'));
$telefon_tel = preg_replace('/[^0-9+]/', '', ayar('telefon'));
?>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col footer-about">
                <a href="<?= e(SITE_URL) ?>/" class="logo">
                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="enamakFooterLogoBg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#2563eb"/>
                                <stop offset="100%" stop-color="#1e40af"/>
                            </linearGradient>
                        </defs>
                        <rect x="0" y="0" width="44" height="44" rx="9" fill="url(#enamakFooterLogoBg)"/>
                        <circle cx="22" cy="22" r="14" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <rect x="15.5" y="14" width="12" height="2.4" rx="0.5" fill="#fff"/>
                        <rect x="15.5" y="20.8" width="9" height="2.4" rx="0.5" fill="#fff"/>
                        <rect x="15.5" y="27.6" width="12" height="2.4" rx="0.5" fill="#fff"/>
                        <rect x="15.5" y="14" width="2.4" height="16" rx="0.5" fill="#fff"/>
                    </svg>
                    <div class="logo-text">
                        <span>ENA-MAK<span class="accent">.</span></span>
                        <small class="logo-sub">Kumlama Teknolojileri</small>
                    </div>
                </a>
                <p><?= e(ayar('site_aciklama')) ?></p>
                <div class="footer-social">
                    <?php if (!empty(ayar('facebook'))): ?>
                    <a href="<?= e(ayar('facebook')) ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                    <?php endif; ?>
                    <?php if (!empty(ayar('instagram'))): ?>
                    <a href="<?= e(ayar('instagram')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                    <?php endif; ?>
                    <?php if (!empty(ayar('linkedin'))): ?>
                    <a href="<?= e(ayar('linkedin')) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                    <?php endif; ?>
                    <?php if (!empty(ayar('youtube'))): ?>
                    <a href="<?= e(ayar('youtube')) ?>" target="_blank" rel="noopener" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-col">
                <h4>Ürünler</h4>
                <ul>
                    <?php
                    try {
                        $kategoriler = $pdo->query("SELECT baslik, slug FROM urun_kategoriler WHERE aktif = 1 ORDER BY sira LIMIT 7")->fetchAll();
                        foreach ($kategoriler as $kat):
                    ?>
                    <li><a href="<?= e(SITE_URL) ?>/urunler.php?kategori=<?= e($kat['slug']) ?>"><?= e($kat['baslik']) ?></a></li>
                    <?php
                        endforeach;
                    } catch (Exception $e) {}
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Hizmetler</h4>
                <ul>
                    <?php
                    try {
                        $hizmetler_footer = $pdo->query("SELECT baslik, slug FROM hizmetler WHERE aktif = 1 ORDER BY sira LIMIT 6")->fetchAll();
                        foreach ($hizmetler_footer as $h):
                    ?>
                    <li><a href="<?= e(SITE_URL) ?>/hizmet.php?slug=<?= e($h['slug']) ?>"><?= e($h['baslik']) ?></a></li>
                    <?php
                        endforeach;
                    } catch (Exception $e) {}
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>İletişim</h4>
                <ul>
                    <li>
                        <a href="tel:<?= e($telefon_tel) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <?= e(ayar('telefon')) ?>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:<?= e(ayar('email')) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <?= e(ayar('email')) ?>
                        </a>
                    </li>
                    <li>
                        <span style="display:flex; gap:.5rem; align-items:flex-start;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;color:var(--c-primary);width:16px;height:16px;margin-top:3px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span style="color:var(--c-text-2);"><?= e(ayar('adres')) ?></span>
                        </span>
                    </li>
                    <li>
                        <span style="display:flex; gap:.5rem; align-items:flex-start;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;color:var(--c-primary);width:16px;height:16px;margin-top:3px;"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                            <span style="color:var(--c-text-2);"><?= e(ayar('calisma_saatleri')) ?></span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                © <?= date('Y') ?> <?= e(ayar('firma_adi')) ?>. Tüm hakları saklıdır.
                <span class="footer-codega">
                    Tasarım &amp; Geliştirme:
                    <a href="https://codega.com.tr" target="_blank" rel="noopener noreferrer">CODEGA</a>
                </span>
            </div>
            <div style="display:flex; gap:1.5rem;">
                <a href="<?= e(SITE_URL) ?>/sayfa.php?slug=gizlilik-politikasi">Gizlilik Politikası</a>
                <a href="<?= e(SITE_URL) ?>/sayfa.php?slug=kvkk">KVKK</a>
                <a href="<?= e(SITE_URL) ?>/sayfa.php?slug=cerez-politikasi">Çerez Politikası</a>
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation (Yunus'un standart kalıbı) -->
<nav class="mobile-bottom-nav" aria-label="Alt gezinti">
    <div class="mbn-items">
        <a href="<?= e(SITE_URL) ?>/" class="mbn-item <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Anasayfa</span>
        </a>
        <a href="<?= e(SITE_URL) ?>/urunler.php" class="mbn-item <?= in_array(basename($_SERVER['SCRIPT_NAME']), ['urunler.php','urun.php']) ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 7.27L12 12l-8.5-4.73M12 22V12M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            <span>Ürünler</span>
        </a>
        <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" class="mbn-item" style="color:#25D366;">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <span>WhatsApp</span>
        </a>
        <a href="tel:<?= e($telefon_tel) ?>" class="mbn-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span>Ara</span>
        </a>
        <a href="<?= e(SITE_URL) ?>/iletisim.php" class="mbn-item <?= basename($_SERVER['SCRIPT_NAME']) === 'iletisim.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>İletişim</span>
        </a>
    </div>
</nav>

<!-- WhatsApp Floating -->
<a href="https://wa.me/<?= e($whatsapp) ?>?text=Merhaba, kumlama makineleri hakkında bilgi almak istiyorum." target="_blank" rel="noopener" class="whatsapp-float" aria-label="WhatsApp ile mesaj gönder">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<!-- Cookie Banner -->
<div class="cookie-banner hidden" id="cookieBanner">
    <p>Web sitemiz deneyiminizi iyileştirmek için çerezler kullanmaktadır. <a href="<?= e(SITE_URL) ?>/sayfa.php?slug=cerez-politikasi" style="color:var(--c-primary);">Detaylı bilgi</a></p>
    <div class="btn-group">
        <button onclick="acceptCookies()" class="btn btn-primary btn-sm">Kabul Et</button>
        <button onclick="document.getElementById('cookieBanner').classList.add('hidden')" class="btn btn-outline btn-sm">Reddet</button>
    </div>
</div>

<script src="<?= e(SITE_URL) ?>/assets/js/script.js?v=<?= e(ayar('versiyon', '1.0.0')) ?>"></script>
</body>
</html>
<?php
ob_end_flush();
?>
