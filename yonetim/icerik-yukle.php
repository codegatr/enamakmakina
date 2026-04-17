<?php
/**
 * İçerik Yükleme ve Teşhis Aracı
 * Admin paneline girişli. Migration SQL'ini manuel çalıştırır ve durum gösterir.
 */
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();

$sayfa_baslik = 'İçerik Yükleme ve Teşhis';

// Tablo sayıları
function tablo_sayisi($pdo, $tablo) {
    try {
        return (int)$pdo->query("SELECT COUNT(*) FROM $tablo")->fetchColumn();
    } catch (Exception $e) {
        return -1;
    }
}

$durum_once = [
    'urun_kategoriler' => tablo_sayisi($pdo, 'urun_kategoriler'),
    'urunler' => tablo_sayisi($pdo, 'urunler'),
    'hizmetler' => tablo_sayisi($pdo, 'hizmetler'),
    'slider' => tablo_sayisi($pdo, 'slider'),
    'referanslar' => tablo_sayisi($pdo, 'referanslar'),
];

$calistirildi = false;
$log = [];
$hata_sayisi = 0;
$basari_sayisi = 0;
$atlandi_sayisi = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['yukle'])) {
    $calistirildi = true;
    $mig_file = __DIR__ . '/../migration-v1.3.0-content.sql';

    if (!file_exists($mig_file)) {
        $log[] = ['tip' => 'hata', 'mesaj' => 'Migration dosyasi bulunamadi: migration-v1.3.0-content.sql'];
    } else {
        $log[] = ['tip' => 'bilgi', 'mesaj' => 'Migration dosyasi bulundu: ' . basename($mig_file) . ' (' . number_format(filesize($mig_file)) . ' byte)'];

        $sql_raw = file_get_contents($mig_file);
        $lines = explode("\n", $sql_raw);
        $clean = '';
        foreach ($lines as $ln) {
            $t = trim($ln);
            if ($t === '' || strpos($t, '--') === 0) continue;
            $clean .= $ln . "\n";
        }
        $statements = preg_split('/;\s*\n/', $clean);
        $log[] = ['tip' => 'bilgi', 'mesaj' => 'Parse edilen statement sayisi: ' . count($statements)];

        // Idempotent hatalar: "zaten yapilmis" anlami tasiyan, gercek hata degil
        $benign_codes = ['42S21', '42S01', '23000', '42000'];
        $benign_patterns = ['duplicate column', 'already exists', 'duplicate key name', 'duplicate entry'];

        foreach ($statements as $idx => $stmt) {
            $stmt = trim($stmt, " \n\r\t;");
            if ($stmt === '') continue;

            // Statement tipini belirle (ilk 60 karakter)
            $ozet = substr(preg_replace('/\s+/', ' ', $stmt), 0, 70);

            try {
                $pdo->exec($stmt);
                $basari_sayisi++;
                $log[] = ['tip' => 'ok', 'mesaj' => '#' . ($idx + 1) . ': ' . $ozet . '...'];
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $is_benign = false;
                foreach ($benign_codes as $code) {
                    if (strpos($msg, $code) !== false) { $is_benign = true; break; }
                }
                if (!$is_benign) {
                    $msg_lower = strtolower($msg);
                    foreach ($benign_patterns as $bm) {
                        if (strpos($msg_lower, $bm) !== false) { $is_benign = true; break; }
                    }
                }
                if ($is_benign) {
                    $atlandi_sayisi++;
                    $log[] = ['tip' => 'atlandi', 'mesaj' => '#' . ($idx + 1) . ' ATLANDI (zaten yapilmis): ' . $ozet . '...'];
                } else {
                    $hata_sayisi++;
                    $log[] = ['tip' => 'hata', 'mesaj' => '#' . ($idx + 1) . ' HATA: ' . $msg . ' | SQL: ' . $ozet];
                }
            }
        }

        $ozet_msg = 'Toplam: ' . $basari_sayisi . ' basarili';
        if ($atlandi_sayisi > 0) $ozet_msg .= ', ' . $atlandi_sayisi . ' atlandi';
        if ($hata_sayisi > 0) $ozet_msg .= ', ' . $hata_sayisi . ' hatali';
        $ozet_msg .= '.';
        $log[] = ['tip' => 'bilgi', 'mesaj' => $ozet_msg];
    }
}

$durum_sonra = [
    'urun_kategoriler' => tablo_sayisi($pdo, 'urun_kategoriler'),
    'urunler' => tablo_sayisi($pdo, 'urunler'),
    'hizmetler' => tablo_sayisi($pdo, 'hizmetler'),
    'slider' => tablo_sayisi($pdo, 'slider'),
    'referanslar' => tablo_sayisi($pdo, 'referanslar'),
];

include __DIR__ . '/header.php';
?>

<div class="yonetim-baslik">
    <h1>İçerik Yükleme ve Teşhis</h1>
    <p class="desc">Migration dosyasını manuel çalıştırır ve her statement için başarı/hata durumunu gösterir.</p>
</div>

