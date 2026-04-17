<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'İletişim Mesajları';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM iletisim_mesajlari WHERE id = ?")->execute([$id]);
    denetim_kaydet('mesaj_silindi', 'iletisim_mesajlari', $id);
    header('Location: mesajlar.php?s=1'); exit;
}

if ($islem === 'durum' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $d = $_GET['d'] ?? 'okundu';
    if (in_array($d, ['yeni', 'okundu', 'yanitlandi', 'arsiv'])) {
        $pdo->prepare("UPDATE iletisim_mesajlari SET durum = ? WHERE id = ?")->execute([$d, $id]);
    }
    header('Location: mesajlar.php' . ($_GET['geri'] ?? '')); exit;
}

// Tekil görüntüleme
if ($id && $islem === 'liste') {
    $stmt = $pdo->prepare("SELECT * FROM iletisim_mesajlari WHERE id = ?");
    $stmt->execute([$id]);
    $m = $stmt->fetch();
    if ($m && $m['durum'] === 'yeni') {
        $pdo->prepare("UPDATE iletisim_mesajlari SET durum = 'okundu' WHERE id = ?")->execute([$id]);
        $m['durum'] = 'okundu';
    }

    include 'header.php';
    if (!$m) {
        echo '<div class="alert alert-danger">Mesaj bulunamadı.</div>';
        include 'footer.php'; exit;
    }
    ?>
    <div class="page-head">
        <h2>Mesaj Detayı</h2>
        <a href="mesajlar.php" class="btn btn-outline">← Listeye Dön</a>
    </div>

    <div class="form-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; gap:20px; flex-wrap:wrap;">
            <div>
                <h2 style="color:#fff; margin-bottom:4px;"><?= e($m['ad_soyad']) ?></h2>
                <p style="color:var(--text-2);"><?= e($m['email']) ?></p>
                <?php if ($m['telefon']): ?><p style="color:var(--text-2);">📞 <?= e($m['telefon']) ?></p><?php endif; ?>
            </div>
            <div>
                <span class="tag tag-<?= $m['durum']==='yeni'?'orange':($m['durum']==='yanitlandi'?'green':'gray') ?>"><?= strtoupper(e($m['durum'])) ?></span>
                <div style="color:var(--text-3); font-size:12px; margin-top:4px;"><?= tr_tarih($m['olusturma_tarihi'], true) ?></div>
            </div>
        </div>

        <?php if ($m['konu']): ?>
            <h3 style="color:#fff; margin-bottom:10px;"><?= e($m['konu']) ?></h3>
        <?php endif; ?>

        <div style="background:var(--bg); padding:18px; border-radius:8px; border:1px solid var(--border); white-space:pre-wrap; color:var(--text); line-height:1.7;">
<?= e($m['mesaj']) ?>
        </div>

        <div style="margin-top:20px; font-size:12px; color:var(--text-3);">
            IP: <?= e($m['ip']) ?>
        </div>

        <div class="form-actions">
            <a href="mailto:<?= e($m['email']) ?>" class="btn btn-outline">E-Posta Yanıtla</a>
            <a href="mesajlar.php?islem=durum&id=<?= (int)$m['id'] ?>&d=yanitlandi&t=<?= e(csrf_token()) ?>&geri=?b=1" class="btn btn-success">Yanıtlandı İşaretle</a>
            <a href="mesajlar.php?islem=sil&id=<?= (int)$m['id'] ?>&t=<?= e(csrf_token()) ?>" class="btn btn-danger" data-sil="Mesajı silmek istediğinize emin misiniz?">Sil</a>
        </div>
    </div>

    <?php
    include 'footer.php';
    exit;
}

// Liste
$durum = $_GET['durum'] ?? '';
$q = trim($_GET['q'] ?? '');
$sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
$per = 20;
$where = "1=1"; $params = [];
if ($durum) { $where .= " AND durum = :d"; $params[':d'] = $durum; }
if ($q) { $where .= " AND (ad_soyad LIKE :q OR email LIKE :q OR konu LIKE :q OR mesaj LIKE :q)"; $params[':q'] = "%$q%"; }
$stmt = $pdo->prepare("SELECT COUNT(*) FROM iletisim_mesajlari WHERE $where");
$stmt->execute($params);
$toplam = (int)$stmt->fetchColumn();
$offset = ($sayfa-1)*$per;
$stmt = $pdo->prepare("SELECT * FROM iletisim_mesajlari WHERE $where ORDER BY olusturma_tarihi DESC LIMIT " . (int)$per . " OFFSET " . (int)$offset);
$stmt->execute($params);
$liste = $stmt->fetchAll();

include 'header.php';
?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if (!empty($_GET['b'])): ?><div class="alert alert-success">Durum güncellendi.</div><?php endif; ?>

<div class="page-head">
    <h2>Mesajlar (<?= $toplam ?>)</h2>
</div>

<div class="data-card">
    <div class="data-card-head">
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="mesajlar.php" class="btn btn-sm <?= !$durum ? 'btn-primary' : 'btn-outline' ?>">Tümü</a>
            <a href="mesajlar.php?durum=yeni" class="btn btn-sm <?= $durum === 'yeni' ? 'btn-primary' : 'btn-outline' ?>">Yeni</a>
            <a href="mesajlar.php?durum=okundu" class="btn btn-sm <?= $durum === 'okundu' ? 'btn-primary' : 'btn-outline' ?>">Okundu</a>
            <a href="mesajlar.php?durum=yanitlandi" class="btn btn-sm <?= $durum === 'yanitlandi' ? 'btn-primary' : 'btn-outline' ?>">Yanıtlandı</a>
            <a href="mesajlar.php?durum=arsiv" class="btn btn-sm <?= $durum === 'arsiv' ? 'btn-primary' : 'btn-outline' ?>">Arşiv</a>
        </div>
        <form method="get" class="data-search">
            <?php if ($durum): ?><input type="hidden" name="durum" value="<?= e($durum) ?>"><?php endif; ?>
            <input type="text" name="q" placeholder="Ara..." value="<?= e($q) ?>">
            <button class="btn btn-primary btn-sm">Ara</button>
        </form>
    </div>
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th>Gönderen</th><th>Konu</th><th width="120">Tarih</th><th width="100">Durum</th><th width="100" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $m): ?>
            <tr style="cursor:pointer;" onclick="window.location='mesajlar.php?id=<?= (int)$m['id'] ?>'">
                <td>
                    <strong style="color:#fff;"><?= e($m['ad_soyad']) ?></strong>
                    <br><small style="color:var(--text-3);"><?= e($m['email']) ?></small>
                </td>
                <td>
                    <?= e(kisalt($m['konu'] ?: $m['mesaj'], 80)) ?>
                </td>
                <td style="font-size:12px; color:var(--text-2);"><?= tr_tarih($m['olusturma_tarihi'], true) ?></td>
                <td><span class="tag tag-<?= $m['durum']==='yeni'?'orange':($m['durum']==='yanitlandi'?'green':'gray') ?>"><?= strtoupper(e($m['durum'])) ?></span></td>
                <td class="actions-cell" onclick="event.stopPropagation();">
                    <a href="mesajlar.php?id=<?= (int)$m['id'] ?>" title="Görüntüle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg></a>
                    <a href="mesajlar.php?islem=sil&id=<?= (int)$m['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?= sayfalama($toplam, $per, $sayfa, 'mesajlar.php?' . http_build_query(array_filter(['durum'=>$durum, 'q'=>$q])) . ($durum || $q ? '&' : '') . 'sayfa=%d') ?>
    <?php else: ?>
        <div class="empty-state"><h3>Mesaj yok</h3></div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
