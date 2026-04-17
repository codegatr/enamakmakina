<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();

$sayfa_baslik = 'Site Ayarları';
$hata = '';
$basarili = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $hata = 'Güvenlik doğrulaması başarısız.';
    } else {
        $ayarlar = $_POST['ayar'] ?? [];
        foreach ($ayarlar as $anahtar => $deger) {
            $pdo->prepare("INSERT INTO ayarlar (anahtar, deger, grup) VALUES (?, ?, COALESCE((SELECT grup FROM (SELECT grup FROM ayarlar WHERE anahtar = ?) AS a), 'genel'))
                           ON DUPLICATE KEY UPDATE deger = VALUES(deger)")
                ->execute([$anahtar, $deger, $anahtar]);
        }
        denetim_kaydet('ayarlar_guncellendi', 'ayarlar');
        $basarili = true;
    }
}

// Ayarları getir
$tum = [];
foreach ($pdo->query("SELECT * FROM ayarlar ORDER BY grup, anahtar") as $row) {
    $tum[$row['grup']][$row['anahtar']] = $row;
}

// Bilinen grup sıralaması
$gruplar = ['genel', 'iletisim', 'sosyal', 'seo', 'mail', 'tasarim', 'sistem'];
foreach (array_keys($tum) as $g) {
    if (!in_array($g, $gruplar)) $gruplar[] = $g;
}

$grup_ad = [
    'genel' => 'Genel',
    'iletisim' => 'İletişim',
    'sosyal' => 'Sosyal Medya',
    'seo' => 'SEO',
    'mail' => 'E-Posta',
    'tasarim' => 'Tasarım',
    'sistem' => 'Sistem',
];

include 'header.php';
?>

<?php if ($basarili): ?>
    <div class="alert alert-success">Ayarlar başarıyla kaydedildi.</div>
<?php endif; ?>
<?php if ($hata): ?>
    <div class="alert alert-danger"><?= e($hata) ?></div>
<?php endif; ?>

<form method="post" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="settings-tabs">
        <?php foreach ($gruplar as $i => $g):
            if (empty($tum[$g])) continue;
        ?>
            <button type="button" class="settings-tab <?= $i === 0 ? 'active' : '' ?>" data-tab="grup-<?= e($g) ?>"><?= e($grup_ad[$g] ?? ucfirst($g)) ?></button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($gruplar as $i => $g):
        if (empty($tum[$g])) continue;
    ?>
        <div class="settings-panel <?= $i === 0 ? 'active' : '' ?>" id="grup-<?= e($g) ?>">
            <?php foreach ($tum[$g] as $anahtar => $ayar):
                $aciklama = $ayar['aciklama'] ?: $anahtar;
                $uzun = mb_strlen($ayar['deger']) > 100 || strpos($anahtar, 'aciklama') !== false || strpos($anahtar, 'metin') !== false || strpos($anahtar, 'iframe') !== false;
            ?>
                <div class="form-group">
                    <label><?= e($aciklama) ?> <small style="color:var(--text-3); font-weight:normal;">(<?= e($anahtar) ?>)</small></label>
                    <?php if ($uzun): ?>
                        <textarea name="ayar[<?= e($anahtar) ?>]" class="form-control" rows="4"><?= e($ayar['deger']) ?></textarea>
                    <?php else: ?>
                        <input type="text" name="ayar[<?= e($anahtar) ?>]" class="form-control" value="<?= e($ayar['deger']) ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Kaydet</button>
    </div>
</form>

<?php include 'footer.php'; ?>
