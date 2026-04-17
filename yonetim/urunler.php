<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();

$sayfa_baslik = 'Ürün Yönetimi';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';
$basarili = '';

// Silme
if ($islem === 'sil' && $id) {
    if (csrf_dogrula($_GET['t'] ?? '')) {
        $pdo->prepare("DELETE FROM urunler WHERE id = ?")->execute([$id]);
        denetim_kaydet('urun_silindi', 'urunler', $id);
        header('Location: urunler.php?s=1');
        exit;
    }
}

// Toggle aktif
if ($islem === 'toggle' && $id) {
    if (csrf_dogrula($_GET['t'] ?? '')) {
        $pdo->prepare("UPDATE urunler SET aktif = IF(aktif=1,0,1) WHERE id = ?")->execute([$id]);
        header('Location: urunler.php');
        exit;
    }
}

// Kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ad = trim($_POST['ad'] ?? '');
        $slug_v = trim($_POST['slug'] ?? '');
        if ($slug_v === '' && $ad) $slug_v = slug($ad);
        $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
        $model_kodu = trim($_POST['model_kodu'] ?? '');
        $kisa_aciklama = trim($_POST['kisa_aciklama'] ?? '');
        $aciklama = $_POST['aciklama'] ?? '';
        $teknik_ozellikler = trim($_POST['teknik_ozellikler'] ?? '');
        $meta_baslik = trim($_POST['meta_baslik'] ?? '');
        $meta_aciklama = trim($_POST['meta_aciklama'] ?? '');
        $meta_anahtar = trim($_POST['meta_anahtar'] ?? '');
        $one_cikan = isset($_POST['one_cikan']) ? 1 : 0;
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        $sira = (int)($_POST['sira'] ?? 0);

        if (!$ad) {
            $hata = 'Ürün adı gerekli.';
        } else {
            // Görsel yükle
            $gorsel = $_POST['mevcut_gorsel'] ?? null;
            if (!empty($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $yeni = resim_yukle($_FILES['gorsel'], 'urunler');
                if ($yeni) $gorsel = $yeni;
            }

            // Galeri
            $galeri = [];
            if (!empty($_FILES['galeri']) && is_array($_FILES['galeri']['tmp_name'])) {
                foreach ($_FILES['galeri']['tmp_name'] as $k => $tmp) {
                    if ($_FILES['galeri']['error'][$k] === UPLOAD_ERR_OK) {
                        $dosya = [
                            'name' => $_FILES['galeri']['name'][$k],
                            'tmp_name' => $tmp,
                            'error' => 0,
                            'size' => $_FILES['galeri']['size'][$k],
                        ];
                        $y = resim_yukle($dosya, 'urunler');
                        if ($y) $galeri[] = $y;
                    }
                }
            }
            // Mevcut galeri varsa ekle
            if (!empty($_POST['mevcut_galeri'])) {
                $mevcut = json_decode($_POST['mevcut_galeri'], true);
                if (is_array($mevcut)) $galeri = array_merge($mevcut, $galeri);
            }
            $galeri_json = $galeri ? json_encode(array_values(array_filter($galeri))) : null;

            if ($islem === 'ekle') {
                $stmt = $pdo->prepare("INSERT INTO urunler
                    (ad, slug, kategori_id, model_kodu, kisa_aciklama, aciklama, teknik_ozellikler, gorsel, galeri, meta_baslik, meta_aciklama, meta_anahtar, one_cikan, aktif, sira)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$ad, $slug_v, $kategori_id, $model_kodu, $kisa_aciklama, $aciklama, $teknik_ozellikler, $gorsel, $galeri_json, $meta_baslik, $meta_aciklama, $meta_anahtar, $one_cikan, $aktif, $sira]);
                $id = (int)$pdo->lastInsertId();
                denetim_kaydet('urun_eklendi', 'urunler', $id);
                header('Location: urunler.php?e=1');
                exit;
            } else {
                $stmt = $pdo->prepare("UPDATE urunler SET
                    ad=?, slug=?, kategori_id=?, model_kodu=?, kisa_aciklama=?, aciklama=?, teknik_ozellikler=?, gorsel=?, galeri=?,
                    meta_baslik=?, meta_aciklama=?, meta_anahtar=?, one_cikan=?, aktif=?, sira=?
                    WHERE id=?");
                $stmt->execute([$ad, $slug_v, $kategori_id, $model_kodu, $kisa_aciklama, $aciklama, $teknik_ozellikler, $gorsel, $galeri_json, $meta_baslik, $meta_aciklama, $meta_anahtar, $one_cikan, $aktif, $sira, $id]);
                denetim_kaydet('urun_guncellendi', 'urunler', $id);
                header('Location: urunler.php?g=1');
                exit;
            }
        }
    }
}

