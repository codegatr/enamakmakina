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

// En son sürüm kontrolü (GitHub Releases API)
$son_surum = null;
$release_info = null;
if (isset($_GET['kontrol'])) {
    $url = 'https://api.github.com/repos/' . GITHUB_REPO . '/releases/latest';
    $ch = curl_init($url);
    $headers = ['User-Agent: EnamakMakina-UpdateClient', 'Accept: application/vnd.github.v3+json'];
    if (defined('GITHUB_TOKEN') && GITHUB_TOKEN) {
        $headers[] = 'Authorization: token ' . GITHUB_TOKEN;
    }
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http === 200) {
        $release_info = json_decode($resp, true);
        $son_surum = ltrim($release_info['tag_name'] ?? '', 'v');
    }
}

// Geçmiş güncellemeler
$gecmis = $pdo->query("SELECT * FROM guncellemeler ORDER BY tarih DESC LIMIT 20")->fetchAll();

include 'header.php';
?>

<div class="page-head">
    <h2>Güncelleme Yönetimi</h2>
    <a href="guncelleme.php?kontrol=1" class="btn btn-primary">Güncelleme Ara</a>
</div>

<div class="form-grid" style="gap:20px;">
    <div>
        <div class="data-card" style="padding:20px;">
            <h3 style="color:#fff; margin-bottom:14px;">Sürüm Bilgisi</h3>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:16px; background:var(--bg); border-radius:8px; margin-bottom:12px;">
                <div>
                    <div style="color:var(--text-2); font-size:12px;">Mevcut Sürüm</div>
                    <div style="color:#fff; font-size:24px; font-weight:700;">v<?= e($mevcut_surum) ?></div>
                </div>
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><path d="M22 11.08V12A10 10 0 1 1 16.91 2.84"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
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
                        <form method="post" action="../update.php" style="display:inline-block; margin-top:10px;">
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
                <p style="color:var(--text-2); font-size:13px;">Yeni güncellemeleri kontrol etmek için yukarıdaki butonu kullanın.</p>
            <?php endif; ?>

            <h4 style="margin-top:24px; color:#fff; font-size:14px;">Repo Bilgileri</h4>
            <table style="width:100%; margin-top:10px;">
                <tr><td style="padding:6px 0; color:var(--text-2); font-size:13px;">GitHub Repo:</td><td style="color:#fff;"><?= e(GITHUB_REPO) ?></td></tr>
                <tr><td style="padding:6px 0; color:var(--text-2); font-size:13px;">Token:</td><td><?= defined('GITHUB_TOKEN') && GITHUB_TOKEN ? '<span class="tag tag-green">Yapılandırıldı</span>' : '<span class="tag tag-red">Yok</span>' ?></td></tr>
                <tr><td style="padding:6px 0; color:var(--text-2); font-size:13px;">PHP Sürümü:</td><td style="color:#fff;"><?= PHP_VERSION ?></td></tr>
            </table>
        </div>
    </div>

    <div>
        <div class="data-card" style="padding:20px;">
            <h3 style="color:#fff; margin-bottom:14px;">Güncelleme Geçmişi</h3>
            <?php if ($gecmis): ?>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ($gecmis as $g): ?>
                        <div style="padding:12px; background:var(--bg); border-radius:8px; border-left:3px solid var(--primary);">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong style="color:#fff;">v<?= e($g['surum']) ?></strong>
                                <small style="color:var(--text-3);"><?= tr_tarih($g['tarih'], true) ?></small>
                            </div>
                            <?php if ($g['notlar']): ?><div style="color:var(--text-2); font-size:12px; margin-top:4px; white-space:pre-wrap;"><?= e(kisalt($g['notlar'], 200)) ?></div><?php endif; ?>
                            <span class="tag tag-<?= $g['durum']==='basarili'?'green':'red' ?>" style="margin-top:6px;"><?= strtoupper(e($g['durum'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:var(--text-3); font-size:13px;">Henüz güncelleme yapılmadı.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
