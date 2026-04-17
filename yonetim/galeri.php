<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Galeri';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM galeri WHERE id = ?")->execute([$id]);
    denetim_kaydet('galeri_silindi', 'galeri', $id);
    header('Location: galeri.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $baslik = trim($_POST['baslik'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $sira = (int)($_POST['sira'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if ($islem === 'ekle') {
            // Çoklu dosya yükle
            if (!empty($_FILES['gorseller']) && is_array($_FILES['gorseller']['tmp_name'])) {
                $eklenen = 0;
                foreach ($_FILES['gorseller']['tmp_name'] as $k => $tmp) {
                    if ($_FILES['gorseller']['error'][$k] === UPLOAD_ERR_OK) {
                        $dosya = ['name' => $_FILES['gorseller']['name'][$k], 'tmp_name' => $tmp, 'error' => 0, 'size' => $_FILES['gorseller']['size'][$k]];
                        $y = resim_yukle($dosya, 'galeri');
                        if ($y) {
                            $pdo->prepare("INSERT INTO galeri (baslik, kategori, gorsel, sira, aktif) VALUES (?,?,?,?,?)")
                                ->execute([$baslik ?: 'Galeri', $kategori, $y, $sira, $aktif]);
                            $eklenen++;
                        }
                    }
                }
                denetim_kaydet('galeri_toplu_eklendi', 'galeri');
                header("Location: galeri.php?b=$eklenen"); exit;
            } else {
                $hata = 'Lütfen en az bir görsel seçin.';
            }
        } else {
            $gorsel = $_POST['mevcut_gorsel'] ?? null;
            if (!empty($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $y = resim_yukle($_FILES['gorsel'], 'galeri');
                if ($y) $gorsel = $y;
            }
            $pdo->prepare("UPDATE galeri SET baslik=?, kategori=?, gorsel=?, sira=?, aktif=? WHERE id=?")
                ->execute([$baslik, $kategori, $gorsel, $sira, $aktif, $id]);
            denetim_kaydet('galeri_guncellendi', 'galeri', $id);
            header('Location: galeri.php?b=1'); exit;
        }
    }
}

$veri = ['baslik'=>'', 'kategori'=>'', 'gorsel'=>'', 'sira'=>0, 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) $veri = array_merge($veri, $v); else $islem = 'liste';
}

include 'header.php';
?>
<?php if (!empty($_GET['b'])): ?><div class="alert alert-success"><?= (int)$_GET['b'] > 1 ? $_GET['b'] . ' görsel eklendi.' : 'Kaydedildi.' ?></div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle'): ?>
<div class="page-head">
    <h2>Galeriye Görsel Ekle</h2>
    <a href="galeri.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid-2">
        <div class="form-group"><label>Başlık (opsiyonel)</label><input type="text" name="baslik" class="form-control"></div>
        <div class="form-group"><label>Kategori (opsiyonel)</label><input type="text" name="kategori" class="form-control"></div>
    </div>
    <div class="form-group"><label>Görseller * (birden fazla seçebilirsiniz)</label>
        <input type="file" name="gorseller[]" accept="image/*" multiple class="form-control" required>
    </div>
    <div class="form-grid-2">
        <div class="form-group"><label>Sıra</label><input type="number" name="sira" class="form-control" value="0"></div>
        <div class="form-group form-check" style="margin-top:28px;"><input type="checkbox" id="aktif" name="aktif" value="1" checked><label for="aktif" style="margin:0;">Aktif</label></div>
    </div>
    <div class="form-actions"><a href="galeri.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Yükle</button></div>
</form>

<?php elseif ($islem === 'duzenle'): ?>
<div class="page-head">
    <h2>Galeri Düzenle</h2>
    <a href="galeri.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-group"><label>Başlık</label><input type="text" name="baslik" class="form-control" value="<?= e($veri['baslik']) ?>"></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="kategori" class="form-control" value="<?= e($veri['kategori']) ?>"></div>
    <div class="form-group"><label>Görsel</label>
        <input type="file" name="gorsel" accept="image/*" class="form-control" data-preview="prev">
        <input type="hidden" name="mevcut_gorsel" value="<?= e($veri['gorsel']) ?>">
        <img id="prev" class="file-preview" src="<?= e(resim_url($veri['gorsel'])) ?>"></div>
    <div class="form-grid-2">
        <div class="form-group"><label>Sıra</label><input type="number" name="sira" class="form-control" value="<?= (int)$veri['sira'] ?>"></div>
        <div class="form-group form-check" style="margin-top:28px;"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
    </div>
    <div class="form-actions"><a href="galeri.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>

<?php else:
    $liste = $pdo->query("SELECT * FROM galeri ORDER BY sira, id DESC")->fetchAll();
?>
<div class="page-head">
    <h2>Galeri (<?= count($liste) ?> görsel)</h2>
    <a href="galeri.php?islem=ekle" class="btn btn-primary">+ Görsel Yükle</a>
</div>
<div class="data-card" style="padding:20px;">
    <?php if ($liste): ?>
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap:14px;">
        <?php foreach ($liste as $g): ?>
            <div style="position:relative; border:1px solid var(--border); border-radius:10px; overflow:hidden; background:var(--bg);">
                <img src="<?= e(resim_url($g['gorsel'])) ?>" style="width:100%; height:150px; object-fit:cover; display:block;">
                <div style="padding:8px 10px; font-size:12px;">
                    <?php if ($g['baslik']): ?><div style="color:var(--text); font-weight:600;"><?= e(kisalt($g['baslik'], 30)) ?></div><?php endif; ?>
                    <?php if ($g['kategori']): ?><small style="color:var(--text-3);"><?= e($g['kategori']) ?></small><?php endif; ?>
                </div>
                <div style="display:flex; gap:4px; padding:0 10px 10px;">
                    <a href="galeri.php?islem=duzenle&id=<?= (int)$g['id'] ?>" class="btn btn-sm btn-outline" style="flex:1;">Düzenle</a>
                    <a href="galeri.php?islem=sil&id=<?= (int)$g['id'] ?>&t=<?= e(csrf_token()) ?>" class="btn btn-sm btn-danger" data-sil="Silmek istediğinize emin misiniz?">Sil</a>
                </div>
                <?php if (!$g['aktif']): ?><span class="tag tag-red" style="position:absolute; top:8px; right:8px;">Pasif</span><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="empty-state"><h3>Galeri boş</h3><br><a href="galeri.php?islem=ekle" class="btn btn-primary">+ İlk Görseli Yükle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
