<?php
require_once __DIR__ . '/../functions.php';
admin_giris_kontrol();
admin_yetki('superadmin');

$sayfa_baslik = 'Güncelleme Yönetimi';

// Mevcut sürüm
$manifest_path = SITE_PATH . '/manifest.json';
$mevcut_surum = '1.0.0';
if (file_exists($manifest_path)) {
    $m = json_decode(file_get_contents($manifest_path), true);
    if (!empty($m['version'])) $mevcut_surum = $m['version'];
}

// ======================================================================
// GÜNCELLEME İŞLEYİCİ (self-post - update.php yerine bu sayfa kullanılır)
// ======================================================================
$log = [];
$guncelleme_basarili = null;
$guncelleme_hata = '';
$uygulanan_surum = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['islem'] ?? '') === 'guncelle') {
    if (!csrf_dogrula($_POST['csrf_token'] ?? '')) {
        $guncelleme_hata = 'Güvenlik doğrulaması başarısız.';
        $guncelleme_basarili = false;
    } else {
        $surum = preg_replace('/[^0-9a-zA-Z\.\-]/', '', $_POST['surum'] ?? '');
        if (!$surum) {
            $guncelleme_hata = 'Geçersiz sürüm.';
            $guncelleme_basarili = false;
        } else {
            $uygulanan_surum = $surum;
            set_time_limit(300);
            ignore_user_abort(true);

            $log_ekle = function($msg) use (&$log) {
                $log[] = ['t' => date('H:i:s'), 'm' => $msg];
            };

            $yedek_klasor = SITE_PATH . '/updates/yedek-v' . $surum . '-' . date('YmdHis');
            $zip_path     = SITE_PATH . '/updates/guncelleme-v' . $surum . '.zip';
            $extract_path = SITE_PATH . '/updates/extract-v' . $surum;

            try {
                $log_ekle('Güncelleme klasörleri hazırlanıyor...');
                if (!is_dir(SITE_PATH . '/updates')) {
                    if (!@mkdir(SITE_PATH . '/updates', 0755, true))
                        throw new Exception('updates klasörü oluşturulamadı.');
                }

                $log_ekle('GitHub sürüm bilgileri alınıyor...');
                $api_url = 'https://api.github.com/repos/' . GITHUB_REPO . '/releases/tags/v' . $surum;
                $ch = curl_init($api_url);
                $hdr = ['User-Agent: EnamakMakina-UpdateClient', 'Accept: application/vnd.github.v3+json'];
                if (defined('GITHUB_TOKEN') && GITHUB_TOKEN) $hdr[] = 'Authorization: token ' . GITHUB_TOKEN;
                curl_setopt_array($ch, [
                    CURLOPT_HTTPHEADER => $hdr,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $resp = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_code !== 200) throw new Exception("Sürüm bilgisi alınamadı (HTTP $http_code).");
                $rel = json_decode($resp, true);

                $zip_url = null;
                if (!empty($rel['assets'])) {
                    foreach ($rel['assets'] as $a) {
                        if (strpos($a['name'], '.zip') !== false) {
                            $zip_url = $a['browser_download_url'];
                            break;
                        }
                    }
                }
                if (!$zip_url) $zip_url = $rel['zipball_url'] ?? null;
                if (!$zip_url) throw new Exception('Güncelleme ZIP bulunamadı.');

                $log_ekle('Güncelleme paketi indiriliyor...');
                $ch = curl_init($zip_url);
                curl_setopt_array($ch, [
                    CURLOPT_HTTPHEADER => $hdr,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 120,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $zipbin = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_code !== 200 || !$zipbin) throw new Exception("ZIP indirilemedi (HTTP $http_code).");
                file_put_contents($zip_path, $zipbin);
                $log_ekle('ZIP indirildi: ' . round(strlen($zipbin) / 1024) . ' KB');

                $log_ekle('ZIP açılıyor...');
                $zip = new ZipArchive();
                if ($zip->open($zip_path) !== TRUE) throw new Exception('ZIP açılamadı.');
                if (is_dir($extract_path)) {
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extract_path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($it as $f) { if ($f->isDir()) @rmdir($f); else @unlink($f); }
                    @rmdir($extract_path);
                }
                @mkdir($extract_path, 0755, true);
                $zip->extractTo($extract_path);
                $zip->close();
                $log_ekle('ZIP başarıyla açıldı.');

                // Kök klasörü tespit et
                $kok = $extract_path;
                $alt = array_values(array_diff(scandir($extract_path), ['.','..','__MACOSX']));
                if (count($alt) === 1 && is_dir($extract_path . '/' . $alt[0])) {
                    if (file_exists($extract_path . '/manifest.json')) $kok = $extract_path;
                    elseif (file_exists($extract_path . '/' . $alt[0] . '/manifest.json')) $kok = $extract_path . '/' . $alt[0];
                    else $kok = $extract_path . '/' . $alt[0];
                }

                $manifest_file = $kok . '/manifest.json';
                if (!file_exists($manifest_file)) throw new Exception('manifest.json paket içinde bulunamadı.');
                $manifest = json_decode(file_get_contents($manifest_file), true);
                if (!$manifest || empty($manifest['files'])) throw new Exception('manifest.json geçersiz.');
                $log_ekle('manifest.json okundu, ' . count($manifest['files']) . ' dosya değiştirilecek.');

                // Yedekle
                $log_ekle('Mevcut dosyalar yedekleniyor...');
                @mkdir($yedek_klasor, 0755, true);
                foreach ($manifest['files'] as $f) {
                    $src = SITE_PATH . '/' . ltrim($f, '/');
                    if (file_exists($src)) {
                        $dst = $yedek_klasor . '/' . ltrim($f, '/');
                        @mkdir(dirname($dst), 0755, true);
                        @copy($src, $dst);
                    }
                }
                $log_ekle('Yedekleme tamamlandı: ' . basename($yedek_klasor));

                // Kopyala (config.php HARİÇ)
                $log_ekle('Dosyalar değiştiriliyor...');
                $kopya = 0;
                foreach ($manifest['files'] as $f) {
                    if ($f === 'config.php') continue;
                    $src = $kok . '/' . ltrim($f, '/');
                    $dst = SITE_PATH . '/' . ltrim($f, '/');
                    if (!file_exists($src)) continue;
                    @mkdir(dirname($dst), 0755, true);
                    if (@copy($src, $dst)) $kopya++;
                }
                $log_ekle($kopya . ' dosya değiştirildi.');

                // Migration
                if (!empty($manifest['migrations'])) {
                    $log_ekle('Veritabanı migrasyonları çalıştırılıyor...');
                    foreach ($manifest['migrations'] as $mig) {
                        $mig_file = $kok . '/' . ltrim($mig, '/');
                        if (!file_exists($mig_file)) {
                            $log_ekle('Migration dosyası bulunamadı: ' . $mig);
                            continue;
                        }
                        $sql_raw = file_get_contents($mig_file);
                        // Yorum satirlarini temizle (-- ile baslayan tam satirlar)
                        $sql_lines = explode("\n", $sql_raw);
                        $sql_clean = '';
                        foreach ($sql_lines as $ln) {
                            $t = trim($ln);
                            if ($t === '' || strpos($t, '--') === 0) continue;
                            $sql_clean .= $ln . "\n";
                        }
                        // ; ile statement'lere bol (string icindeki ; degil, satir sonundaki)
                        $statements = preg_split('/;\s*\n/', $sql_clean);
                        $ok = 0; $fail = 0; $skipped = 0;
                        // Idempotent migrasyon: bu MySQL hata kodlari "zaten yapilmis" anlaminda
                        // ve gercek hata olarak sayilmaz
                        $benign_codes = [
                            '42S21', // 1060 Duplicate column
                            '42S01', // 1050 Table already exists
                            '23000', // 1062 Duplicate entry / 1022 Duplicate key
                            '42000', // 1061 Duplicate key name
                        ];
                        $benign_messages = ['duplicate column', 'already exists', 'duplicate key name', 'duplicate entry'];
                        foreach ($statements as $stmt) {
                            $stmt = trim($stmt, " \n\r\t;");
                            if ($stmt === '') continue;
                            try {
                                $pdo->exec($stmt);
                                $ok++;
                            } catch (Exception $e) {
                                $msg = $e->getMessage();
                                $is_benign = false;
                                foreach ($benign_codes as $code) {
                                    if (strpos($msg, $code) !== false) { $is_benign = true; break; }
                                }
                                if (!$is_benign) {
                                    $msg_lower = strtolower($msg);
                                    foreach ($benign_messages as $bm) {
                                        if (strpos($msg_lower, $bm) !== false) { $is_benign = true; break; }
                                    }
                                }
                                if ($is_benign) {
                                    $skipped++;
                                    $log_ekle('  ℹ Atlandi (zaten yapilmis): ' . substr($msg, 0, 120));
                                } else {
                                    $fail++;
                                    $log_ekle('  ✗ Statement hatasi: ' . substr($msg, 0, 150));
                                }
                            }
                        }
                        $summary = 'Migration: ' . basename($mig) . ' - ' . $ok . ' basarili';
                        if ($skipped > 0) $summary .= ', ' . $skipped . ' atlandi';
                        if ($fail > 0) $summary .= ', ' . $fail . ' hatali';
                        $log_ekle($summary);
                    }
                }

                @copy($manifest_file, SITE_PATH . '/manifest.json');

                $log_ekle('Geçici dosyalar temizleniyor...');
                @unlink($zip_path);
                if (is_dir($extract_path)) {
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extract_path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($it as $p) { if ($p->isDir()) @rmdir($p); else @unlink($p); }
                    @rmdir($extract_path);
                }

                try {
                    $pdo->prepare("INSERT INTO guncellemeler (surum, notlar, durum, tarih) VALUES (?, ?, 'basarili', NOW())")
                        ->execute([$surum, $rel['body'] ?? '']);
                } catch (Exception $e) {}
                denetim_kaydet('guncelleme_yapildi', 'guncellemeler');

                $log_ekle('✓ Güncelleme başarıyla tamamlandı.');
                $guncelleme_basarili = true;
                $mevcut_surum = $surum;

            } catch (Exception $e) {
                $guncelleme_hata = $e->getMessage();
                $log_ekle('✗ HATA: ' . $guncelleme_hata);
                $guncelleme_basarili = false;

                if (is_dir($yedek_klasor)) {
                    $log_ekle('Rollback başlatılıyor...');
                    try {
                        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($yedek_klasor, FilesystemIterator::SKIP_DOTS));
                        foreach ($it as $f) {
                            if ($f->isFile()) {
                                $rp = str_replace('\\', '/', substr($f->getPathname(), strlen($yedek_klasor) + 1));
                                if ($rp === 'config.php') continue;
                                @copy($f->getPathname(), SITE_PATH . '/' . $rp);
                            }
                        }
                        $log_ekle('Dosyalar yedekten geri yüklendi.');
                    } catch (Exception $e2) {
                        $log_ekle('Rollback hatası: ' . $e2->getMessage());
                    }
                }

                try {
                    $pdo->prepare("INSERT INTO guncellemeler (surum, notlar, durum, tarih) VALUES (?, ?, 'basarisiz', NOW())")
                        ->execute([$surum, $guncelleme_hata]);
                } catch (Exception $e) {}
            }
        }
    }
}

// GitHub'dan son sürüm kontrolü
$son_surum = null;
$release_info = null;
if (isset($_GET['kontrol'])) {
    $url = 'https://api.github.com/repos/' . GITHUB_REPO . '/releases/latest';
    $ch = curl_init($url);
    $hdr = ['User-Agent: EnamakMakina-UpdateClient', 'Accept: application/vnd.github.v3+json'];
    if (defined('GITHUB_TOKEN') && GITHUB_TOKEN) $hdr[] = 'Authorization: token ' . GITHUB_TOKEN;
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $hdr,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http_code === 200) {
        $release_info = json_decode($resp, true);
        $son_surum = ltrim($release_info['tag_name'] ?? '', 'v');
    }
}

