<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Hizmet Yönetimi';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM hizmetler WHERE id = ?")->execute([$id]);
    denetim_kaydet('hizmet_silindi', 'hizmetler', $id);
    header('Location: hizmetler.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ad = trim($_POST['ad'] ?? '');
        $slug_v = trim($_POST['slug'] ?? '') ?: ($ad ? slug($ad) : '');
        $kisa = trim($_POST['kisa_aciklama'] ?? '');
        $aciklama = $_POST['aciklama'] ?? '';
        $ikon = $_POST['ikon'] ?? '';
        $meta_baslik = trim($_POST['meta_baslik'] ?? '');
        $meta_aciklama = trim($_POST['meta_aciklama'] ?? '');
        $sira = (int)($_POST['sira'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$ad) $hata = 'Ad zorunlu.';
        else {
            $gorsel = $_POST['mevcut_gorsel'] ?? null;
            if (!empty($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $y = resim_yukle($_FILES['gorsel'], 'hizmetler');
                if ($y) $gorsel = $y;
            }
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO hizmetler (ad, slug, kisa_aciklama, aciklama, ikon, gorsel, meta_baslik, meta_aciklama, sira, aktif) VALUES (?,?,?,?,?,?,?,?,?,?)")
                    ->execute([$ad, $slug_v, $kisa, $aciklama, $ikon, $gorsel, $meta_baslik, $meta_aciklama, $sira, $aktif]);
                denetim_kaydet('hizmet_eklendi', 'hizmetler', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE hizmetler SET ad=?, slug=?, kisa_aciklama=?, aciklama=?, ikon=?, gorsel=?, meta_baslik=?, meta_aciklama=?, sira=?, aktif=? WHERE id=?")
                    ->execute([$ad, $slug_v, $kisa, $aciklama, $ikon, $gorsel, $meta_baslik, $meta_aciklama, $sira, $aktif, $id]);
                denetim_kaydet('hizmet_guncellendi', 'hizmetler', $id);
            }
            header('Location: hizmetler.php?b=1'); exit;
        }
    }
}

$veri = ['ad'=>'', 'slug'=>'', 'kisa_aciklama'=>'', 'aciklama'=>'', 'ikon'=>'', 'gorsel'=>'', 'meta_baslik'=>'', 'meta_aciklama'=>'', 'sira'=>0, 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM hizmetler WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) $veri = array_merge($veri, $v);
    else $islem = 'liste';
}

include 'header.php';
?>

<?php if (!empty($_GET['b'])): ?><div class="alert alert-success">Kaydedildi.</div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle' || $islem === 'duzenle'): ?>
<div class="page-head">
    <h2><?= $islem === 'ekle' ? 'Yeni Hizmet' : 'Hizmet Düzenle' ?></h2>
    <a href="hizmetler.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid">
        <div>
            <div class="form-group"><label>Hizmet Adı *</label>
                <input type="text" id="ad" name="ad" class="form-control" required value="<?= e($veri['ad']) ?>" data-slug-target="slug"></div>
            <div class="form-group"><label>Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" value="<?= e($veri['slug']) ?>" data-slug-source></div>
            <div class="form-group"><label>Kısa Açıklama</label>
                <textarea name="kisa_aciklama" class="form-control" rows="3"><?= e($veri['kisa_aciklama']) ?></textarea></div>
            <div class="form-group"><label>Detaylı Açıklama (HTML)</label>
                <textarea name="aciklama" class="form-control" rows="10"><?= e($veri['aciklama']) ?></textarea></div>
            <div class="form-group"><label>SVG İkon (opsiyonel, HTML)</label>
                <textarea name="ikon" class="form-control" rows="3" placeholder='<svg>...</svg>'><?= e($veri['ikon']) ?></textarea></div>
            <h3 style="margin:20px 0 12px; color:var(--text);">SEO</h3>
            <div class="form-group"><label>Meta Başlık</label>
                <input type="text" name="meta_baslik" class="form-control" value="<?= e($veri['meta_baslik']) ?>"></div>
            <div class="form-group"><label>Meta Açıklama</label>
                <textarea name="meta_aciklama" class="form-control" rows="2"><?= e($veri['meta_aciklama']) ?></textarea></div>
        </div>
        <div>
            <div class="form-group"><label>Görsel</label>
                <input type="file" name="gorsel" accept="image/*" class="form-control" data-preview="prev">
                <input type="hidden" name="mevcut_gorsel" value="<?= e($veri['gorsel']) ?>">
                <img id="prev" class="file-preview" src="<?= e(resim_url($veri['gorsel'])) ?>" style="<?= $veri['gorsel'] ? '' : 'display:none' ?>"></div>
            <div class="form-group"><label>Sıra</label><input type="number" name="sira" class="form-control" value="<?= (int)$veri['sira'] ?>"></div>
            <div class="form-group form-check"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
        </div>
    </div>
    <div class="form-actions"><a href="hizmetler.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $liste = $pdo->query("SELECT * FROM hizmetler ORDER BY sira, id")->fetchAll();
?>
<div class="page-head">
    <h2>Hizmetler (<?= count($liste) ?>)</h2>
    <a href="hizmetler.php?islem=ekle" class="btn btn-primary">+ Yeni Hizmet</a>
</div>
<div class="data-card">
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th width="60">Görsel</th><th>Ad</th><th>Slug</th><th width="80">Sıra</th><th width="80">Durum</th><th width="120" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $h): ?>
            <tr>
                <td><img class="thumb" src="<?= e(resim_url($h['gorsel'])) ?>" alt=""></td>
                <td><strong style="color:var(--text);"><?= e($h['ad']) ?></strong></td>
                <td><code style="background:var(--bg); padding:2px 6px; border-radius:4px; font-size:11px;"><?= e($h['slug']) ?></code></td>
                <td><?= (int)$h['sira'] ?></td>
                <td><span class="tag <?= $h['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $h['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="hizmetler.php?islem=duzenle&id=<?= (int)$h['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <a href="hizmetler.php?islem=sil&id=<?= (int)$h['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Hizmeti silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div class="empty-state"><h3>Hizmet yok</h3><br><a href="hizmetler.php?islem=ekle" class="btn btn-primary">+ Ekle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
