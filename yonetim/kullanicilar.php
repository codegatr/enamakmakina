<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
admin_yetki('superadmin');

$sayfa_baslik = 'Yöneticiler';
$islem = $_GET['islem'] ?? 'liste';
$id = (int)($_GET['id'] ?? 0);
$hata = '';

if ($islem === 'sil' && $id && csrf_dogrula($_GET['t'] ?? '')) {
    if ($id == $_SESSION['admin_id']) {
        header('Location: kullanicilar.php?e=1'); exit;
    }
    $pdo->prepare("DELETE FROM yoneticiler WHERE id = ?")->execute([$id]);
    denetim_kaydet('yonetici_silindi', 'yoneticiler', $id);
    header('Location: kullanicilar.php?s=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($islem === 'ekle' || $islem === 'duzenle')) {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $ad_soyad = trim($_POST['ad_soyad'] ?? '');
        $rol = $_POST['rol'] ?? 'editor';
        $sifre = $_POST['sifre'] ?? '';
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        if (!$kullanici_adi || !$email) $hata = 'Kullanıcı adı ve e-posta zorunlu.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $hata = 'Geçersiz e-posta.';
        elseif ($islem === 'ekle' && strlen($sifre) < 8) $hata = 'Şifre en az 8 karakter olmalı.';
        else {
            if ($islem === 'ekle') {
                $hash = password_hash($sifre, PASSWORD_BCRYPT);
                $pdo->prepare("INSERT INTO yoneticiler (kullanici_adi, email, sifre, ad_soyad, rol, aktif) VALUES (?,?,?,?,?,?)")
                    ->execute([$kullanici_adi, $email, $hash, $ad_soyad, $rol, $aktif]);
                denetim_kaydet('yonetici_eklendi', 'yoneticiler', (int)$pdo->lastInsertId());
            } else {
                if ($sifre) {
                    if (strlen($sifre) < 8) { $hata = 'Şifre en az 8 karakter olmalı.'; }
                    else {
                        $hash = password_hash($sifre, PASSWORD_BCRYPT);
                        $pdo->prepare("UPDATE yoneticiler SET kullanici_adi=?, email=?, sifre=?, ad_soyad=?, rol=?, aktif=? WHERE id=?")
                            ->execute([$kullanici_adi, $email, $hash, $ad_soyad, $rol, $aktif, $id]);
                    }
                } else {
                    $pdo->prepare("UPDATE yoneticiler SET kullanici_adi=?, email=?, ad_soyad=?, rol=?, aktif=? WHERE id=?")
                        ->execute([$kullanici_adi, $email, $ad_soyad, $rol, $aktif, $id]);
                }
                if (!$hata) denetim_kaydet('yonetici_guncellendi', 'yoneticiler', $id);
            }
            if (!$hata) { header('Location: kullanicilar.php?b=1'); exit; }
        }
    }
}

$veri = ['kullanici_adi'=>'', 'email'=>'', 'ad_soyad'=>'', 'rol'=>'editor', 'aktif'=>1];
if ($islem === 'duzenle' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM yoneticiler WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) $veri = array_merge($veri, $v); else $islem = 'liste';
}

