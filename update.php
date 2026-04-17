<?php
/**
 * Enamak Makina - Güncelleme İşleyicisi
 * GitHub Releases tabanlı ZIP güncelleme sistemi
 * - manifest.json dosyasına göre dosyaları değiştirir
 * - config.php DAİMA korunur
 * - Yedekleme yapar, hata durumunda rollback
 */
require_once __DIR__ . '/functions.php';
admin_giris_kontrol();
admin_yetki('superadmin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: yonetim/guncelleme.php'); exit;
}
if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
    die('Güvenlik doğrulaması başarısız.');
}
$islem = $_POST['islem'] ?? '';
if ($islem !== 'guncelle') {
    die('Geçersiz işlem.');
}
$surum = preg_replace('/[^0-9a-zA-Z\.\-]/', '', $_POST['surum'] ?? '');
if (!$surum) die('Geçersiz sürüm.');

set_time_limit(300);
ignore_user_abort(true);

$log = [];
function log_ekle(&$log, $msg) { $log[] = ['t' => date('H:i:s'), 'm' => $msg]; }

$basarili = false;
$hata = '';
$yedek_klasor = SITE_PATH . '/updates/yedek-v' . $surum . '-' . date('YmdHis');
$zip_path = SITE_PATH . '/updates/guncelleme-v' . $surum . '.zip';
$extract_path = SITE_PATH . '/updates/extract-v' . $surum;

try {
    // 1. Updates klasörü
    log_ekle($log, 'Güncelleme klasörleri hazırlanıyor...');
    if (!is_dir(SITE_PATH . '/updates')) {
        if (!@mkdir(SITE_PATH . '/updates', 0755, true)) throw new Exception('updates klasörü oluşturulamadı.');
    }

    // 2. GitHub Release detayını al
    log_ekle($log, 'GitHub sürüm bilgileri alınıyor...');
    $api_url = 'https://api.github.com/repos/' . GITHUB_REPO . '/releases/tags/v' . $surum;
    $ch = curl_init($api_url);
    $headers = ['User-Agent: EnamakMakina-UpdateClient', 'Accept: application/vnd.github.v3+json'];
    if (defined('GITHUB_TOKEN') && GITHUB_TOKEN) $headers[] = 'Authorization: token ' . GITHUB_TOKEN;
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http !== 200) throw new Exception('Sürüm bilgisi alınamadı (HTTP ' . $http . ').');
    $rel = json_decode($resp, true);

    // ZIP asset'ini bul
    $zip_url = null;
    if (!empty($rel['assets'])) {
        foreach ($rel['assets'] as $asset) {
            if (strpos($asset['name'], '.zip') !== false) {
                $zip_url = $asset['browser_download_url'];
                break;
            }
        }
    }
    if (!$zip_url) $zip_url = $rel['zipball_url'] ?? null;
    if (!$zip_url) throw new Exception('Güncelleme ZIP bulunamadı.');

    // 3. ZIP indir
    log_ekle($log, 'Güncelleme paketi indiriliyor...');
    $ch = curl_init($zip_url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $zipbin = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http !== 200 || !$zipbin) throw new Exception('ZIP indirilemedi (HTTP ' . $http . ').');

    file_put_contents($zip_path, $zipbin);
    log_ekle($log, 'ZIP indirildi: ' . round(strlen($zipbin) / 1024) . ' KB');

    // 4. Extract
    log_ekle($log, 'ZIP açılıyor...');
    $zip = new ZipArchive();
    if ($zip->open($zip_path) !== TRUE) throw new Exception('ZIP açılamadı.');
    if (is_dir($extract_path)) {
        $dosyalarTmp = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extract_path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($dosyalarTmp as $f) { if ($f->isDir()) @rmdir($f); else @unlink($f); }
        @rmdir($extract_path);
    }
    @mkdir($extract_path, 0755, true);
    $zip->extractTo($extract_path);
    $zip->close();
    log_ekle($log, 'ZIP başarıyla açıldı.');

    // 5. Kök klasörü bul (GitHub ZIP içeride bir alt klasör oluşturur)
    $kok = $extract_path;
    $iter = scandir($extract_path);
    $alt = array_values(array_diff($iter, ['.', '..', '__MACOSX']));
    if (count($alt) === 1 && is_dir($extract_path . '/' . $alt[0])) {
        // manifest.json ZIP kökünde varsa onu kullan
        if (file_exists($extract_path . '/manifest.json')) {
            $kok = $extract_path;
        } elseif (file_exists($extract_path . '/' . $alt[0] . '/manifest.json')) {
            $kok = $extract_path . '/' . $alt[0];
        } else {
            $kok = $extract_path . '/' . $alt[0];
        }
    }

    // 6. manifest.json oku
    $manifest_file = $kok . '/manifest.json';
    if (!file_exists($manifest_file)) throw new Exception('manifest.json paket içinde bulunamadı.');
    $manifest = json_decode(file_get_contents($manifest_file), true);
    if (!$manifest || empty($manifest['files'])) throw new Exception('manifest.json geçersiz.');
    log_ekle($log, 'manifest.json okundu, ' . count($manifest['files']) . ' dosya değiştirilecek.');

    // 7. Yedek al
    log_ekle($log, 'Mevcut dosyalar yedekleniyor...');
    @mkdir($yedek_klasor, 0755, true);
    foreach ($manifest['files'] as $f) {
        $src = SITE_PATH . '/' . ltrim($f, '/');
        if (file_exists($src)) {
            $dst = $yedek_klasor . '/' . ltrim($f, '/');
            @mkdir(dirname($dst), 0755, true);
            @copy($src, $dst);
        }
    }
    log_ekle($log, 'Yedekleme tamamlandı: ' . basename($yedek_klasor));

    // 8. Dosyaları kopyala (config.php HARİÇ)
    log_ekle($log, 'Dosyalar değiştiriliyor...');
    $kopya_sayisi = 0;
    foreach ($manifest['files'] as $f) {
        if ($f === 'config.php') continue; // HİÇ bir zaman config.php üzerine yazma
        $src = $kok . '/' . ltrim($f, '/');
        $dst = SITE_PATH . '/' . ltrim($f, '/');
        if (!file_exists($src)) continue;
        @mkdir(dirname($dst), 0755, true);
        if (@copy($src, $dst)) $kopya_sayisi++;
    }
    log_ekle($log, $kopya_sayisi . ' dosya değiştirildi.');

    // 9. Migration SQL (varsa)
    if (!empty($manifest['migrations'])) {
        log_ekle($log, 'Veritabanı migrasyonları çalıştırılıyor...');
        foreach ($manifest['migrations'] as $mig) {
            $mig_file = $kok . '/' . ltrim($mig, '/');
            if (file_exists($mig_file)) {
                try {
                    $sql = file_get_contents($mig_file);
                    $pdo->exec($sql);
                    log_ekle($log, 'Migration çalıştı: ' . basename($mig));
                } catch (Exception $e) {
                    log_ekle($log, 'Migration hatası: ' . $e->getMessage());
                }
            }
        }
    }

    // 10. manifest.json'ı da kopyala (yeni sürüm bilgisi için)
    @copy($manifest_file, SITE_PATH . '/manifest.json');

    // 11. Temizlik
    log_ekle($log, 'Geçici dosyalar temizleniyor...');
    @unlink($zip_path);
    // Extract path'i sil
    if (is_dir($extract_path)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extract_path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $p) { if ($p->isDir()) @rmdir($p); else @unlink($p); }
        @rmdir($extract_path);
    }

    // 12. Kayıt
    $pdo->prepare("INSERT INTO guncellemeler (surum, notlar, durum, tarih) VALUES (?, ?, 'basarili', NOW())")
        ->execute([$surum, $rel['body'] ?? '']);
    denetim_kaydet('guncelleme_yapildi', 'guncellemeler', null);

    log_ekle($log, '✓ Güncelleme başarıyla tamamlandı.');
    $basarili = true;

} catch (Exception $e) {
    $hata = $e->getMessage();
    log_ekle($log, '✗ HATA: ' . $hata);

    // Rollback (yedekten geri yükle)
    if (is_dir($yedek_klasor)) {
        log_ekle($log, 'Geri alma işlemi başlatılıyor...');
        try {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($yedek_klasor, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if ($f->isFile()) {
                    $rel_path = substr($f->getPathname(), strlen($yedek_klasor) + 1);
                    $rel_path = str_replace('\\', '/', $rel_path);
                    if ($rel_path === 'config.php') continue;
                    $dst = SITE_PATH . '/' . $rel_path;
                    @copy($f->getPathname(), $dst);
                }
            }
            log_ekle($log, 'Dosyalar yedekten geri yüklendi.');
        } catch (Exception $e2) {
            log_ekle($log, 'Rollback sırasında hata: ' . $e2->getMessage());
        }
    }

    try {
        $pdo->prepare("INSERT INTO guncellemeler (surum, notlar, durum, tarih) VALUES (?, ?, 'basarisiz', NOW())")
            ->execute([$surum, $hata]);
    } catch (Exception $eig) {}
}

