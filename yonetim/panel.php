<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();

$sayfa_baslik = 'Kontrol Paneli';

// İstatistikler
try {
    $ist = [
        'urun' => (int)$pdo->query("SELECT COUNT(*) FROM urunler WHERE aktif=1")->fetchColumn(),
        'hizmet' => (int)$pdo->query("SELECT COUNT(*) FROM hizmetler WHERE aktif=1")->fetchColumn(),
        'blog' => (int)$pdo->query("SELECT COUNT(*) FROM bloglar WHERE aktif=1")->fetchColumn(),
        'referans' => (int)$pdo->query("SELECT COUNT(*) FROM referanslar WHERE aktif=1")->fetchColumn(),
        'mesaj_toplam' => (int)$pdo->query("SELECT COUNT(*) FROM iletisim_mesajlari")->fetchColumn(),
        'mesaj_yeni' => (int)$pdo->query("SELECT COUNT(*) FROM iletisim_mesajlari WHERE durum='yeni'")->fetchColumn(),
        'teklif_toplam' => (int)$pdo->query("SELECT COUNT(*) FROM teklif_talepleri")->fetchColumn(),
        'teklif_yeni' => (int)$pdo->query("SELECT COUNT(*) FROM teklif_talepleri WHERE durum='yeni'")->fetchColumn(),
        'ziyaret_bugun' => (int)$pdo->query("SELECT COUNT(*) FROM ziyaretler WHERE DATE(tarih)=CURDATE()")->fetchColumn(),
        'ziyaret_toplam' => (int)$pdo->query("SELECT COUNT(*) FROM ziyaretler")->fetchColumn(),
    ];
} catch (Exception $e) {
    $ist = array_fill_keys(['urun','hizmet','blog','referans','mesaj_toplam','mesaj_yeni','teklif_toplam','teklif_yeni','ziyaret_bugun','ziyaret_toplam'], 0);
}

// Son mesajlar
$son_mesajlar = $pdo->query("SELECT * FROM iletisim_mesajlari ORDER BY olusturma_tarihi DESC LIMIT 5")->fetchAll();
$son_teklifler = $pdo->query("SELECT t.*, u.ad AS urun_ad FROM teklif_talepleri t LEFT JOIN urunler u ON u.id = t.urun_id ORDER BY t.olusturma_tarihi DESC LIMIT 5")->fetchAll();

// Son 7 gün ziyaret
$son7 = $pdo->query("SELECT DATE(tarih) AS g, COUNT(*) AS s FROM ziyaretler WHERE tarih >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(tarih) ORDER BY g ASC")->fetchAll();
$ziyaret_json = json_encode($son7);

include 'header.php';
?>

<div class="dash-hero">
    <div>
        <h2>Hoşgeldin, <?= e($_SESSION['admin_ad_soyad'] ?: $_SESSION['admin_kullanici']) ?>!</h2>
        <p>Siteni yönetmek için aşağıdaki araçları kullanabilirsin.</p>
    </div>
    <div class="hero-quick">
        <a href="../index.php" target="_blank" class="btn btn-outline">Siteyi Görüntüle</a>
        <a href="urunler.php?yeni=1" class="btn btn-primary">+ Yeni Ürün</a>
    </div>
</div>

