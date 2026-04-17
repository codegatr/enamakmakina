<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Referanslar';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM referanslar WHERE id = ?")->execute([$id]);
    denetim_kaydet('referans_silindi', 'referanslar', $id);
    header('Location: referanslar.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $firma_adi = trim($_POST['firma_adi'] ?? '');
        $sektor = trim($_POST['sektor'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $aciklama = trim($_POST['aciklama'] ?? '');
        $sira = (int)($_POST['sira'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$firma_adi) $hata = 'Firma adı zorunlu.';
        else {
            $logo = $_POST['mevcut_logo'] ?? null;
            if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $y = resim_yukle($_FILES['logo'], 'referanslar');
                if ($y) $logo = $y;
            }
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO referanslar (firma_adi, sektor, website, aciklama, logo, sira, aktif) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$firma_adi, $sektor, $website, $aciklama, $logo, $sira, $aktif]);
                denetim_kaydet('referans_eklendi', 'referanslar', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE referanslar SET firma_adi=?, sektor=?, website=?, aciklama=?, logo=?, sira=?, aktif=? WHERE id=?")
                    ->execute([$firma_adi, $sektor, $website, $aciklama, $logo, $sira, $aktif, $id]);
                denetim_kaydet('referans_guncellendi', 'referanslar', $id);
            }
            header('Location: referanslar.php?b=1'); exit;
        }
    }
}

$veri = ['firma_adi'=>'', 'sektor'=>'', 'website'=>'', 'aciklama'=>'', 'logo'=>'', 'sira'=>0, 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM referanslar WHERE id=?");
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
    <h2><?= $islem === 'ekle' ? 'Yeni Referans' : 'Referans Düzenle' ?></h2>
    <a href="referanslar.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid">
        <div>
            <div class="form-group"><label>Firma Adı *</label><input type="text" name="firma_adi" class="form-control" required value="<?= e($veri['firma_adi']) ?>"></div>
            <div class="form-grid-2">
                <div class="form-group"><label>Sektör</label><input type="text" name="sektor" class="form-control" value="<?= e($veri['sektor']) ?>"></div>
                <div class="form-group"><label>Website</label><input type="url" name="website" class="form-control" value="<?= e($veri['website']) ?>"></div>
            </div>
            <div class="form-group"><label>Açıklama</label><textarea name="aciklama" class="form-control" rows="5"><?= e($veri['aciklama']) ?></textarea></div>
        </div>
        <div>
            <div class="form-group"><label>Logo</label>
                <input type="file" name="logo" accept="image/*" class="form-control" data-preview="prev">
                <input type="hidden" name="mevcut_logo" value="<?= e($veri['logo']) ?>">
                <img id="prev" class="file-preview" src="<?= e(resim_url($veri['logo'])) ?>" style="<?= $veri['logo'] ? '' : 'display:none' ?>"></div>
            <div class="form-group"><label>Sıra</label><input type="number" name="sira" class="form-control" value="<?= (int)$veri['sira'] ?>"></div>
            <div class="form-group form-check"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
        </div>
    </div>
    <div class="form-actions"><a href="referanslar.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $liste = $pdo->query("SELECT * FROM referanslar ORDER BY sira, firma_adi")->fetchAll();
?>
<div class="page-head">
    <h2>Referanslar (<?= count($liste) ?>)</h2>
    <a href="referanslar.php?islem=ekle" class="btn btn-primary">+ Yeni Referans</a>
</div>
<div class="data-card">
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th width="80">Logo</th><th>Firma</th><th>Sektör</th><th>Website</th><th width="80">Sıra</th><th width="80">Durum</th><th width="100" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $r): ?>
            <tr>
                <td><img class="thumb" src="<?= e(resim_url($r['logo'] ?: '/assets/img/placeholder-logo.svg')) ?>" alt="" style="object-fit:contain; background:#fff; padding:3px;"></td>
                <td><strong style="color:var(--text);"><?= e($r['firma_adi']) ?></strong></td>
                <td><?= e($r['sektor'] ?: '—') ?></td>
                <td><?php if ($r['website']): ?><a href="<?= e($r['website']) ?>" target="_blank" style="color:var(--text-2);"><?= e($r['website']) ?></a><?php endif; ?></td>
                <td><?= (int)$r['sira'] ?></td>
                <td><span class="tag <?= $r['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $r['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="referanslar.php?islem=duzenle&id=<?= (int)$r['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <a href="referanslar.php?islem=sil&id=<?= (int)$r['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Referansı silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div class="empty-state"><h3>Referans yok</h3><br><a href="referanslar.php?islem=ekle" class="btn btn-primary">+ Ekle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
