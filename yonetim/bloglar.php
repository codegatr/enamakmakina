<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
$sayfa_baslik = 'Blog Yönetimi';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    $pdo->prepare("DELETE FROM bloglar WHERE id = ?")->execute([$id]);
    denetim_kaydet('blog_silindi', 'bloglar', $id);
    header('Location: bloglar.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $baslik = trim($_POST['baslik'] ?? '');
        $slug_v = trim($_POST['slug'] ?? '') ?: ($baslik ? slug($baslik) : '');
        $kategori = trim($_POST['kategori'] ?? '');
        $yazar = trim($_POST['yazar'] ?? $_SESSION['admin_ad_soyad'] ?: 'Enamak Makina');
        $ozet = trim($_POST['ozet'] ?? '');
        $icerik = $_POST['icerik'] ?? '';
        $meta_baslik = trim($_POST['meta_baslik'] ?? '');
        $meta_aciklama = trim($_POST['meta_aciklama'] ?? '');
        $yayin_tarihi = $_POST['yayin_tarihi'] ?: date('Y-m-d H:i:s');
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$baslik) $hata = 'Başlık zorunlu.';
        else {
            $gorsel = $_POST['mevcut_gorsel'] ?? null;
            if (!empty($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $y = resim_yukle($_FILES['gorsel'], 'blog');
                if ($y) $gorsel = $y;
            }
            if ($islem === 'ekle') {
                $pdo->prepare("INSERT INTO bloglar (baslik, slug, kategori, yazar, ozet, icerik, gorsel, meta_baslik, meta_aciklama, yayin_tarihi, aktif) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
                    ->execute([$baslik, $slug_v, $kategori, $yazar, $ozet, $icerik, $gorsel, $meta_baslik, $meta_aciklama, $yayin_tarihi, $aktif]);
                denetim_kaydet('blog_eklendi', 'bloglar', (int)$pdo->lastInsertId());
            } else {
                $pdo->prepare("UPDATE bloglar SET baslik=?, slug=?, kategori=?, yazar=?, ozet=?, icerik=?, gorsel=?, meta_baslik=?, meta_aciklama=?, yayin_tarihi=?, aktif=? WHERE id=?")
                    ->execute([$baslik, $slug_v, $kategori, $yazar, $ozet, $icerik, $gorsel, $meta_baslik, $meta_aciklama, $yayin_tarihi, $aktif, $id]);
                denetim_kaydet('blog_guncellendi', 'bloglar', $id);
            }
            header('Location: bloglar.php?b=1'); exit;
        }
    }
}

$veri = ['baslik'=>'', 'slug'=>'', 'kategori'=>'', 'yazar'=>$_SESSION['admin_ad_soyad'] ?: '', 'ozet'=>'', 'icerik'=>'', 'gorsel'=>'', 'meta_baslik'=>'', 'meta_aciklama'=>'', 'yayin_tarihi'=>date('Y-m-d\TH:i'), 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM bloglar WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) {
        $veri = array_merge($veri, $v);
        $veri['yayin_tarihi'] = $v['yayin_tarihi'] ? date('Y-m-d\TH:i', strtotime($v['yayin_tarihi'])) : date('Y-m-d\TH:i');
    } else $islem = 'liste';
}