// Düzenle formu
$urun = [
    'id' => null, 'ad' => '', 'slug' => '', 'kategori_id' => null, 'model_kodu' => '',
    'kisa_aciklama' => '', 'aciklama' => '', 'teknik_ozellikler' => '', 'gorsel' => '',
    'galeri' => '', 'meta_baslik' => '', 'meta_aciklama' => '', 'meta_anahtar' => '',
    'one_cikan' => 0, 'aktif' => 1, 'sira' => 0,
];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM urunler WHERE id = ?");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    if ($u) $urun = array_merge($urun, $u);
    else { $hata = 'Ürün bulunamadı.'; $islem = 'liste'; }
}

$kategoriler = $pdo->query("SELECT * FROM urun_kategoriler ORDER BY sira, ad")->fetchAll();

include 'header.php';
?>

<?php if (!empty($_GET['e'])): ?><div class="alert alert-success">Ürün başarıyla eklendi.</div><?php endif; ?>
<?php if (!empty($_GET['g'])): ?><div class="alert alert-success">Ürün güncellendi.</div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Ürün silindi.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle' || $islem === 'duzenle'):
    $galeri_list = [];
    if (!empty($urun['galeri'])) {
        $tmp = json_decode($urun['galeri'], true);
        if (is_array($tmp)) $galeri_list = $tmp;
    }
?>

<div class="page-head">
    <h2><?= $islem === 'ekle' ? 'Yeni Ürün' : 'Ürün Düzenle' ?></h2>
    <div class="actions">
        <a href="urunler.php" class="btn btn-outline">← Listeye Dön</a>
    </div>
</div>