// Sonuç ekranı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Güncelleme - Enamak Makina</title>
<link rel="stylesheet" href="yonetim/assets/admin.css">
</head>
<body style="padding:40px 20px;">
<div style="max-width:800px; margin:0 auto;">
    <div class="data-card" style="padding:30px;">
        <h1 style="color:#fff; margin-bottom:8px;">Güncelleme <?= $basarili ? '✓' : '✗' ?></h1>
        <p style="color:var(--text-2);">Sürüm v<?= e($surum) ?></p>

        <?php if ($basarili): ?>
            <div class="alert alert-success" style="margin-top:20px;">
                ✓ Güncelleme başarıyla tamamlandı! Site yeni sürüme yükseltildi.
            </div>
        <?php else: ?>
            <div class="alert alert-danger" style="margin-top:20px;">
                ✗ Güncelleme başarısız: <?= e($hata) ?>
                <br><br>Dosyalar yedekten geri yüklenmeye çalışıldı.
            </div>
        <?php endif; ?>

        <h3 style="color:#fff; margin:24px 0 10px;">İşlem Kaydı</h3>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:16px; font-family:monospace; font-size:12px; max-height:400px; overflow-y:auto;">
            <?php foreach ($log as $entry): ?>
                <div style="color:var(--text-2); padding:3px 0;">
                    <span style="color:var(--text-3);">[<?= e($entry['t']) ?>]</span> <?= e($entry['m']) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:24px;">
            <a href="yonetim/guncelleme.php" class="btn btn-outline">← Güncelleme Paneline Dön</a>
            <a href="index.php" class="btn btn-primary" target="_blank">Siteyi Test Et</a>
        </div>
    </div>
</div>
</body>
</html>
