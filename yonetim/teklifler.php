<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Teklif Talepleri';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM teklif_talepleri WHERE id = ?")->execute([$id]);
    denetim_kaydet('teklif_silindi', 'teklif_talepleri', $id);
    header('Location: teklifler.php?s=1'); exit;
}

if ($islem === 'durum' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $d = $_GET['d'] ?? 'goruldu';
    if (in_array($d, ['yeni', 'goruldu', 'fiyat_gonderildi', 'kapatildi'])) {
        $pdo->prepare("UPDATE teklif_talepleri SET durum = ? WHERE id = ?")->execute([$d, $id]);
    }
    header('Location: teklifler.php?id=' . $id . '&b=1'); exit;
}

// Tekil görüntüleme
if ($id && $islem === 'liste') {
    $stmt = $pdo->prepare("SELECT t.*, u.ad AS urun_ad FROM teklif_talepleri t LEFT JOIN urunler u ON u.id = t.urun_id WHERE t.id = ?");
    $stmt->execute([$id]);
    $t = $stmt->fetch();
    if ($t && $t['durum'] === 'yeni') {
        $pdo->prepare("UPDATE teklif_talepleri SET durum = 'goruldu' WHERE id = ?")->execute([$id]);
        $t['durum'] = 'goruldu';
    }

    include 'header.php';
    if (!$t) { echo '<div class="alert alert-danger">Talep bulunamadı.</div>'; include 'footer.php'; exit; }
    ?>
    <div class="page-head">
        <h2>Teklif Talebi</h2>
        <a href="teklifler.php" class="btn btn-outline">← Listeye Dön</a>
    </div>
    <div class="form-card">
        <?php if (!empty($_GET['b'])): ?><div class="alert alert-success" style="margin-bottom:20px;">Durum güncellendi.</div><?php endif; ?>
        <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap;">
            <div style="flex:1; min-width:280px;">
                <h3 style="color:#fff; font-size:20px;"><?= e($t['ad_soyad']) ?></h3>
                <?php if ($t['firma']): ?><p style="color:var(--text-2);"><?= e($t['firma']) ?></p><?php endif; ?>
                <p style="color:var(--text-2); margin-top:6px;">✉ <?= e($t['email']) ?></p>
                <p style="color:var(--text-2);">📞 <?= e($t['telefon']) ?></p>
                <?php if ($t['urun_ad']): ?><p style="color:var(--text-2);">📦 <?= e($t['urun_ad']) ?></p><?php endif; ?>
            </div>
            <div style="text-align:right;">
                <span class="tag tag-<?= $t['durum']==='yeni'?'orange':($t['durum']==='fiyat_gonderildi'?'green':($t['durum']==='kapatildi'?'gray':'blue')) ?>"><?= strtoupper(str_replace('_',' ',$t['durum'])) ?></span>
                <div style="color:var(--text-3); font-size:12px; margin-top:6px;"><?= tr_tarih($t['olusturma_tarihi'], true) ?></div>
            </div>
        </div>

        <hr style="border:0; border-top:1px solid var(--border); margin:20px 0;">

        <div class="form-grid-2">
            <?php if ($t['parca_tipi']): ?><div><strong>Parça Tipi:</strong><br><span style="color:var(--text-2);"><?= e($t['parca_tipi']) ?></span></div><?php endif; ?>
            <?php if ($t['parca_boyut']): ?><div><strong>Parça Boyutu:</strong><br><span style="color:var(--text-2);"><?= e($t['parca_boyut']) ?></span></div><?php endif; ?>
            <?php if ($t['gunluk_uretim']): ?><div><strong>Günlük Üretim:</strong><br><span style="color:var(--text-2);"><?= e($t['gunluk_uretim']) ?></span></div><?php endif; ?>
            <?php if ($t['mevcut_durum']): ?><div><strong>Mevcut Durum:</strong><br><span style="color:var(--text-2);"><?= e($t['mevcut_durum']) ?></span></div><?php endif; ?>
        </div>

        <?php if ($t['mesaj']): ?>
            <h4 style="margin-top:20px; color:#fff;">Mesaj</h4>
            <div style="background:var(--bg); padding:16px; border-radius:8px; border:1px solid var(--border); white-space:pre-wrap; color:var(--text); line-height:1.7; margin-top:8px;">
<?= e($t['mesaj']) ?>
            </div>
        <?php endif; ?>

        <div style="margin-top:20px; font-size:12px; color:var(--text-3);">IP: <?= e($t['ip']) ?></div>

        <div class="form-actions" style="flex-wrap:wrap;">
            <a href="mailto:<?= e($t['email']) ?>?subject=Teklif%20-%20Enamak%20Makina" class="btn btn-outline">E-Posta</a>
            <a href="tel:<?= e($t['telefon']) ?>" class="btn btn-outline">Ara</a>
            <a href="teklifler.php?islem=durum&id=<?= (int)$t['id'] ?>&d=fiyat_gonderildi&t=<?= e(csrf_token()) ?>" class="btn btn-success">Fiyat Gönderildi</a>
            <a href="teklifler.php?islem=durum&id=<?= (int)$t['id'] ?>&d=kapatildi&t=<?= e(csrf_token()) ?>" class="btn btn-outline">Kapat</a>
            <a href="teklifler.php?islem=sil&id=<?= (int)$t['id'] ?>&t=<?= e(csrf_token()) ?>" class="btn btn-danger" data-sil="Silmek istediğinize emin misiniz?">Sil</a>
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
if ($q) { $where .= " AND (ad_soyad LIKE :q OR email LIKE :q OR firma LIKE :q OR telefon LIKE :q)"; $params[':q'] = "%$q%"; }
$stmt = $pdo->prepare("SELECT COUNT(*) FROM teklif_talepleri WHERE $where");
$stmt->execute($params);
$toplam = (int)$stmt->fetchColumn();
$offset = ($sayfa-1)*$per;
$stmt = $pdo->prepare("SELECT t.*, u.ad AS urun_ad FROM teklif_talepleri t LEFT JOIN urunler u ON u.id = t.urun_id WHERE $where ORDER BY t.olusturma_tarihi DESC LIMIT " . (int)$per . " OFFSET " . (int)$offset);
$stmt->execute($params);
$liste = $stmt->fetchAll();

include 'header.php';
?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>

<div class="page-head">
    <h2>Teklif Talepleri (<?= $toplam ?>)</h2>
</div>
<div class="data-card">
    <div class="data-card-head">
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="teklifler.php" class="btn btn-sm <?= !$durum ? 'btn-primary' : 'btn-outline' ?>">Tümü</a>
            <a href="teklifler.php?durum=yeni" class="btn btn-sm <?= $durum === 'yeni' ? 'btn-primary' : 'btn-outline' ?>">Yeni</a>
            <a href="teklifler.php?durum=goruldu" class="btn btn-sm <?= $durum === 'goruldu' ? 'btn-primary' : 'btn-outline' ?>">Görüldü</a>
            <a href="teklifler.php?durum=fiyat_gonderildi" class="btn btn-sm <?= $durum === 'fiyat_gonderildi' ? 'btn-primary' : 'btn-outline' ?>">Fiyat Gönderildi</a>
            <a href="teklifler.php?durum=kapatildi" class="btn btn-sm <?= $durum === 'kapatildi' ? 'btn-primary' : 'btn-outline' ?>">Kapatıldı</a>
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
        <thead><tr><th>Gönderen</th><th>Ürün/İhtiyaç</th><th width="120">Telefon</th><th width="120">Tarih</th><th width="120">Durum</th><th width="100" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $t): ?>
            <tr style="cursor:pointer;" onclick="window.location='teklifler.php?id=<?= (int)$t['id'] ?>'">
                <td>
                    <strong style="color:#fff;"><?= e($t['ad_soyad']) ?></strong>
                    <?php if ($t['firma']): ?><br><small style="color:var(--text-3);"><?= e($t['firma']) ?></small><?php endif; ?>
                    <br><small style="color:var(--text-3);"><?= e($t['email']) ?></small>
                </td>
                <td><?= e($t['urun_ad'] ?: ($t['parca_tipi'] ?: '—')) ?></td>
                <td><?= e($t['telefon']) ?></td>
                <td style="font-size:12px; color:var(--text-2);"><?= tr_tarih($t['olusturma_tarihi'], true) ?></td>
                <td><span class="tag tag-<?= $t['durum']==='yeni'?'orange':($t['durum']==='fiyat_gonderildi'?'green':($t['durum']==='kapatildi'?'gray':'blue')) ?>"><?= strtoupper(str_replace('_',' ',$t['durum'])) ?></span></td>
                <td class="actions-cell" onclick="event.stopPropagation();">
                    <a href="teklifler.php?id=<?= (int)$t['id'] ?>" title="Görüntüle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg></a>
                    <a href="teklifler.php?islem=sil&id=<?= (int)$t['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?= sayfalama($toplam, $per, $sayfa, 'teklifler.php?' . http_build_query(array_filter(['durum'=>$durum, 'q'=>$q])) . ($durum || $q ? '&' : '') . 'sayfa=%d') ?>
    <?php else: ?>
        <div class="empty-state"><h3>Teklif talebi yok</h3></div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