include 'header.php';
?>
<?php if (!empty($_GET['b'])): ?><div class="alert alert-success">Kaydedildi.</div><?php endif; ?>
<?php if (!empty($_GET['s'])): ?><div class="alert alert-success">Silindi.</div><?php endif; ?>
<?php if (!empty($_GET['e'])): ?><div class="alert alert-danger">Kendinizi silemezsiniz.</div><?php endif; ?>
<?php if ($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>

<?php if ($islem === 'ekle' || $islem === 'duzenle'): ?>
<div class="page-head">
    <h2><?= $islem === 'ekle' ? 'Yeni Yönetici' : 'Yöneticiyi Düzenle' ?></h2>
    <a href="kullanicilar.php" class="btn btn-outline">← Geri</a>
</div>
<form method="post" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-grid-2">
        <div class="form-group"><label>Kullanıcı Adı *</label><input type="text" name="kullanici_adi" class="form-control" required value="<?= e($veri['kullanici_adi']) ?>"></div>
        <div class="form-group"><label>E-Posta *</label><input type="email" name="email" class="form-control" required value="<?= e($veri['email']) ?>"></div>
    </div>
    <div class="form-grid-2">
        <div class="form-group"><label>Ad Soyad</label><input type="text" name="ad_soyad" class="form-control" value="<?= e($veri['ad_soyad']) ?>"></div>
        <div class="form-group"><label>Rol</label>
            <select name="rol" class="form-control">
                <option value="editor" <?= $veri['rol']==='editor'?'selected':'' ?>>Editor</option>
                <option value="admin" <?= $veri['rol']==='admin'?'selected':'' ?>>Admin</option>
                <option value="superadmin" <?= $veri['rol']==='superadmin'?'selected':'' ?>>Süper Admin</option>
            </select>
        </div>
    </div>
    <div class="form-group"><label>Şifre <?= $islem==='duzenle' ? '(değiştirmek istemiyorsanız boş bırakın)' : '*' ?></label>
        <input type="password" name="sifre" class="form-control" <?= $islem==='ekle' ? 'required minlength="8"' : '' ?>></div>
    <div class="form-group form-check"><input type="checkbox" id="aktif" name="aktif" value="1" <?= $veri['aktif'] ? 'checked' : '' ?>><label for="aktif" style="margin:0;">Aktif</label></div>
    <div class="form-actions"><a href="kullanicilar.php" class="btn btn-outline">İptal</a><button class="btn btn-primary">Kaydet</button></div>
</form>
<?php else:
    $liste = $pdo->query("SELECT * FROM yoneticiler ORDER BY id")->fetchAll();
?>
<div class="page-head">
    <h2>Yöneticiler (<?= count($liste) ?>)</h2>
    <a href="kullanicilar.php?islem=ekle" class="btn btn-primary">+ Yeni Yönetici</a>
</div>
<div class="data-card">
    <?php if ($liste): ?>
    <div style="overflow-x:auto;">
    <table class="data-table">
        <thead><tr><th>Kullanıcı</th><th>E-Posta</th><th>Rol</th><th>Son Giriş</th><th width="80">Durum</th><th width="120" class="actions-cell">İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($liste as $y): ?>
            <tr>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="avatar"><?= e(mb_substr($y['ad_soyad'] ?: $y['kullanici_adi'], 0, 1)) ?></span>
                        <div>
                            <strong style="color:var(--text);"><?= e($y['ad_soyad'] ?: $y['kullanici_adi']) ?></strong>
                            <?php if ($y['id'] == $_SESSION['admin_id']): ?><span class="tag tag-blue">Siz</span><?php endif; ?>
                            <br><small style="color:var(--text-3);">@<?= e($y['kullanici_adi']) ?></small>
                        </div>
                    </div>
                </td>
                <td><?= e($y['email']) ?></td>
                <td><span class="tag tag-<?= $y['rol']==='superadmin'?'orange':($y['rol']==='admin'?'blue':'gray') ?>"><?= strtoupper(e($y['rol'])) ?></span></td>
                <td style="font-size:12px; color:var(--text-2);"><?= $y['son_giris'] ? tr_tarih($y['son_giris'], true) : '—' ?></td>
                <td><span class="tag <?= $y['aktif'] ? 'tag-green' : 'tag-red' ?>"><?= $y['aktif'] ? 'Aktif' : 'Pasif' ?></span></td>
                <td class="actions-cell">
                    <a href="kullanicilar.php?islem=duzenle&id=<?= (int)$y['id'] ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4A2 2 0 0 0 2 6V20A2 2 0 0 0 4 22H18A2 2 0 0 0 20 20V13"/><path d="M18.5 2.5A2.121 2.121 0 0 1 21.5 5.5L12 15L8 16L9 12Z"/></svg></a>
                    <?php if ($y['id'] != $_SESSION['admin_id']): ?>
                    <a href="kullanicilar.php?islem=sil&id=<?= (int)$y['id'] ?>&t=<?= e(csrf_token()) ?>" class="del" data-sil="Yöneticiyi silmek istediğinize emin misiniz?"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/></svg></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