<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-icon orange"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8A2 2 0 0 0 20 6.27L13 2.27A2 2 0 0 0 11 2.27L4 6.27A2 2 0 0 0 3 8V16A2 2 0 0 0 4 17.73L11 21.73A2 2 0 0 0 13 21.73L20 17.73"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Ürünler</div>
            <div class="stat-num"><?= $ist['urun'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3L15 7.6L18.4 11L19.7 11.3L22 9L15 2L13 4"/><path d="M7 17L2 22"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Hizmetler</div>
            <div class="stat-num"><?= $ist['hizmet'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15A2 2 0 0 1 19 17H7L3 21V5A2 2 0 0 1 5 3H19A2 2 0 0 1 21 5Z"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Mesajlar</div>
            <div class="stat-num"><?= $ist['mesaj_toplam'] ?> <small class="badge"><?= $ist['mesaj_yeni'] ?> yeni</small></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6A2 2 0 0 0 4 4V20A2 2 0 0 0 6 22H18A2 2 0 0 0 20 20V8Z"/><polyline points="14 2 14 8 20 8"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Teklifler</div>
            <div class="stat-num"><?= $ist['teklif_toplam'] ?> <small class="badge"><?= $ist['teklif_yeni'] ?> yeni</small></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Bugün Ziyaret</div>
            <div class="stat-num"><?= $ist['ziyaret_bugun'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gray"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Toplam Ziyaret</div>
            <div class="stat-num"><?= number_format($ist['ziyaret_toplam'], 0, ',', '.') ?></div>
        </div>
    </div>
</div>

<div class="dash-grid">
    <!-- Son Mesajlar -->
    <div class="dash-card">
        <div class="dash-card-head">
            <h3>Son Mesajlar</h3>
            <a href="mesajlar.php" class="link-sm">Tümü</a>
        </div>
        <div class="dash-card-body">
            <?php if ($son_mesajlar): ?>
                <div class="dash-list">
                    <?php foreach ($son_mesajlar as $m): ?>
                        <div class="dash-item">
                            <div class="di-avatar"><?= e(mb_substr($m['ad_soyad'], 0, 1)) ?></div>
                            <div class="di-body">
                                <div class="di-title">
                                    <a href="mesajlar.php?id=<?= (int)$m['id'] ?>"><?= e($m['ad_soyad']) ?></a>
                                    <?php if ($m['durum'] === 'yeni'): ?><span class="tag tag-orange">Yeni</span><?php endif; ?>
                                </div>
                                <div class="di-desc"><?= e(kisalt($m['konu'] ?: $m['mesaj'], 80)) ?></div>
                                <small class="di-time"><?= tr_tarih($m['olusturma_tarihi'], true) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty">Henüz mesaj yok.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Son Teklifler -->
    <div class="dash-card">
        <div class="dash-card-head">
            <h3>Son Teklif Talepleri</h3>
            <a href="teklifler.php" class="link-sm">Tümü</a>
        </div>
        <div class="dash-card-body">
            <?php if ($son_teklifler): ?>
                <div class="dash-list">
                    <?php foreach ($son_teklifler as $t): ?>
                        <div class="dash-item">
                            <div class="di-avatar"><?= e(mb_substr($t['ad_soyad'], 0, 1)) ?></div>
                            <div class="di-body">
                                <div class="di-title">
                                    <a href="teklifler.php?id=<?= (int)$t['id'] ?>"><?= e($t['ad_soyad']) ?></a>
                                    <?php if ($t['firma']): ?><small>(<?= e($t['firma']) ?>)</small><?php endif; ?>
                                    <?php if ($t['durum'] === 'yeni'): ?><span class="tag tag-orange">Yeni</span><?php endif; ?>
                                </div>
                                <div class="di-desc"><?= e($t['urun_ad'] ?: 'Genel teklif talebi') ?> — <?= e($t['telefon']) ?></div>
                                <small class="di-time"><?= tr_tarih($t['olusturma_tarihi'], true) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty">Henüz teklif talebi yok.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hızlı erişim -->
    <div class="dash-card dash-full">
        <div class="dash-card-head">
            <h3>Hızlı Erişim</h3>
        </div>
        <div class="dash-card-body">
            <div class="quick-links">
                <a href="urunler.php" class="ql">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8A2 2 0 0 0 20 6.27L13 2.27A2 2 0 0 0 11 2.27L4 6.27A2 2 0 0 0 3 8V16A2 2 0 0 0 4 17.73L11 21.73A2 2 0 0 0 13 21.73L20 17.73"/></svg>
                    <span>Ürünler</span>
                </a>
                <a href="slider.php" class="ql">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15L16 10L5 21"/></svg>
                    <span>Slider</span>
                </a>
                <a href="bloglar.php" class="ql">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20V22H6.5A2.5 2.5 0 0 1 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                    <span>Blog</span>
                </a>
                <a href="ayarlar.php" class="ql">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/></svg>
                    <span>Ayarlar</span>
                </a>
                <a href="referanslar.php" class="ql">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8.5 22 9 17 14 18 21 12 17.5 6 21 7 14 2 9 9 8.5 12 2"/></svg>
                    <span>Referanslar</span>
                </a>
                <?php if ($_SESSION['admin_rol'] === 'superadmin'): ?>
                <a href="guncelleme.php" class="ql">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M3.51 9A9 9 0 0 1 18.36 5.64"/></svg>
                    <span>Güncelleme</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
