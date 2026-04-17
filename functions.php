<?php
/**
 * Enamak Makina - Ortak Yardımcı Fonksiyonlar
 */

require_once __DIR__ . '/config.php';

/**
 * XSS önlemi için escape
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Site ayarlarını cache ile getir
 */
function ayar(string $anahtar, string $varsayilan = ''): string {
    static $cache = null;
    global $pdo;

    if ($cache === null) {
        $cache = [];
        try {
            $stmt = $pdo->query("SELECT anahtar, deger FROM ayarlar");
            foreach ($stmt->fetchAll() as $row) {
                $cache[$row['anahtar']] = $row['deger'] ?? '';
            }
        } catch (Exception $e) {
            return $varsayilan;
        }
    }
    return $cache[$anahtar] ?? $varsayilan;
}

/**
 * Tüm ayarları bir grup için getir
 */
function ayarlar_grup(string $grup): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT anahtar, deger FROM ayarlar WHERE grup = ?");
    $stmt->execute([$grup]);
    $result = [];
    foreach ($stmt->fetchAll() as $row) {
        $result[$row['anahtar']] = $row['deger'];
    }
    return $result;
}

/**
 * Türkçe karakterleri URL-friendly hale getir (slug)
 */
function slug(string $metin): string {
    $tr = ['ç','ğ','ı','ö','ş','ü','Ç','Ğ','İ','Ö','Ş','Ü'];
    $en = ['c','g','i','o','s','u','c','g','i','o','s','u'];
    $metin = str_replace($tr, $en, $metin);
    $metin = mb_strtolower($metin, 'UTF-8');
    $metin = preg_replace('/[^a-z0-9\s-]/', '', $metin);
    $metin = preg_replace('/[\s-]+/', '-', $metin);
    return trim($metin, '-');
}

/**
 * Türkçe tarih formatı
 */
function tr_tarih(string $tarih, bool $saat = false): string {
    if (empty($tarih)) return '';
    $aylar = ['01'=>'Ocak','02'=>'Şubat','03'=>'Mart','04'=>'Nisan','05'=>'Mayıs','06'=>'Haziran','07'=>'Temmuz','08'=>'Ağustos','09'=>'Eylül','10'=>'Ekim','11'=>'Kasım','12'=>'Aralık'];
    $ts = strtotime($tarih);
    if ($ts === false) return $tarih;
    $gun = date('d', $ts);
    $ay = $aylar[date('m', $ts)] ?? '';
    $yil = date('Y', $ts);
    return $saat ? "$gun $ay $yil " . date('H:i', $ts) : "$gun $ay $yil";
}

/**
 * CSRF Token oluştur
 */
function csrf_token(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token doğrula
 */
function csrf_dogrula(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Admin giriş kontrolü
 */
function admin_giris_kontrol(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . (defined('ADMIN_URL') ? ADMIN_URL : '/yonetim/') . 'index.php');
        exit;
    }
    // 2 saat hareketsizlikte otomatik çıkış
    if (isset($_SESSION['admin_son_aktivite']) && (time() - $_SESSION['admin_son_aktivite']) > 7200) {
        session_unset();
        session_destroy();
        header('Location: ' . (defined('ADMIN_URL') ? ADMIN_URL : '/yonetim/') . 'index.php?timeout=1');
        exit;
    }
    $_SESSION['admin_son_aktivite'] = time();
}

/**
 * Admin yetki kontrolü
 */
function admin_yetki(string $gerekli_rol = 'admin'): bool {
    if (empty($_SESSION['admin_rol'])) return false;
    $hiyerarsi = ['editor' => 1, 'admin' => 2, 'superadmin' => 3];
    $mevcut = $hiyerarsi[$_SESSION['admin_rol']] ?? 0;
    $gerekli = $hiyerarsi[$gerekli_rol] ?? 2;
    return $mevcut >= $gerekli;
}

/**
 * Denetim kaydı ekle
 */
