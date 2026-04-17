<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Slider Yönetimi';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM slider WHERE id = ?")->execute([$id]);
    denetim_kaydet('slider_silindi', 'slider', $id);
    header('Location: slider.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ust_baslik = trim($_POST['ust_baslik'] ?? '');
        $baslik = trim($_POST['baslik'] ?? '');
        $aciklama = trim($_POST['aciklama'] ?? '');
        $buton_metin = trim($_POST['buton_metin'] ?? '');
        $buton_link = trim($_POST['buton_link'] ?? '');
        $sira = (int)($_POST['sira'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$baslik) $hata = 'Başlık zorunlu.';
        else {
            $gorsel = $_POST['mevcut_gorsel'] ?? null;
            if (!empty($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $y = resim_yukle($_FILES['gorsel'], 'slider');
                if ($y) $gorsel = $y;
            }
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO slider (ust_baslik, baslik, aciklama, gorsel, buton_metin, buton_link, sira, aktif) VALUES (?,?,?,?,?,?,?,?)")
                    ->execute([$ust_baslik, $baslik, $aciklama, $gorsel, $buton_metin, $buton_link, $sira, $aktif]);
                denetim_kaydet('slider_eklendi', 'slider', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE slider SET ust_baslik=?, baslik=?, aciklama=?, gorsel=?, buton_metin=?, buton_link=?, sira=?, aktif=? WHERE id=?")
                    ->execute([$ust_baslik, $baslik, $aciklama, $gorsel, $buton_metin, $buton_link, $sira, $aktif, $id]);
                denetim_kaydet('slider_guncellendi', 'slider', $id);
            }
            header('Location: slider.php?b=1'); exit;
        }
    }
}

$veri = ['ust_baslik'=>'', 'baslik'=>'', 'aciklama'=>'', 'gorsel'=>'', 'buton_metin'=>'', 'buton_link'=>'', 'sira'=>0, 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM slider WHERE id=?");
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
    <h2><?= $islem === 'ekle' ? 'Yeni Slayt' : 'Slayt Düzenle' ?></h2>
    <a href="slider.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid">
        <div>
            <div class="form-group"><label>Üst Başlık (Eyebrow)</label><input type="text" name="ust_baslik" class="form-control" value="<?= e($veri['ust_baslik']) ?>"></div>
            <div class="form-group"><label>Başlık *</label><input type="text" name="baslik" class="form-control" required value="<?= e($veri['baslik']) ?>"></div>
            <div class="form-group"><label>Açıklama</label><textarea name="aciklama" class="form-control" rows="4"><?= e($veri['aciklama']) ?></textarea></div>
            <div class="form-grid-2">
                <div class="form-group"><label>Buton Metni</label><input type="text" name="buton_metin" class="form-control" value="<?= e($veri['buton_metin']) ?>"></div>
                <div class="form-group"><label>Buton Linki</label><input type="text" name="buton_link" class="form-control" placeholder="urunler.php" value="<?= e($veri['buton_link']) ?>"></div>
            </div>
        </div>
        <div>
            <div class="form-group"><label>Arka Plan Görseli</label>
                <input type="file" name="gorsel" accept="image/*" class="form-control" data-preview="prev">
                <input type="hidden" name="mevcut_gorsel" value="<?= e($veri['gorsel']) ?>">
                <img id="prev" class="file-preview" src="<?= e(resim_url($veri['gorsel'])) ?>" style="<?= $veri['gorsel'] ? '' : 'display:none' ?>"></div>
            <div class="form-group"><label>Sıra</label><input type="number" name="sira" class="form-control" value="<?= (int)$veri['sira'] ?>"></div>
            <div class="form-group form-check"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
        </div>
    </div>
    <div class="form-actions"><a href="slider.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $liste = $pdo->query("SELECT * FROM slider ORDER BY sira, id")->fetchAll();
?>
<div class="page-head">
    <h2>Slider (<?= count($liste) ?>)</h2>
    <a href="slider.php?islem=ekle" class="btn btn-primary">+ Yeni Slayt</a>
</div>
<div class="data-card">
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th width="80">Görsel</th><th>Başlık</th><th>Buton</th><th width="80">Sıra</th><th width="80">Durum</th><th width="120" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $s): ?>
            <tr>
                <td><img class="thumb" src="<?= e(resim_url($s['gorsel'])) ?>" alt=""></td>
                <td>
                    <strong style="color:#fff;"><?= e($s['baslik']) ?></strong>
                    <?php if ($s['ust_baslik']): ?><br><small style="color:var(--text-3);"><?= e($s['ust_baslik']) ?></small><?php endif; ?>
                </td>
                <td><?php if ($s['buton_metin']): ?><?= e($s['buton_metin']) ?> → <?= e($s['buton_link']) ?><?php endif; ?></td>
                <td><?= (int)$s['sira'] ?></td>
                <td><span class="tag <?= $s['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $s['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="slider.php?islem=duzenle&id=<?= (int)$s['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <a href="slider.php?islem=sil&id=<?= (int)$s['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div class="empty-state"><h3>Slayt yok</h3><br><a href="slider.php?islem=ekle" class="btn btn-primary">+ Ekle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