<form method="post" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="form-grid">
        <!-- Sol -->
        <div>
            <div class="form-group">
                <label>Ürün Adı *</label>
                <input type="text" id="ad" name="ad" class="form-control" required value="<?= e($urun['ad']) ?>" data-slug-target="slug">
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Slug (URL)</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?= e($urun['slug']) ?>" data-slug-source>
                </div>
                <div class="form-group">
                    <label>Model Kodu</label>
                    <input type="text" name="model_kodu" class="form-control" value="<?= e($urun['model_kodu']) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Kısa Açıklama</label>
                <textarea name="kisa_aciklama" class="form-control" rows="3"><?= e($urun['kisa_aciklama']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Detaylı Açıklama (HTML destekli)</label>
                <textarea name="aciklama" class="form-control" rows="10"><?= e($urun['aciklama']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Teknik Özellikler (JSON formatında)</label>
                <textarea name="teknik_ozellikler" class="form-control" rows="6" placeholder='[{"etiket":"Kabin Boyutu","deger":"1200x1000x1500 mm"},{"etiket":"Motor Gücü","deger":"5.5 kW"}]'><?= e($urun['teknik_ozellikler']) ?></textarea>
                <small style="color:var(--text-3); font-size:12px;">Her satır için: [{"etiket":"...","deger":"..."}] formatında yazın.</small>
            </div>

            <h3 style="margin:20px 0 12px; color:#fff; font-size:15px;">SEO Ayarları</h3>
            <div class="form-group">
                <label>Meta Başlık</label>
                <input type="text" name="meta_baslik" class="form-control" value="<?= e($urun['meta_baslik']) ?>">
            </div>
            <div class="form-group">
                <label>Meta Açıklama</label>
                <textarea name="meta_aciklama" class="form-control" rows="2"><?= e($urun['meta_aciklama']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Meta Anahtar Kelimeler</label>
                <input type="text" name="meta_anahtar" class="form-control" value="<?= e($urun['meta_anahtar']) ?>">
            </div>
        </div>

        <!-- Sağ -->
        <div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" class="form-control">
                    <option value="">-- Seçin --</option>
                    <?php foreach ($kategoriler as $k): ?>
                        <option value="<?= (int)$k['id'] ?>" <?= $urun['kategori_id'] == $k['id'] ? 'selected' : '' ?>><?= e($k['ad']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Ana Görsel</label>
                <input type="file" name="gorsel" accept="image/*" class="form-control" data-preview="gorsel-prev">
                <input type="hidden" name="mevcut_gorsel" value="<?= e($urun['gorsel']) ?>">
                <img id="gorsel-prev" class="file-preview" src="<?= e(resim_url($urun['gorsel'])) ?>" alt="" style="<?= $urun['gorsel'] ? '' : 'display:none' ?>">
            </div>
            <div class="form-group">
                <label>Galeri (Birden fazla görsel)</label>
                <input type="file" name="galeri[]" accept="image/*" multiple class="form-control">
                <input type="hidden" name="mevcut_galeri" value="<?= e(json_encode($galeri_list)) ?>">
                <?php if ($galeri_list): ?>
                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:10px;">
                        <?php foreach ($galeri_list as $g): ?>
                            <img src="<?= e(resim_url($g)) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:6px; border:1px solid var(--border);">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Sıra</label>
                <input type="number" name="sira" class="form-control" value="<?= (int)$urun['sira'] ?>">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" id="one_cikan" name="one_cikan" value="1" <?= $urun['one_cikan'] ? 'checked' : '' ?>>
                <label for="one_cikan" style="margin:0;">Öne Çıkan Ürün</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" id="aktif" name="aktif" value="1" <?= $urun['aktif'] ? 'checked' : '' ?>>
                <label for="aktif" style="margin:0;">Aktif</label>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="urunler.php" class="btn btn-outline">İptal</a>
        <button type="submit" class="btn btn-primary"><?= $islem === 'ekle' ? 'Ürünü Ekle' : 'Güncelle' ?></button>
    </div>
</form>

<?php else:
    // LİSTE
    $q = trim($_GET['q'] ?? '');
    $kat = (int)($_GET['kat'] ?? 0);
    $sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
    $sayfa_basi = 20;

    $where = ["1=1"]; $params = [];
    if ($q) { $where[] = "u.ad LIKE :q"; $params[':q'] = "%$q%"; }
    if ($kat) { $where[] = "u.kategori_id = :kat"; $params[':kat'] = $kat; }
    $where_sql = implode(' AND ', $where);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM urunler u WHERE $where_sql");
    $stmt->execute($params);
    $toplam = (int)$stmt->fetchColumn();

    $offset = ($sayfa - 1) * $sayfa_basi;
    $stmt = $pdo->prepare("SELECT u.*, k.ad AS kategori_ad FROM urunler u LEFT JOIN urun_kategoriler k ON k.id = u.kategori_id WHERE $where_sql ORDER BY u.sira, u.id DESC LIMIT " . (int)$sayfa_basi . " OFFSET " . (int)$offset);
    $stmt->execute($params);
    $urunler = $stmt->fetchAll();
?>

<div class="page-head">
    <h2>Ürünler (<?= $toplam ?>)</h2>
    <div class="actions">
        <a href="kategoriler.php" class="btn btn-outline">Kategorileri Yönet</a>
        <a href="urunler.php?islem=ekle" class="btn btn-primary">+ Yeni Ürün</a>
    </div>
</div>

<div class="data-card">
    <div class="data-card-head">
        <form method="get" class="data-search">
            <input type="text" name="q" placeholder="Ürün ara..." value="<?= e($q) ?>">
            <select name="kat" class="form-control" style="min-width:180px;">
                <option value="0">Tüm Kategoriler</option>
                <?php foreach ($kategoriler as $k): ?>
                    <option value="<?= (int)$k['id'] ?>" <?= $kat == $k['id'] ? 'selected' : '' ?>><?= e($k['ad']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-sm">Filtrele</button>
        </form>
    </div>

    <?php if ($urunler): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead>
            <tr>
                <th width="60">Görsel</th>
                <th>Ürün Adı</th>
                <th>Kategori</th>
                <th width="80">Sıra</th>
                <th width="80">Durum</th>
                <th width="180" class="actions-cell">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($urunler as $u): ?>
            <tr>
                <td><img class="thumb" src="<?= e(resim_url($u['gorsel'])) ?>" alt=""></td>
                <td>
                    <strong style="color:#fff;"><?= e($u['ad']) ?></strong>
                    <?php if ($u['one_cikan']): ?><span class="tag tag-orange">Öne Çıkan</span><?php endif; ?>
                    <?php if ($u['model_kodu']): ?><br><small style="color:var(--text-3);"><?= e($u['model_kodu']) ?></small><?php endif; ?>
                </td>
                <td><?= e($u['kategori_ad'] ?: '—') ?></td>
                <td><?= (int)$u['sira'] ?></td>
                <td>
                    <span class="tag <?= $u['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $u['aktif'] ? 'Aktif' : 'Pasif' ?></span>
                </td>
                <td class="actions-cell">
                    <a href="../urun.php?slug=<?= e($u['slug']) ?>" target="_blank" title="Görüntüle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a href="urunler.php?islem=toggle&id=<?= (int)$u['id'] ?>&t=<?= e(csrf_token()) ?>" title="<?= $u['aktif'] ? 'Pasifleştir' : 'Aktifleştir' ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                    </a>
                    <a href="urunler.php?islem=duzenle&id=<?= (int)$u['id'] ?>" title="Düzenle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg>
                    </a>
                    <a href="urunler.php?islem=sil&id=<?= (int)$u['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" title="Sil" data-sil="Bu ürünü silmek istediğinize emin misiniz?">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6V20A2 2 0 0 1 17 22H7A2 2 0 0 1 5 20V6M8 6V4A2 2 0 0 1 10 2H14A2 2 0 0 1 16 4V6"/></svg>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?= sayfalama($toplam, $sayfa_basi, $sayfa, 'urunler.php?' . http_build_query(array_filter(['q' => $q, 'kat' => $kat])) . ($q || $kat ? '&' : '') . 'sayfa=%d') ?>

    <?php else: ?>
    <div class="empty-state">
        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8A2 2 0 0 0 20 6.27L13 2.27A2 2 0 0 0 11 2.27L4 6.27A2 2 0 0 0 3 8V16A2 2 0 0 0 4 17.73L11 21.73A2 2 0 0 0 13 21.73L20 17.73"/></svg>
        <h3>Henüz ürün eklenmedi</h3>
        <p>Başlamak için yeni ürün ekleyin.</p>
        <br>
        <a href="urunler.php?islem=ekle" class="btn btn-primary">+ İlk Ürünü Ekle</a>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>