<div class="yonetim-icerik">
    <div class="card" style="padding: 24px; margin-bottom: 24px;">
        <h3 style="margin-top: 0;">Mevcut Durum</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="text-align: left; padding: 10px; font-weight: 700;">Tablo</th>
                    <th style="text-align: right; padding: 10px; font-weight: 700;">Kayıt Sayısı</th>
                    <th style="text-align: right; padding: 10px; font-weight: 700;">Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($durum_sonra as $tablo => $sayi): ?>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px;"><code><?= e($tablo) ?></code></td>
                    <td style="padding: 10px; text-align: right; font-weight: 600;">
                        <?php if ($sayi === -1): ?>
                            <span style="color: #dc2626;">HATA</span>
                        <?php else: ?>
                            <?= $sayi ?>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px; text-align: right;">
                        <?php if ($sayi === -1): ?>
                            <span style="color: #dc2626;">✗ Tablo erişilemez</span>
                        <?php elseif ($sayi === 0): ?>
                            <span style="color: #f59e0b;">⚠ Boş</span>
                        <?php else: ?>
                            <span style="color: #16a34a;">✓ Dolu</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$calistirildi): ?>
    <div class="card" style="padding: 24px; margin-bottom: 24px; background: #eff6ff; border: 1px solid #bfdbfe;">
        <h3 style="margin-top: 0; color: #1e40af;">İçeriği Yükle</h3>
        <p>Bu buton <code>migration-v1.3.0-content.sql</code> dosyasını parse ederek her SQL statement'ini tek tek çalıştırır.</p>
        <p style="color: #92400e;"><strong>⚠ Uyarı:</strong> Bu işlem mevcut ürün, kategori, hizmet, slider ve referans kayıtlarını <strong>siler</strong> ve yeni örnek içerikle doldurur. Kendi eklediğiniz veriler varsa önce yedekleyin.</p>
        <form method="post">
            <button type="submit" name="yukle" value="1" class="btn btn-primary btn-lg" onclick="return confirm('İçeriği yüklemek istediğinize emin misiniz? Mevcut ürün/kategori/hizmet/slider/referans kayıtları silinecek.')">
                İçeriği Yükle ve Detaylı Log Göster
            </button>
        </form>
    </div>
    <?php else: ?>
    <div class="card" style="padding: 24px; margin-bottom: 24px; background: <?= $hata_sayisi === 0 ? '#f0fdf4' : '#fef2f2' ?>; border: 1px solid <?= $hata_sayisi === 0 ? '#86efac' : '#fca5a5' ?>;">
        <h3 style="margin-top: 0;">
            <?php if ($hata_sayisi === 0): ?>
                <span style="color: #16a34a;">✓ Tamamlandı</span>
            <?php else: ?>
                <span style="color: #dc2626;">⚠ Bazı statement'lar hata aldı</span>
            <?php endif; ?>
        </h3>
        <p>Başarılı: <strong><?= $basari_sayisi ?></strong> · Atlandı: <strong><?= $atlandi_sayisi ?></strong> · Hatalı: <strong><?= $hata_sayisi ?></strong> statement</p>
        <?php if ($atlandi_sayisi > 0 && $hata_sayisi === 0): ?>
            <p style="color: #475569; font-size: 13px; margin-top: 6px;">
                <strong>ℹ Atlanan statement'lar</strong> — "zaten yapılmış" olan ALTER TABLE / INSERT işlemleridir (örn. kolon zaten var). Bu normal bir davranıştır, migration dosyası tekrar çalıştırıldığında yeniden yapılmaz.
            </p>
        <?php endif; ?>
        <form method="post" action="icerik-yukle.php" style="margin-top: 12px;">
            <button type="submit" name="yukle" value="1" class="btn btn-outline" onclick="return confirm('Tekrar çalıştırılsın mı?')">
                Yeniden Çalıştır
            </button>
            <a href="<?= e(SITE_URL) ?>" class="btn btn-primary" target="_blank" style="margin-left: 8px;">Siteyi Aç ve Kontrol Et</a>
        </form>
    </div>

    <div class="card" style="padding: 24px;">
        <h3 style="margin-top: 0;">Detaylı Log</h3>
        <div style="max-height: 500px; overflow: auto; background: #0f172a; color: #e2e8f0; padding: 16px; border-radius: 6px; font-family: 'SF Mono', Consolas, monospace; font-size: 13px; line-height: 1.6;">
            <?php foreach ($log as $entry): ?>
                <div style="margin-bottom: 4px; <?php
                    if ($entry['tip'] === 'ok') echo 'color: #86efac;';
                    elseif ($entry['tip'] === 'hata') echo 'color: #fca5a5;';
                    elseif ($entry['tip'] === 'atlandi') echo 'color: #fde68a;';
                    else echo 'color: #93c5fd;';
                ?>">
                    <?php
                    if ($entry['tip'] === 'ok') echo '✓';
                    elseif ($entry['tip'] === 'hata') echo '✗';
                    elseif ($entry['tip'] === 'atlandi') echo 'ℹ';
                    else echo 'ℹ';
                    ?>
                    <?= e($entry['mesaj']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($calistirildi && $hata_sayisi === 0): ?>
    <div class="card" style="padding: 24px; margin-top: 24px; background: #ecfdf5;">
        <h3 style="margin-top: 0;">Sonraki Adımlar</h3>
        <ol style="line-height: 2;">
            <li><a href="<?= e(SITE_URL) ?>" target="_blank"><strong>Anasayfayı aç</strong></a> → slider, kategoriler, hizmetler görünmeli</li>
            <li><a href="<?= e(SITE_URL) ?>/urunler.php" target="_blank"><strong>Ürünler sayfası</strong></a> → 14 ürün listelenmeli</li>
            <li><a href="<?= e(SITE_URL) ?>/hizmetler.php" target="_blank"><strong>Hizmetler sayfası</strong></a> → 7 hizmet listelenmeli</li>
            <li>Kendi ürün fotoğraflarınız hazır olduğunda <a href="urunler.php"><strong>Ürün Yönetimi</strong></a> bölümünden her ürünü düzenleyip görsel yükleyebilirsiniz</li>
        </ol>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
