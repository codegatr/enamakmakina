<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Statik Sayfalar';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM sayfalar WHERE id = ?")->execute([$id]);
    denetim_kaydet('sayfa_silindi', 'sayfalar', $id);
    header('Location: sayfalar.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $baslik = trim($_POST['baslik'] ?? '');
        $slug_v = trim($_POST['slug'] ?? '') ?: ($baslik ? slug($baslik) : '');
        $icerik = $_POST['icerik'] ?? '';
        $meta_baslik = trim($_POST['meta_baslik'] ?? '');
        $meta_aciklama = trim($_POST['meta_aciklama'] ?? '');
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$baslik) $hata = 'Başlık zorunlu.';
        else {
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO sayfalar (baslik, slug, icerik, meta_baslik, meta_aciklama, aktif) VALUES (?,?,?,?,?,?)")
                    ->execute([$baslik, $slug_v, $icerik, $meta_baslik, $meta_aciklama, $aktif]);
                denetim_kaydet('sayfa_eklendi', 'sayfalar', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE sayfalar SET baslik=?, slug=?, icerik=?, meta_baslik=?, meta_aciklama=?, aktif=? WHERE id=?")
                    ->execute([$baslik, $slug_v, $icerik, $meta_baslik, $meta_aciklama, $aktif, $id]);
                denetim_kaydet('sayfa_guncellendi', 'sayfalar', $id);
            }
            header('Location: sayfalar.php?b=1'); exit;
        }
    }
}

$veri = ['baslik'=>'', 'slug'=>'', 'icerik'=>'', 'meta_baslik'=>'', 'meta_aciklama'=>'', 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM sayfalar WHERE id=?");
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
    <h2><?= $islem === 'ekle' ? 'Yeni Sayfa' : 'Sayfa Düzenle' ?></h2>
    <a href="sayfalar.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-group"><label>Başlık *</label><input type="text" id="baslik" name="baslik" class="form-control" required value="<?= e($veri['baslik']) ?>" data-slug-target="slug"></div>
    <div class="form-group"><label>Slug</label><input type="text" id="slug" name="slug" class="form-control" value="<?= e($veri['slug']) ?>" data-slug-source>
    <small style="color:var(--text-3); font-size:12px;">Sayfa URL'si: /sayfa.php?slug=<?= e($veri['slug'] ?: 'ornek') ?></small></div>
    <div class="form-group"><label>İçerik (HTML)</label><textarea name="icerik" class="form-control" rows="20"><?= e($veri['icerik']) ?></textarea></div>
    <div class="form-grid-2">
        <div class="form-group"><label>Meta Başlık</label><input type="text" name="meta_baslik" class="form-control" value="<?= e($veri['meta_baslik']) ?>"></div>
        <div class="form-group"><label>Meta Açıklama</label><input type="text" name="meta_aciklama" class="form-control" value="<?= e($veri['meta_aciklama']) ?>"></div>
    </div>
    <div class="form-group form-check"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
    <div class="form-actions"><a href="sayfalar.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $liste = $pdo->query("SELECT * FROM sayfalar ORDER BY baslik ASC")->fetchAll();
?>
<div class="page-head">
    <h2>Statik Sayfalar (<?= count($liste) ?>)</h2>
    <a href="sayfalar.php?islem=ekle" class="btn btn-primary">+ Yeni Sayfa</a>
</div>
<div class="data-card">
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th>Başlık</th><th>Slug</th><th width="80">Durum</th><th width="140" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $s): ?>
            <tr>
                <td><strong style="color:#fff;"><?= e($s['baslik']) ?></strong></td>
                <td><code style="background:var(--bg); padding:2px 6px; border-radius:4px; font-size:11px;"><?= e($s['slug']) ?></code></td>
                <td><span class="tag <?= $s['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $s['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="../sayfa.php?slug=<?= e($s['slug']) ?>" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg></a>
                    <a href="sayfalar.php?islem=duzenle&id=<?= (int)$s['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <a href="sayfalar.php?islem=sil&id=<?= (int)$s['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Sayfayı silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div class="empty-state"><h3>Sayfa yok</h3><br><a href="sayfalar.php?islem=ekle" class="btn btn-primary">+ Ekle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
