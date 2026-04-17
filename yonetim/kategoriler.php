<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();

$sayfa_baslik = 'Ürün Kategorileri';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM urun_kategoriler WHERE id = ?")->execute([$id]);
    denetim_kaydet('kategori_silindi', 'urun_kategoriler', $id);
    header('Location: kategoriler.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ad = trim($_POST['ad'] ?? '');
        $slug_v = trim($_POST['slug'] ?? '') ?: ($ad ? slug($ad) : '');
        $aciklama = $_POST['aciklama'] ?? '';
        $meta_aciklama = trim($_POST['meta_aciklama'] ?? '');
        $sira = (int)($_POST['sira'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$ad) { $hata = 'Ad zorunlu.'; }
        else {
            $gorsel = $_POST['mevcut_gorsel'] ?? null;
            if (!empty($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $yeni = resim_yukle($_FILES['gorsel'], 'urunler');
                if ($yeni) $gorsel = $yeni;
            }
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO urun_kategoriler (ad, slug, aciklama, gorsel, meta_aciklama, sira, aktif) VALUES (?, ?, ?, ?, ?, ?, ?)")
                    ->execute([$ad, $slug_v, $aciklama, $gorsel, $meta_aciklama, $sira, $aktif]);
                denetim_kaydet('kategori_eklendi', 'urun_kategoriler', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE urun_kategoriler SET ad=?, slug=?, aciklama=?, gorsel=?, meta_aciklama=?, sira=?, aktif=? WHERE id=?")
                    ->execute([$ad, $slug_v, $aciklama, $gorsel, $meta_aciklama, $sira, $aktif, $id]);
                denetim_kaydet('kategori_guncellendi', 'urun_kategoriler', $id);
            }
            header('Location: kategoriler.php?b=1'); exit;
        }
    }
}

$veri = ['ad' => '', 'slug' => '', 'aciklama' => '', 'gorsel' => '', 'meta_aciklama' => '', 'sira' => 0, 'aktif' => 1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM urun_kategoriler WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) $veri = array_merge($veri, $v);
    else { $islem = 'liste'; }
}

include 'header.php';
?>

<?php if (!empty($_GET['b'])): ?><div class="alert alert-success">Kaydedildi.</div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle' || $islem === 'duzenle'): ?>
<div class="page-head">
    <h2><?= $islem === 'ekle' ? 'Yeni Kategori' : 'Kategori Düzenle' ?></h2>
    <a href="kategoriler.php" class="btn btn-outline">← Geri</a>
</div>

<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid">
        <div>
            <div class="form-group">
                <label>Kategori Adı *</label>
                <input type="text" id="ad" name="ad" class="form-control" required value="<?= e($veri['ad']) ?>" data-slug-target="slug">
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" value="<?= e($veri['slug']) ?>" data-slug-source>
            </div>
            <div class="form-group">
                <label>Açıklama (HTML)</label>
                <textarea name="aciklama" class="form-control" rows="8"><?= e($veri['aciklama']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Meta Açıklama</label>
                <textarea name="meta_aciklama" class="form-control" rows="2"><?= e($veri['meta_aciklama']) ?></textarea>
            </div>
        </div>
        <div>
            <div class="form-group">
                <label>Görsel</label>
                <input type="file" name="gorsel" accept="image/*" class="form-control" data-preview="prev">
                <input type="hidden" name="mevcut_gorsel" value="<?= e($veri['gorsel']) ?>">
                <img id="prev" class="file-preview" src="<?= e(resim_url($veri['gorsel'])) ?>" style="<?= $veri['gorsel'] ? '' : 'display:none' ?>">
            </div>
            <div class="form-group">
                <label>Sıra</label>
                <input type="number" name="sira" class="form-control" value="<?= (int)$veri['sira'] ?>">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>>
                <label for="aktif" style="margin:0;">Aktif</label>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <a href="kategoriler.php" class="btn btn-outline">İptal</a>
        <button class="btn btn-primary">Kaydet</button>
    </div>
</form>

<?php else:
    $kategoriler = $pdo->query("SELECT k.*, (SELECT COUNT(*) FROM urunler WHERE kategori_id=k.id) AS urun_sayisi FROM urun_kategoriler k ORDER BY k.sira, k.id")->fetchAll();
?>

<div class="page-head">
    <h2>Kategoriler (<?= count($kategoriler) ?>)</h2>
    <a href="kategoriler.php?islem=ekle" class="btn btn-primary">+ Yeni Kategori</a>
</div>

<div class="data-card">
    <?php if ($kategoriler): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr>
            <th width="60">Görsel</th><th>Ad</th><th>Slug</th><th width="100">Ürün</th><th width="80">Sıra</th><th width="80">Durum</th><th width="140" class="actions-cell">İşlem</th>
        </tr></thead>
        <tbody>
            <?php foreach ($kategoriler as $k): ?>
            <tr>
                <td><img class="thumb" src="<?= e(resim_url($k['gorsel'])) ?>" alt=""></td>
                <td><strong style="color:#fff;"><?= e($k['ad']) ?></strong></td>
                <td><code style="background:var(--bg); padding:2px 6px; border-radius:4px; font-size:11px; color:var(--text-2);"><?= e($k['slug']) ?></code></td>
                <td><?= (int)$k['urun_sayisi'] ?></td>
                <td><?= (int)$k['sira'] ?></td>
                <td><span class="tag <?= $k['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $k['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="kategoriler.php?islem=duzenle&id=<?= (int)$k['id'] ?>" title="Düzenle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg>
                    </a>
                    <a href="kategoriler.php?islem=sil&id=<?= (int)$k['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Kategoriyi silmek istediğinize emin misiniz? Ürünlerin kategorisi boşalır.">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6V20A2 2 0 0 1 17 22H7A2 2 0 0 1 5 20V6"/></svg>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <h3>Kategori yok</h3>
        <p>İlk kategoriyi ekleyin.</p>
        <br><a href="kategoriler.php?islem=ekle" class="btn btn-primary">+ Ekle</a>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>
