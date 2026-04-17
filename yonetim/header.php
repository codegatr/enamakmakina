<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();

$sayfa_baslik = $sayfa_baslik ?? 'Yönetim Paneli';

// Bildirim sayacı
try {
    $yeni_mesaj = (int)$pdo->query("SELECT COUNT(*) FROM iletisim_mesajlari WHERE durum = 'yeni'")->fetchColumn();
    $yeni_teklif = (int)$pdo->query("SELECT COUNT(*) FROM teklif_talepleri WHERE durum = 'yeni'")->fetchColumn();
} catch (Exception $e) {
    $yeni_mesaj = 0;
    $yeni_teklif = 0;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($sayfa_baslik) ?> - <?= e(ayar('site_baslik', 'Enamak Makina')) ?></title>
<link rel="stylesheet" href="assets/admin.css">
<link rel="icon" href="../assets/img/favicon.svg" type="image/svg+xml">
</head>
<body>
<div class="admin-wrap">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-topbar">
            <button class="sidebar-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('open')" aria-label="Menü">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1 class="admin-page-title"><?= e($sayfa_baslik) ?></h1>
            <div class="admin-topbar-right">
                <a href="../index.php" target="_blank" class="topbar-btn" title="Siteyi Görüntüle">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>
                <a href="mesajlar.php" class="topbar-btn" title="Mesajlar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15A2 2 0 0 1 19 17H7L3 21V5A2 2 0 0 1 5 3H19A2 2 0 0 1 21 5Z"/></svg>
                    <?php if ($yeni_mesaj > 0): ?><span class="badge"><?= $yeni_mesaj ?></span><?php endif; ?>
                </a>
                <a href="teklifler.php" class="topbar-btn" title="Teklifler">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6A2 2 0 0 0 4 4V20A2 2 0 0 0 6 22H18A2 2 0 0 0 20 20V8Z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <?php if ($yeni_teklif > 0): ?><span class="badge"><?= $yeni_teklif ?></span><?php endif; ?>
                </a>
                <div class="user-menu">
                    <button class="user-btn" onclick="this.nextElementSibling.classList.toggle('show')">
                        <span class="avatar"><?= e(mb_substr($_SESSION['admin_ad_soyad'] ?: $_SESSION['admin_kullanici'], 0, 1)) ?></span>
                        <span class="user-name"><?= e($_SESSION['admin_ad_soyad'] ?: $_SESSION['admin_kullanici']) ?></span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="user-dropdown">
                        <a href="profil.php">Profilim</a>
                        <a href="ayarlar.php">Site Ayarları</a>
                        <a href="cikis.php" class="danger">Çıkış</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-content">
