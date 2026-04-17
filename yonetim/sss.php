<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Sıkça Sorulan Sorular';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM sss WHERE id = ?")->execute([$id]);
    denetim_kaydet('sss_silindi', 'sss', $id);
    header('Location: sss.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $kategori = trim($_POST['kategori'] ?? 'Genel');
        $soru = trim($_POST['soru'] ?? '');
        $cevap = trim($_POST['cevap'] ?? '');
        $sira = (int)($_POST['sira'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$soru || !$cevap) $hata = 'Soru ve cevap zorunlu.';
        else {
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO sss (kategori, soru, cevap, sira, aktif) VALUES (?,?,?,?,?)")
                    ->execute([$kategori, $soru, $cevap, $sira, $aktif]);
                denetim_kaydet('sss_eklendi', 'sss', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE sss SET kategori=?, soru=?, cevap=?, sira=?, aktif=? WHERE id=?")
                    ->execute([$kategori, $soru, $cevap, $sira, $aktif, $id]);
                denetim_kaydet('sss_guncellendi', 'sss', $id);
            }
            header('Location: sss.php?b=1'); exit;
        }
    }
}

$veri = ['kategori'=>'Genel', 'soru'=>'', 'cevap'=>'', 'sira'=>0, 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM sss WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) $veri = array_merge($veri, $v); else $islem = 'liste';
}

include 'header.php';
?>
<?php if (!empty($_GET['b'])): ?><div class="alert alert-success">Kaydedildi.</div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle' || $islem === 'duzenle'): ?>
<div class="page-head">
    <h2><?= $islem === 'ekle' ? 'Yeni Soru' : 'Soruyu Düzenle' ?></h2>
    <a href="sss.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-group"><label>Kategori</label><input type="text" name="kategori" class="form-control" value="<?= e($veri['kategori']) ?>" placeholder="Örn: Genel, Teknik, Fiyatlandırma"></div>
    <div class="form-group"><label>Soru *</label><input type="text" name="soru" class="form-control" required value="<?= e($veri['soru']) ?>"></div>
    <div class="form-group"><label>Cevap * (HTML)</label><textarea name="cevap" class="form-control" rows="6" required><?= e($veri['cevap']) ?></textarea></div>
    <div class="form-grid-2">
        <div class="form-group"><label>Sıra</label><input type="number" name="sira" class="form-control" value="<?= (int)$veri['sira'] ?>"></div>
        <div class="form-group form-check" style="margin-top:28px;"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
    </div>
    <div class="form-actions"><a href="sss.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $liste = $pdo->query("SELECT * FROM sss ORDER BY kategori, sira, id")->fetchAll();
?>
<div class="page-head">
    <h2>SSS (<?= count($liste) ?>)</h2>
    <a href="sss.php?islem=ekle" class="btn btn-primary">+ Yeni Soru</a>
</div>
<div class="data-card">
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th>Soru</th><th width="130">Kategori</th><th width="80">Sıra</th><th width="80">Durum</th><th width="120" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $s): ?>
            <tr>
                <td><strong style="color:#fff;"><?= e($s['soru']) ?></strong><br><small style="color:var(--text-3);"><?= e(kisalt(strip_tags($s['cevap']), 100)) ?></small></td>
                <td><span class="tag tag-blue"><?= e($s['kategori']) ?></span></td>
                <td><?= (int)$s['sira'] ?></td>
                <td><span class="tag <?= $s['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $s['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="sss.php?islem=duzenle&id=<?= (int)$s['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <a href="sss.php?islem=sil&id=<?= (int)$s['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div class="empty-state"><h3>SSS yok</h3><br><a href="sss.php?islem=ekle" class="btn btn-primary">+ Ekle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