include 'header.php';
?>
<?php if (!empty($_GET['b'])): ?><div class="alert alert-success">Kaydedildi.</div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle' || $islem === 'duzenle'): ?>
<div class="page-head">
    <h2><?= $islem === 'ekle' ? 'Yeni Yazı' : 'Yazıyı Düzenle' ?></h2>
    <a href="bloglar.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid">
        <div>
            <div class="form-group"><label>Başlık *</label><input type="text" id="baslik" name="baslik" class="form-control" required value="<?= e($veri['baslik']) ?>" data-slug-target="slug"></div>
            <div class="form-group"><label>Slug</label><input type="text" id="slug" name="slug" class="form-control" value="<?= e($veri['slug']) ?>" data-slug-source></div>
            <div class="form-grid-2">
                <div class="form-group"><label>Kategori</label><input type="text" name="kategori" class="form-control" value="<?= e($veri['kategori']) ?>"></div>
                <div class="form-group"><label>Yazar</label><input type="text" name="yazar" class="form-control" value="<?= e($veri['yazar']) ?>"></div>
            </div>
            <div class="form-group"><label>Özet</label><textarea name="ozet" class="form-control" rows="3"><?= e($veri['ozet']) ?></textarea></div>
            <div class="form-group"><label>İçerik (HTML)</label><textarea name="icerik" class="form-control" rows="15"><?= e($veri['icerik']) ?></textarea></div>
            <h3 style="margin:20px 0 12px; color:#fff; font-size:15px;">SEO</h3>
            <div class="form-group"><label>Meta Başlık</label><input type="text" name="meta_baslik" class="form-control" value="<?= e($veri['meta_baslik']) ?>"></div>
            <div class="form-group"><label>Meta Açıklama</label><textarea name="meta_aciklama" class="form-control" rows="2"><?= e($veri['meta_aciklama']) ?></textarea></div>
        </div>
        <div>
            <div class="form-group"><label>Kapak Görseli</label>
                <input type="file" name="gorsel" accept="image/*" class="form-control" data-preview="prev">
                <input type="hidden" name="mevcut_gorsel" value="<?= e($veri['gorsel']) ?>">
                <img id="prev" class="file-preview" src="<?= e(resim_url($veri['gorsel'])) ?>" style="<?= $veri['gorsel'] ? '' : 'display:none' ?>"></div>
            <div class="form-group"><label>Yayın Tarihi</label><input type="datetime-local" name="yayin_tarihi" class="form-control" value="<?= e($veri['yayin_tarihi']) ?>"></div>
            <div class="form-group form-check"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Yayında</label></div>
        </div>
    </div>
    <div class="form-actions"><a href="bloglar.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $q = trim($_GET['q'] ?? '');
    $sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
    $per = 20;
    $where = "1=1"; $params = [];
    if ($q) { $where .= " AND baslik LIKE :q"; $params[':q'] = "%$q%"; }
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bloglar WHERE $where");
    $stmt->execute($params);
    $toplam = (int)$stmt->fetchColumn();
    $offset = ($sayfa-1)*$per;
    $stmt = $pdo->prepare("SELECT * FROM bloglar WHERE $where ORDER BY yayin_tarihi DESC LIMIT " . (int)$per . " OFFSET " . (int)$offset);
    $stmt->execute($params);
    $liste = $stmt->fetchAll();
?>
<div class="page-head">
    <h2>Blog Yazıları (<?= $toplam ?>)</h2>
    <a href="bloglar.php?islem=ekle" class="btn btn-primary">+ Yeni Yazı</a>
</div>
<div class="data-card">
    <div class="data-card-head">
        <form method="get" class="data-search">
            <input type="text" name="q" placeholder="Yazı ara..." value="<?= e($q) ?>">
            <button class="btn btn-primary btn-sm">Ara</button>
        </form>
    </div>
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th width="60">Kapak</th><th>Başlık</th><th>Kategori</th><th>Yayın</th><th width="80">Durum</th><th width="120" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $b): ?>
            <tr>
                <td><img class="thumb" src="<?= e(resim_url($b['gorsel'])) ?>" alt=""></td>
                <td><strong style="color:#fff;"><?= e($b['baslik']) ?></strong><br><small style="color:var(--text-3);"><?= e($b['yazar']) ?></small></td>
                <td><?= e($b['kategori'] ?: '—') ?></td>
                <td><?= tr_tarih($b['yayin_tarihi']) ?></td>
                <td><span class="tag <?= $b['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $b['aktif'] ? 'Yayında' : 'Taslak' ?></span></td>
                <td class="actions-cell">
                    <a href="../blog-detay.php?slug=<?= e($b['slug']) ?>" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg></a>
                    <a href="bloglar.php?islem=duzenle&id=<?= (int)$b['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <a href="bloglar.php?islem=sil&id=<?= (int)$b['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Yazıyı silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?= sayfalama($toplam, $per, $sayfa, 'bloglar.php?' . ($q ? 'q='.urlencode($q).'&' : '') . 'sayfa=%d') ?>
    <?php else: ?>
        <div class="empty-state"><h3>Blog yazısı yok</h3><br><a href="bloglar.php?islem=ekle" class="btn btn-primary">+ İlk Yazıyı Ekle</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