$gecmis = $pdo->query("SELECT * FROM guncellemeler ORDER BY tarih DESC LIMIT 20")->fetchAll();

include 'header.php';
?>

<div class="page-head">
    <h2>Güncelleme Yönetimi</h2>
    <a href="guncelleme.php?kontrol=1" class="btn btn-primary">Güncelleme Ara</a>
</div>

<?php if ($guncelleme_basarili === true): ?>
    <div class="alert alert-success" style="margin-bottom:16px;">
        ✓ <strong>v<?= e($uygulanan_surum) ?></strong> sürümüne başarıyla güncellendi! Siteyi test edin.
    </div>
<?php elseif ($guncelleme_basarili === false): ?>
    <div class="alert alert-danger" style="margin-bottom:16px;">
        ✗ Güncelleme başarısız: <?= e($guncelleme_hata) ?>
        <br>Yedekten geri yükleme denendi.
    </div>
<?php endif; ?>

<?php if (!empty($log)): ?>
    <div class="data-card" style="padding:18px; margin-bottom:20px;">
        <h3 style="color:var(--text); margin-bottom:10px; font-size:14px;">İşlem Kaydı</h3>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:14px; font-family:monospace; font-size:12px; max-height:300px; overflow-y:auto;">
            <?php foreach ($log as $e): ?>
                <div style="color:var(--text-2); padding:2px 0;">
                    <span style="color:var(--text-3);">[<?= e($e['t']) ?>]</span> <?= e($e['m']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="form-grid" style="gap:20px;">
    <div>
        <div class="data-card" style="padding:0;">
            <div class="data-card-head" style="padding:16px 20px;">
                <h3 style="margin:0; font-size:15px; font-weight:700;">Sürüm Bilgisi</h3>
            </div>
            <div style="padding:20px;">
                <!-- Mevcut sürüm kutusu -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:20px; background:linear-gradient(135deg, var(--primary-tint) 0%, #e0e7ff 100%); border:1px solid #c7d2fe; border-radius:var(--radius); margin-bottom:14px;">
                    <div>
                        <div style="color:var(--text-2); font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:4px;">Mevcut Sürüm</div>
                        <div style="color:var(--primary); font-size:28px; font-weight:800; font-family:var(--font-display); letter-spacing:-0.02em;">v<?= e($mevcut_surum) ?></div>
                    </div>
                    <div style="width:48px; height:48px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>

                <?php if (isset($_GET['kontrol'])): ?>
                    <?php if (!$release_info): ?>
                        <div class="alert alert-danger">GitHub sunucusuna ulaşılamadı. İnternet bağlantınızı veya token'ınızı kontrol edin.</div>
                    <?php elseif ($son_surum && version_compare($son_surum, $mevcut_surum, '>')): ?>
                        <div class="alert alert-warn" style="display:block;">
                            <strong>🎉 Yeni sürüm mevcut: v<?= e($son_surum) ?></strong>
                            <?php if (!empty($release_info['body'])): ?>
                                <div style="margin-top:10px; font-size:13px; white-space:pre-wrap;"><?= e($release_info['body']) ?></div>
                            <?php endif; ?>
                            <br>
                            <form method="post" action="guncelleme.php" style="display:inline-block; margin-top:10px;">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="islem" value="guncelle">
                                <input type="hidden" name="surum" value="<?= e($son_surum) ?>">
                                <button class="btn btn-primary" onclick="return confirm('Güncellemeyi başlatmak istediğinize emin misiniz? Yedekleme otomatik yapılacaktır.');">Güncellemeyi Başlat</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">✓ Site en güncel sürümde (v<?= e($mevcut_surum) ?>)</div>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color:var(--text-2); font-size:13px; padding:8px 0;">Yeni güncellemeleri kontrol etmek için üstteki <strong>Güncelleme Ara</strong> butonunu kullanın.</p>
                <?php endif; ?>

                <!-- Repo bilgileri -->
                <div style="margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
                    <h4 style="color:var(--text); font-size:13px; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:12px;">Repo Bilgileri</h4>
                    <div style="display:grid; grid-template-columns:auto 1fr; gap:10px 16px; align-items:center;">
                        <span style="color:var(--text-2); font-size:13px; font-weight:500;">GitHub Repo:</span>
                        <code style="color:var(--text); font-size:13px; background:var(--bg-3); padding:4px 8px; border-radius:4px; font-family:ui-monospace, monospace; justify-self:start;"><?= e(GITHUB_REPO) ?></code>

                        <span style="color:var(--text-2); font-size:13px; font-weight:500;">Token:</span>
                        <div><?= defined('GITHUB_TOKEN') && GITHUB_TOKEN ? '<span class="tag tag-green">✓ Yapılandırıldı</span>' : '<span class="tag tag-red">✗ Yok</span>' ?></div>

                        <span style="color:var(--text-2); font-size:13px; font-weight:500;">PHP Sürümü:</span>
                        <code style="color:var(--text); font-size:13px; background:var(--bg-3); padding:4px 8px; border-radius:4px; font-family:ui-monospace, monospace; justify-self:start;"><?= PHP_VERSION ?></code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="data-card" style="padding:0;">
            <div class="data-card-head" style="padding:16px 20px;">
                <h3 style="margin:0; font-size:15px; font-weight:700;">Güncelleme Geçmişi</h3>
                <?php if ($gecmis): ?><span class="tag tag-gray"><?= count($gecmis) ?> kayıt</span><?php endif; ?>
            </div>
            <div style="padding:20px; max-height:600px; overflow-y:auto;">
            <?php if ($gecmis): ?>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($gecmis as $g): ?>
                        <?php $durum_renk = $g['durum']==='basarili' ? 'var(--success)' : 'var(--danger)'; ?>
                        <div style="padding:14px; background:var(--bg-3); border-radius:var(--radius); border-left:3px solid <?= $durum_renk ?>;">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                                <strong style="color:var(--text); font-family:var(--font-display); font-size:14px;">v<?= e($g['surum']) ?></strong>
                                <small style="color:var(--text-3); font-size:11px;"><?= tr_tarih($g['tarih'], true) ?></small>
                            </div>
                            <?php if ($g['notlar']): ?>
                                <div style="color:var(--text-2); font-size:12px; line-height:1.5; margin-top:4px;"><?= e(kisalt(preg_replace('/[#*`]/', '', strip_tags($g['notlar'])), 180)) ?></div>
                            <?php endif; ?>
                            <span class="tag tag-<?= $g['durum']==='basarili'?'green':'red' ?>" style="margin-top:8px; font-size:10px;"><?= strtoupper(e($g['durum'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:var(--text-3); font-size:13px; text-align:center; padding:20px;">Henüz güncelleme yapılmadı.</p>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