function denetim_kaydet(string $islem, string $tablo = null, int $kayit_id = null, string $detay = null): void {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO denetim_kaydi (kullanici_id, kullanici_adi, islem, tablo, kayit_id, detay, ip_adresi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['admin_id'] ?? null,
            $_SESSION['admin_kullanici_adi'] ?? null,
            $islem,
            $tablo,
            $kayit_id,
            $detay,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (Exception $e) {
        // Sessizce geç
    }
}

/**
 * IP adresi
 */
function ip_adresi(): string {
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = explode(',', $_SERVER[$key])[0];
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

/**
 * Dosya yükleme (resim)
 */
function resim_yukle(array $dosya, string $klasor, int $max_boyut = 5242880): ?string {
    if (!isset($dosya['tmp_name']) || $dosya['error'] !== UPLOAD_ERR_OK) return null;
    if ($dosya['size'] > $max_boyut) return null;

    $izinli = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
    if (!in_array($uzanti, $izinli)) return null;

    $klasor_yolu = __DIR__ . '/uploads/' . trim($klasor, '/') . '/';
    if (!is_dir($klasor_yolu)) mkdir($klasor_yolu, 0755, true);

    $yeni_ad = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $uzanti;
    $hedef = $klasor_yolu . $yeni_ad;

    if (move_uploaded_file($dosya['tmp_name'], $hedef)) {
        return 'uploads/' . trim($klasor, '/') . '/' . $yeni_ad;
    }
    return null;
}

/**
 * Kısaltma (string truncate)
 */
function kisalt(string $metin, int $uzunluk = 150, string $son = '...'): string {
    $metin = strip_tags($metin);
    if (mb_strlen($metin) <= $uzunluk) return $metin;
    return mb_substr($metin, 0, $uzunluk) . $son;
}

/**
 * JSON response
 */
function json_yanit(array $veri, int $kod = 200): void {
    http_response_code($kod);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($veri, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Menü öğesi aktif mi?
 */
function aktif_menu(string $sayfa): string {
    $mevcut = basename($_SERVER['SCRIPT_NAME']);
    return $mevcut === $sayfa ? 'active' : '';
}

/**
 * Kategori getir
 */
function kategori_getir(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM urun_kategoriler WHERE id = ?");
    $stmt->execute([$id]);
    $sonuc = $stmt->fetch();
    return $sonuc ?: null;
}

/**
 * URL oluştur
 */
function url(string $sayfa, array $params = []): string {
    $base = rtrim(SITE_URL, '/') . '/' . ltrim($sayfa, '/');
    if (!empty($params)) {
        $base .= (strpos($sayfa, '?') === false ? '?' : '&') . http_build_query($params);
    }
    return $base;
}

/**
 * Resim URL
 */
function resim_url(?string $yol, string $varsayilan = 'assets/img/placeholder.svg'): string {
    if (empty($yol)) {
        return SITE_URL . '/' . $varsayilan;
    }
    // Dış URL
    if (strpos($yol, 'http') === 0) return $yol;
    return SITE_URL . '/' . ltrim($yol, '/');
}

/**
 * Breadcrumb oluştur
 */
function breadcrumb(array $items): string {
    $html = '<nav class="breadcrumb" aria-label="Gezinti yolu"><ol>';
    $html .= '<li><a href="' . e(SITE_URL) . '/">Anasayfa</a></li>';
    $son = count($items) - 1;
    foreach ($items as $i => $item) {
        if ($i === $son || empty($item['url'])) {
            $html .= '<li aria-current="page">' . e($item['baslik']) . '</li>';
        } else {
            $html .= '<li><a href="' . e($item['url']) . '">' . e($item['baslik']) . '</a></li>';
        }
    }
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Sayfalama
 */
function sayfalama(int $toplam, int $sayfa_basi, int $mevcut_sayfa, string $url_kalip = '?sayfa=%d'): string {
    $toplam_sayfa = (int)ceil($toplam / $sayfa_basi);
    if ($toplam_sayfa <= 1) return '';

    $html = '<nav class="pagination" aria-label="Sayfalama"><ul>';
    if ($mevcut_sayfa > 1) {
        $html .= '<li><a href="' . sprintf($url_kalip, $mevcut_sayfa - 1) . '">« Önceki</a></li>';
    }
    $baslangic = max(1, $mevcut_sayfa - 2);
    $bitis = min($toplam_sayfa, $mevcut_sayfa + 2);
    if ($baslangic > 1) {
        $html .= '<li><a href="' . sprintf($url_kalip, 1) . '">1</a></li>';
        if ($baslangic > 2) $html .= '<li class="dots">…</li>';
    }
    for ($i = $baslangic; $i <= $bitis; $i++) {
        $aktif = $i === $mevcut_sayfa ? ' class="active"' : '';
        $html .= '<li' . $aktif . '><a href="' . sprintf($url_kalip, $i) . '">' . $i . '</a></li>';
    }
    if ($bitis < $toplam_sayfa) {
        if ($bitis < $toplam_sayfa - 1) $html .= '<li class="dots">…</li>';
        $html .= '<li><a href="' . sprintf($url_kalip, $toplam_sayfa) . '">' . $toplam_sayfa . '</a></li>';
    }
    if ($mevcut_sayfa < $toplam_sayfa) {
        $html .= '<li><a href="' . sprintf($url_kalip, $mevcut_sayfa + 1) . '">Sonraki »</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Ziyaret kaydet
 */
function ziyaret_kaydet(): void {
    global $pdo;
    if (isset($_SESSION['son_ziyaret']) && (time() - $_SESSION['son_ziyaret']) < 60) return;
    $_SESSION['son_ziyaret'] = time();
    try {
        $stmt = $pdo->prepare("INSERT INTO ziyaretler (ip_adresi, sayfa, referans, tarayici, tarih) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt->execute([
            ip_adresi(),
            $_SERVER['REQUEST_URI'] ?? '/',
            $_SERVER['HTTP_REFERER'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    } catch (Exception $e) {
        // Sessizce geç
    }
}

/**
 * Basit mail gönderimi (SMTP veya mail())
 */
function mail_gonder(string $alici, string $konu, string $mesaj): bool {
    $gonderen = ayar('smtp_gonderen_email', 'noreply@enamakmakina.com');
    $gonderen_ad = ayar('smtp_gonderen_ad', 'Enamak Makina');

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . mb_encode_mimeheader($gonderen_ad) . ' <' . $gonderen . '>',
        'Reply-To: ' . $gonderen,
        'X-Mailer: PHP/' . phpversion(),
    ];
    return @mail($alici, '=?UTF-8?B?' . base64_encode($konu) . '?=', $mesaj, implode("\r\n", $headers));
}
