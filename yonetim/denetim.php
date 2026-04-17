<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
admin_yetki('superadmin');

$sayfa_baslik = 'Denetim Kaydı';

$q = trim($_GET['q'] ?? '');
$islem_f = trim($_GET['islem_f'] ?? '');
$sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
$per = 50;
$where = "1=1"; $params = [];
if ($q) { $where .= " AND (aciklama LIKE :q OR islem LIKE :q OR ip LIKE :q)"; $params[':q'] = "%$q%"; }
if ($islem_f) { $where .= " AND islem = :if"; $params[':if'] = $islem_f; }
$stmt = $pdo->prepare("SELECT COUNT(*) FROM denetim_kaydi WHERE $where");
$stmt->execute($params);
$toplam = (int)$stmt->fetchColumn();
$offset = ($sayfa-1)*$per;
$stmt = $pdo->prepare("SELECT d.*, y.kullanici_adi, y.ad_soyad FROM denetim_kaydi d LEFT JOIN yoneticiler y ON y.id = d.kullanici_id WHERE $where ORDER BY d.tarih DESC LIMIT " . (int)$per . " OFFSET " . (int)$offset);
$stmt->execute($params);
$liste = $stmt->fetchAll();

// Farklı işlemler
$islemler = $pdo->query("SELECT DISTINCT islem FROM denetim_kaydi ORDER BY islem")->fetchAll(PDO::FETCH_COLUMN);

include 'header.php';
?>
<div class="page-head">
    <h2>Denetim Kaydı (<?= $toplam ?>)</h2>
</div>
<div class="data-card">
    <div class="data-card-head">
        <form method="get" class="data-search">
            <select name="islem_f" class="form-control" style="min-width:180px;">
                <option value="">Tüm İşlemler</option>
                <?php foreach ($islemler as $i): ?>
                    <option value="<?= e($i) ?>" <?= $islem_f === $i ? 'selected' : '' ?>><?= e($i) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="q" placeholder="Ara..." value="<?= e($q) ?>">
            <button class="btn btn-primary btn-sm">Filtrele</button>
        </form>
    </div>
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th width="140">Tarih</th><th>Kullanıcı</th><th>İşlem</th><th>Tablo/ID</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $d): ?>
            <tr>
                <td style="font-size:12px; color:var(--text-2);"><?= tr_tarih($d['tarih'], true) ?></td>
                <td><?= e($d['ad_soyad'] ?: $d['kullanici_adi'] ?: 'Bilinmiyor') ?></td>
                <td><code style="background:var(--bg-3); padding:2px 8px; border-radius:4px; font-size:12px; color:var(--primary);"><?= e($d['islem']) ?></code></td>
                <td><?= e($d['tablo'] ?: '—') ?><?php if ($d['kayit_id']): ?> #<?= (int)$d['kayit_id'] ?><?php endif; ?></td>
                <td style="font-size:12px; color:var(--text-3);"><?= e($d['ip']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?= sayfalama($toplam, $per, $sayfa, 'denetim.php?' . http_build_query(array_filter(['q'=>$q, 'islem_f'=>$islem_f])) . ($q || $islem_f ? '&' : '') . 'sayfa=%d') ?>
    <?php else: ?>
        <div class="empty-state"><h3>Kayıt yok</h3></div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
