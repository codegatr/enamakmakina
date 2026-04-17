-- =====================================================
-- ENAMAK MAKINA - Kumlama Makinesi İmalat ve Bakım
-- Veritabanı Kurulum Scripti
-- PHP 8.3 / MariaDB / MySQL 5.7+ uyumlu
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. Site Ayarları
-- =====================================================
CREATE TABLE IF NOT EXISTS `ayarlar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `anahtar` VARCHAR(100) NOT NULL UNIQUE,
  `deger` LONGTEXT,
  `grup` VARCHAR(50) DEFAULT 'genel',
  `aciklama` VARCHAR(255) DEFAULT NULL,
  `guncelleme_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_grup` (`grup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. Yöneticiler
-- =====================================================
CREATE TABLE IF NOT EXISTS `yoneticiler` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ad_soyad` VARCHAR(100) NOT NULL,
  `kullanici_adi` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `sifre` VARCHAR(255) NOT NULL,
  `rol` ENUM('superadmin','admin','editor') DEFAULT 'admin',
  `son_giris` DATETIME DEFAULT NULL,
  `son_giris_ip` VARCHAR(45) DEFAULT NULL,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_aktif` (`aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. Slider (Anasayfa)
-- =====================================================
CREATE TABLE IF NOT EXISTS `slider` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `baslik` VARCHAR(200) DEFAULT NULL,
  `alt_baslik` VARCHAR(300) DEFAULT NULL,
  `aciklama` TEXT DEFAULT NULL,
  `gorsel` VARCHAR(255) DEFAULT NULL,
  `buton_metin` VARCHAR(100) DEFAULT NULL,
  `buton_link` VARCHAR(255) DEFAULT NULL,
  `sira` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sira_aktif` (`sira`, `aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. Ürün Kategorileri
-- =====================================================
CREATE TABLE IF NOT EXISTS `urun_kategoriler` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `baslik` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `aciklama` TEXT DEFAULT NULL,
  `icerik` LONGTEXT DEFAULT NULL,
  `gorsel` VARCHAR(255) DEFAULT NULL,
  `ikon` VARCHAR(100) DEFAULT NULL,
  `seo_baslik` VARCHAR(200) DEFAULT NULL,
  `seo_aciklama` VARCHAR(300) DEFAULT NULL,
  `seo_anahtar` VARCHAR(255) DEFAULT NULL,
  `sira` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_sira_aktif` (`sira`, `aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. Ürünler (Kumlama Makineleri)
-- =====================================================
CREATE TABLE IF NOT EXISTS `urunler` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kategori_id` INT(11) DEFAULT NULL,
  `baslik` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `kisa_aciklama` TEXT DEFAULT NULL,
  `icerik` LONGTEXT DEFAULT NULL,
  `teknik_ozellikler` LONGTEXT DEFAULT NULL,
  `kullanim_alanlari` LONGTEXT DEFAULT NULL,
  `ana_gorsel` VARCHAR(255) DEFAULT NULL,
  `galeri` LONGTEXT DEFAULT NULL,
  `model_kodu` VARCHAR(100) DEFAULT NULL,
  `seo_baslik` VARCHAR(200) DEFAULT NULL,
  `seo_aciklama` VARCHAR(300) DEFAULT NULL,
  `seo_anahtar` VARCHAR(255) DEFAULT NULL,
  `sira` INT(11) DEFAULT 0,
  `one_cikan` TINYINT(1) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `goruntulenme` INT(11) DEFAULT 0,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kategori` (`kategori_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_one_cikan_aktif` (`one_cikan`, `aktif`),
  KEY `idx_sira_aktif` (`sira`, `aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. Hizmetler
-- =====================================================
CREATE TABLE IF NOT EXISTS `hizmetler` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `baslik` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `kisa_aciklama` TEXT DEFAULT NULL,
  `icerik` LONGTEXT DEFAULT NULL,
  `ikon` VARCHAR(100) DEFAULT NULL,
  `gorsel` VARCHAR(255) DEFAULT NULL,
  `seo_baslik` VARCHAR(200) DEFAULT NULL,
  `seo_aciklama` VARCHAR(300) DEFAULT NULL,
  `seo_anahtar` VARCHAR(255) DEFAULT NULL,
  `sira` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_sira_aktif` (`sira`, `aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. Statik Sayfalar (Hakkımızda, KVKK, vb.)
-- =====================================================
CREATE TABLE IF NOT EXISTS `sayfalar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `baslik` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `icerik` LONGTEXT DEFAULT NULL,
  `gorsel` VARCHAR(255) DEFAULT NULL,
  `seo_baslik` VARCHAR(200) DEFAULT NULL,
  `seo_aciklama` VARCHAR(300) DEFAULT NULL,
  `seo_anahtar` VARCHAR(255) DEFAULT NULL,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. Blog / Haberler
-- =====================================================
CREATE TABLE IF NOT EXISTS `bloglar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `baslik` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `ozet` TEXT DEFAULT NULL,
  `icerik` LONGTEXT DEFAULT NULL,
  `gorsel` VARCHAR(255) DEFAULT NULL,
  `yazar` VARCHAR(100) DEFAULT 'Enamak Makina',
  `etiketler` VARCHAR(500) DEFAULT NULL,
  `seo_baslik` VARCHAR(200) DEFAULT NULL,
  `seo_aciklama` VARCHAR(300) DEFAULT NULL,
  `seo_anahtar` VARCHAR(255) DEFAULT NULL,
  `goruntulenme` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `yayin_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_yayin` (`yayin_tarihi`, `aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. Referanslar
-- =====================================================
CREATE TABLE IF NOT EXISTS `referanslar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `firma_adi` VARCHAR(200) NOT NULL,
  `sektor` VARCHAR(100) DEFAULT NULL,
  `aciklama` TEXT DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `web` VARCHAR(255) DEFAULT NULL,
  `sira` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sira_aktif` (`sira`, `aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. Galeri
-- =====================================================
CREATE TABLE IF NOT EXISTS `galeri` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `baslik` VARCHAR(200) DEFAULT NULL,
  `aciklama` TEXT DEFAULT NULL,
  `gorsel` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(100) DEFAULT NULL,
  `sira` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sira_aktif` (`sira`, `aktif`),
  KEY `idx_kategori` (`kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. SSS
-- =====================================================
CREATE TABLE IF NOT EXISTS `sss` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `soru` VARCHAR(500) NOT NULL,
  `cevap` LONGTEXT NOT NULL,
  `kategori` VARCHAR(100) DEFAULT 'Genel',
  `sira` INT(11) DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sira_aktif` (`sira`, `aktif`),
  KEY `idx_kategori` (`kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. İletişim Mesajları
-- =====================================================
CREATE TABLE IF NOT EXISTS `iletisim_mesajlari` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ad_soyad` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telefon` VARCHAR(30) DEFAULT NULL,
  `firma` VARCHAR(150) DEFAULT NULL,
  `konu` VARCHAR(200) DEFAULT NULL,
  `mesaj` TEXT NOT NULL,
  `ip_adresi` VARCHAR(45) DEFAULT NULL,
  `okundu` TINYINT(1) DEFAULT 0,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_okundu` (`okundu`),
  KEY `idx_tarih` (`olusturma_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. Teklif Talepleri
-- =====================================================
CREATE TABLE IF NOT EXISTS `teklif_talepleri` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ad_soyad` VARCHAR(100) NOT NULL,
  `firma` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telefon` VARCHAR(30) NOT NULL,
  `urun_id` INT(11) DEFAULT NULL,
  `urun_baslik` VARCHAR(200) DEFAULT NULL,
  `miktar` VARCHAR(50) DEFAULT NULL,
  `parca_tipi` VARCHAR(200) DEFAULT NULL,
  `parca_boyut` VARCHAR(200) DEFAULT NULL,
  `gunluk_uretim` VARCHAR(200) DEFAULT NULL,
  `mesaj` TEXT DEFAULT NULL,
  `durum` ENUM('yeni','incelendi','teklif_gonderildi','kazanildi','kaybedildi') DEFAULT 'yeni',
  `notlar` TEXT DEFAULT NULL,
  `ip_adresi` VARCHAR(45) DEFAULT NULL,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_durum` (`durum`),
  KEY `idx_tarih` (`olusturma_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. Denetim Kaydı (Audit Log)
-- =====================================================
CREATE TABLE IF NOT EXISTS `denetim_kaydi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` INT(11) DEFAULT NULL,
  `kullanici_adi` VARCHAR(100) DEFAULT NULL,
  `islem` VARCHAR(100) NOT NULL,
  `tablo` VARCHAR(100) DEFAULT NULL,
  `kayit_id` INT(11) DEFAULT NULL,
  `detay` TEXT DEFAULT NULL,
  `ip_adresi` VARCHAR(45) DEFAULT NULL,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kullanici` (`kullanici_id`),
  KEY `idx_tarih` (`olusturma_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. Güncelleme Kayıtları
-- =====================================================
CREATE TABLE IF NOT EXISTS `guncellemeler` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `versiyon` VARCHAR(50) NOT NULL,
  `onceki_versiyon` VARCHAR(50) DEFAULT NULL,
  `aciklama` TEXT DEFAULT NULL,
  `degisen_dosya_sayisi` INT(11) DEFAULT 0,
  `durum` ENUM('basarili','basarisiz','devam_ediyor') DEFAULT 'devam_ediyor',
  `hata_mesaji` TEXT DEFAULT NULL,
  `kullanici_id` INT(11) DEFAULT NULL,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tarih` (`olusturma_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. Ziyaret İstatistikleri
-- =====================================================
CREATE TABLE IF NOT EXISTS `ziyaretler` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip_adresi` VARCHAR(45) DEFAULT NULL,
  `sayfa` VARCHAR(255) DEFAULT NULL,
  `referans` VARCHAR(255) DEFAULT NULL,
  `tarayici` VARCHAR(255) DEFAULT NULL,
  `tarih` DATE DEFAULT NULL,
  `olusturma_tarihi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tarih` (`tarih`),
  KEY `idx_ip_tarih` (`ip_adresi`, `tarih`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VARSAYILAN VERİLER
-- =====================================================

-- Site Ayarları
INSERT INTO `ayarlar` (`anahtar`, `deger`, `grup`, `aciklama`) VALUES
('site_baslik', 'Enamak Makina | Kumlama Makinesi İmalat ve Bakım', 'genel', 'Site başlığı'),
('site_aciklama', 'Endüstriyel kumlama makineleri imalatı, bakım ve onarım hizmetleri. Askılı, tamburlu, basınçlı ve tünel tip kumlama sistemleri.', 'genel', 'Site açıklaması'),
('site_anahtar', 'kumlama makinesi, askılı kumlama, tamburlu kumlama, basınçlı kumlama, kumlama bakım, kumlama imalatı, enamak makina', 'genel', 'SEO anahtar kelimeler'),
('firma_adi', 'Enamak Makina', 'iletisim', 'Firma adı'),
('firma_unvan', 'Enamak Makina Sanayi ve Ticaret Ltd. Şti.', 'iletisim', 'Firma tam ünvanı'),
('telefon', '+90 000 000 00 00', 'iletisim', 'Telefon numarası'),
('telefon2', '+90 000 000 00 00', 'iletisim', 'İkinci telefon'),
('whatsapp', '905000000000', 'iletisim', 'WhatsApp numarası (başında +, boşluk, tire olmadan)'),
('email', 'info@enamakmakina.com', 'iletisim', 'E-posta'),
('email2', 'satis@enamakmakina.com', 'iletisim', 'İkinci e-posta'),
('adres', 'Organize Sanayi Bölgesi, Konya / Türkiye', 'iletisim', 'Adres'),
('harita', '<iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'iletisim', 'Google harita iframe kodu'),
('calisma_saatleri', 'Pazartesi - Cumartesi: 08:30 - 18:00', 'iletisim', 'Çalışma saatleri'),
('facebook', '', 'sosyal', 'Facebook sayfası'),
('instagram', '', 'sosyal', 'Instagram sayfası'),
('linkedin', '', 'sosyal', 'LinkedIn sayfası'),
('youtube', '', 'sosyal', 'YouTube kanalı'),
('twitter', '', 'sosyal', 'Twitter / X sayfası'),
('logo', 'assets/img/logo.svg', 'tasarim', 'Logo yolu'),
('logo_beyaz', 'assets/img/logo-white.svg', 'tasarim', 'Beyaz logo yolu'),
('favicon', 'assets/img/favicon.svg', 'tasarim', 'Favicon yolu'),
('tema_renk', '#ff6b1a', 'tasarim', 'Ana tema rengi'),
('smtp_aktif', '0', 'mail', 'SMTP aktif mi (0/1)'),
('smtp_host', '', 'mail', 'SMTP sunucu'),
('smtp_port', '587', 'mail', 'SMTP port'),
('smtp_kullanici', '', 'mail', 'SMTP kullanıcı'),
('smtp_sifre', '', 'mail', 'SMTP şifre'),
('smtp_gonderen_ad', 'Enamak Makina', 'mail', 'Gönderen adı'),
('smtp_gonderen_email', 'info@enamakmakina.com', 'mail', 'Gönderen e-posta'),
('google_analytics', '', 'seo', 'Google Analytics kodu'),
('google_search_console', '', 'seo', 'Search Console doğrulama'),
('versiyon', '1.0.0', 'sistem', 'Mevcut sistem versiyonu'),
('github_repo', 'codegatr/enamakmakina', 'sistem', 'GitHub deposu'),
('github_token', '', 'sistem', 'GitHub erişim token (zorunlu: panel üzerinden girin)'),
('bakim_modu', '0', 'sistem', 'Bakım modu (0: kapalı, 1: açık)'),
('kurulum_tarihi', NOW(), 'sistem', 'Kurulum tarihi');

-- Süper admin kullanıcı (şifre: Admin.2026!)
INSERT INTO `yoneticiler` (`ad_soyad`, `kullanici_adi`, `email`, `sifre`, `rol`, `aktif`) VALUES
('Süper Yönetici', 'admin', 'admin@enamakmakina.com', '$2y$10$YourHashedPasswordWillBeReplacedByInstallScript', 'superadmin', 1);

-- Ürün Kategorileri (Kumlama Makinesi Çeşitleri)
INSERT INTO `urun_kategoriler` (`baslik`, `slug`, `aciklama`, `icerik`, `ikon`, `sira`, `aktif`) VALUES
('Askılı Kumlama Makineleri', 'askili-kumlama-makineleri', 'Büyük ve karmaşık geometrili parçalar için askı hattı üzerinde sürekli yüzey temizleme çözümleri.', '<p>Askılı kumlama makineleri, <strong>döküm, dövme, pres ve çelik konstrüksiyon parçaların</strong> yüzey temizlik işlemi için tasarlanmıştır. Parçalar monoray askı sistemine yerleştirilir ve kabin içerisinde dönerek türbin bombardımanına maruz kalır.</p><h3>Teknik Avantajları</h3><ul><li>Manganlı çelik astar (yüksek aşınma direnci)</li><li>Yüksek verimli türbin sistemi</li><li>Otomatik elevatör ve seperatör</li><li>Kartuş filtre sistemi ile tozsuz çalışma</li><li>PLC tabanlı otomasyon</li></ul>', 'hanger', 1, 1),
('Tamburlu Kumlama Makineleri', 'tamburlu-kumlama-makineleri', 'Küçük ve orta boy dökme parçalar için lastik tamburlu homojen yüzey temizleme sistemleri.', '<p>Tamburlu kumlama makineleri, <strong>cıvata, bağlantı elemanı, küçük döküm parçalar</strong> gibi dökme halde işlenebilen ürünler için idealdir. Parçalar tambur içinde sürekli döner ve her yüzeyi homojen şekilde kumlanır.</p>', 'tumble', 2, 1),
('Basınçlı Kumlama Kabinleri', 'basincli-kumlama-kabinleri', 'Hassas ve orta ölçekli parçalar için manuel basınçlı kumlama çözümleri.', '<p>Basınçlı kumlama kabinleri, <strong>boyalı, paslı, epoksi kaplı yüzeylerin temizlenmesi</strong> için tasarlanmış manuel sistemlerdir. Basınçlı hava ile aşındırıcı malzeme yüksek hızla yüzeye püskürtülür.</p>', 'cabinet', 3, 1),
('Vakumlu Kumlama Makineleri', 'vakumlu-kumlama-makineleri', 'Tozsuz çalışma gerektiren uygulamalar için vakum destekli kapalı devre kumlama sistemleri.', '<p>Vakumlu kumlama makineleri, <strong>bilgisayar ortamında 3D tasarlanmış ve özel isteklere cevap verebilecek</strong> şekilde geliştirilmiş pratik kullanımlı sistemlerdir. Özellikle laboratuvar, medikal ve elektronik sektöründe tercih edilir.</p>', 'vacuum', 4, 1),
('Tünel Tip Kumlama Makineleri', 'tunel-tip-kumlama-makineleri', 'Sürekli üretim hattı için sac, profil ve yapı elemanı kumlama çözümleri.', '<p>Kontinü (sürekli) tünel tip kumlama makineleri, <strong>yassı çelik ürünleri ve yapı elemanlarının</strong> boyama öncesi hazırlığı için imal edilir. Giriş ve çıkış ruloları ile parçalar otomatik beslenir.</p>', 'tunnel', 5, 1),
('Sac ve Profil Kumlama', 'sac-profil-kumlama-makineleri', 'Çelik konstrüksiyon sektörü için sac, kiriş ve profil kumlama sistemleri.', '<p>Sac ve profil kumlama makineleri, <strong>çelik konstrüksiyon, gemi inşaatı ve köprü imalat</strong> sektörlerinin vazgeçilmezidir. Farklı en ve kalınlıklardaki çelik yüzeyler ISO 8501-1 Sa 2.5 standardında temizlenir.</p>', 'sheet', 6, 1),
('Boru Kumlama Makineleri', 'boru-kumlama-makineleri', 'İç ve dış yüzey boru kumlama sistemleri - otomatik dönel sistem ile homojen temizlik.', '<p>Boru kumlama makineleri, <strong>boruların iç ve dış yüzeylerini</strong> kontrollü ve eşit biçimde temizler. Petrol, doğalgaz ve altyapı projelerinde kaplama öncesi kritik bir adımdır.</p>', 'pipe', 7, 1),
('Seyyar Kumlama Makineleri', 'seyyar-kumlama-makineleri', 'Saha kullanımına uygun mobil kumlama kazanı ve sistemleri.', '<p>Seyyar kumlama makineleri, <strong>sahada büyük boyutlu yüzeylerin kumlanması</strong> için tasarlanmıştır. CSK serisi kazanlar 100 litreden 600 litreye kadar farklı kapasitelerde üretilir.</p>', 'mobile', 8, 1),
('Mermer ve Doğal Taş Kumlama', 'mermer-kumlama-makineleri', 'Dekoratif yüzey işleme için doğal taş kumlama sistemleri.', '<p>Mermer kumlama makineleri, <strong>doğal taşlara mat, kaymaz ve estetik doku</strong> kazandırmak için kullanılır. Peyzaj, iç mekan ve dış cephe uygulamalarında tercih edilir.</p>', 'marble', 9, 1),
('Yedek Parça ve Sarf Malzeme', 'yedek-parca-sarf-malzeme', 'Kumlama sistemleri için orijinal yedek parça ve aşındırıcı malzemeler.', '<p>Türbin kanatları, manganlı çelik astarlar, filtre kartuşları, çelik bilye, çelik granül, korund, cam bilye ve tüm sarf malzemeleri orijinal kalitede stoğumuzdan anında temin edilir.</p>', 'parts', 10, 1);

-- Hizmetler
INSERT INTO `hizmetler` (`baslik`, `slug`, `kisa_aciklama`, `icerik`, `ikon`, `sira`, `aktif`) VALUES
('Kumlama Makinesi İmalatı', 'kumlama-makinesi-imalati', 'Projeye özel tasarım ve mühendislik ile sıfırdan kumlama makinesi üretimi.', '<p>Her kumlama projesi benzersizdir. <strong>Parça tipi, üretim kapasitesi ve proses gereksinimlerinize göre</strong> sıfırdan tasarlanan çözümler sunuyoruz. 3D CAD tasarımı, mühendislik hesapları, fabrika kabul testi (FAT) ve sahada devreye alma ekibimizle sürecin her aşamasında yanınızdayız.</p><h3>İmalat Süreçlerimiz</h3><ul><li>Proje analizi ve teknik şartname</li><li>3D CAD tasarımı ve mühendislik hesapları</li><li>Malzeme seçimi ve kalite kontrol</li><li>İmalat ve montaj</li><li>Fabrika kabul testi (FAT)</li><li>Nakliye ve kurulum</li><li>Devreye alma ve operatör eğitimi</li></ul>', 'factory', 1, 1),
('Periyodik Bakım ve Onarım', 'periyodik-bakim-onarim', 'Mevcut kumlama sistemleriniz için kapsamlı bakım, arıza teşhis ve onarım hizmetleri.', '<p>Kumlama makineleri yüksek aşınma koşullarında çalışır. Düzenli bakım, <strong>üretim sürekliliği ve makine ömrü</strong> için kritiktir. Uzman teknik ekibimiz marka bağımsız tüm kumlama makinelerine servis verir.</p><h3>Bakım Kapsamımız</h3><ul><li>Türbin balansı ve kanat değişimi</li><li>Manganlı çelik astar yenileme</li><li>Elevatör ve seperatör bakımı</li><li>Filtre sistemi temizliği</li><li>Elektrik ve pnömatik sistem kontrolü</li><li>Yağlama ve ayar işlemleri</li></ul>', 'maintenance', 2, 1),
('Yedek Parça Tedariki', 'yedek-parca-tedariki', 'Orijinal ve muadil yedek parça stoğumuzdan hızlı teslimat.', '<p>Duran üretim hattı günde binlerce TL kayıp demektir. <strong>Kritik yedek parçaları yerli stoğumuzda</strong> bulundurarak, yurtdışı tedarikin 3-6 haftalık bekleme süresini ortadan kaldırıyoruz.</p><h3>Stok Kalemlerimiz</h3><ul><li>Türbin kanatları ve distribütörler</li><li>Manganlı çelik astarlar</li><li>Elevatör kovaları ve bantları</li><li>Kartuş filtreler</li><li>Seperatör elekleri</li><li>Kumanda panosu bileşenleri</li></ul>', 'parts', 3, 1),
('Revizyon ve Modernizasyon', 'revizyon-modernizasyon', 'Eski kumlama makinelerinin güncel standartlara kavuşturulması.', '<p>10-20 yıllık kumlama makineleriniz, doğru revizyonla <strong>yeni makine performansına</strong> ulaşabilir. PLC otomasyonu, frekans kontrolörleri, modern filtre sistemleri ve enerji verimliliği iyileştirmeleri ile makinelerinizi geleceğe hazırlıyoruz.</p>', 'upgrade', 4, 1),
('Türbin Balansı', 'turbin-balansi', 'Hassas türbin balansı ile titreşim azaltma ve ömür uzatma.', '<p>Dengesiz türbin, <strong>rulman arızası, gürültü ve yüksek enerji tüketimi</strong> demektir. Hassas balans cihazlarımızla türbin kanatlarınızı dinamik olarak dengeliyoruz.</p>', 'balance', 5, 1),
('Saha Servisi', 'saha-servisi', '7/24 acil müdahale ekibi ile fabrikanızda hızlı arıza çözümü.', '<p>Acil arıza durumlarında, <strong>Türkiye genelinde saha servis ekibimiz 24-48 saat içinde</strong> fabrikanızda olur. Yol/konaklama dahil tek fiyat politikamızla sürprizsiz hizmet garantisi sunuyoruz.</p>', 'service', 6, 1),
('Teknik Danışmanlık', 'teknik-danismanlik', 'Proje bazlı mühendislik danışmanlığı ve makine seçim desteği.', '<p>Hangi kumlama teknolojisi projeniz için doğru? Askılı mı, tamburlu mu, tünel mü? <strong>Parça analizinizi yapıyor, kapasite hesaplarınızı çıkarıyor ve size özel yatırım önerisi</strong> hazırlıyoruz.</p>', 'consulting', 7, 1);

-- Statik Sayfalar
INSERT INTO `sayfalar` (`baslik`, `slug`, `icerik`, `aktif`) VALUES
('Hakkımızda', 'hakkimizda', '<h2>Enamak Makina</h2><p><strong>Enamak Makina</strong>, kumlama teknolojileri alanında <strong>imalat, bakım ve satış sonrası destek</strong> sunan bir mühendislik firmasıdır. Yıllara dayanan sektör deneyimimizi yerli mühendislik kapasitesiyle buluşturarak, Türkiye ve yurt dışı pazarlarına yüksek kaliteli kumlama sistemleri sunuyoruz.</p><h3>Misyonumuz</h3><p>Müşterilerimize, sadece makine değil; <strong>proje analizi, mühendislik tasarımı, imalat kalitesi, devreye alma ve satış sonrası destek</strong> içeren komple bir yüzey hazırlık çözümü sunmak.</p><h3>Vizyonumuz</h3><p>Kumlama makinesi sektöründe, <strong>yerli teknoloji ve yerli yedek parça stoğu</strong> ile referans alınan, yurt dışına ihracat yapan, sürdürülebilir bir mühendislik firması olmak.</p><h3>Neden Enamak Makina?</h3><ul><li><strong>Yerli Mühendislik:</strong> Her proje bilgisayar ortamında 3D tasarlanır, sonlu elemanlar analizi yapılır.</li><li><strong>Kaliteli Malzeme:</strong> Manganlı çelik astarlar, orijinal türbin kanatları, endüstriyel sınıf filtreler.</li><li><strong>Yerli Stok:</strong> Kritik yedek parçalar depomuzda; arıza durumunda 24-48 saat içinde teslimat.</li><li><strong>Marka Bağımsız Servis:</strong> Endümak, Abana, Strong, SATMAK, Saygılı veya ithal tüm markalar için bakım.</li><li><strong>Fabrika Kabul Testi:</strong> Her makine teslim öncesi tam yük testine tabidir.</li><li><strong>Sahada Devreye Alma:</strong> Kurulum ve operatör eğitimi dahil.</li></ul>', 1),
('Gizlilik Politikası', 'gizlilik-politikasi', '<h2>Gizlilik Politikası</h2><p>Enamak Makina olarak, web sitemizi ziyaret eden tüm kullanıcıların kişisel verilerinin korunmasına büyük önem veriyoruz.</p><h3>Toplanan Veriler</h3><p>İletişim ve teklif formları aracılığıyla adınız, e-posta adresiniz, telefon numaranız ve firma bilgileriniz toplanır.</p><h3>Verilerin Kullanımı</h3><p>Toplanan veriler yalnızca teklif sürecinizin ilerletilmesi, teknik destek sağlanması ve size ulaşılması amacıyla kullanılır; 3. taraflarla paylaşılmaz.</p><h3>KVKK Hakları</h3><p>6698 sayılı KVKK kapsamındaki haklarınız için <a href="mailto:info@enamakmakina.com">info@enamakmakina.com</a> adresi üzerinden bize ulaşabilirsiniz.</p>', 1),
('KVKK Aydınlatma Metni', 'kvkk', '<h2>KVKK Aydınlatma Metni</h2><p>6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında, Enamak Makina olarak veri sorumlusu sıfatıyla kişisel verilerinizi işlemekteyiz.</p><h3>İşleme Amaçları</h3><ul><li>Teklif ve siparişlerin işlenmesi</li><li>Teknik destek ve satış sonrası hizmet</li><li>Yasal yükümlülüklerin yerine getirilmesi</li></ul><p>Detaylı bilgi için <a href="mailto:info@enamakmakina.com">info@enamakmakina.com</a> adresine başvurabilirsiniz.</p>', 1),
('Çerez Politikası', 'cerez-politikasi', '<h2>Çerez Politikası</h2><p>Web sitemiz, kullanıcı deneyimini iyileştirmek için çerezler kullanmaktadır. Çerezler, küçük veri dosyalarıdır ve cihazınıza kaydedilir.</p><h3>Kullandığımız Çerez Türleri</h3><ul><li>Zorunlu çerezler (site işleyişi için)</li><li>Performans çerezleri (Google Analytics)</li><li>İşlevsel çerezler (dil, tema tercihi)</li></ul>', 1);

-- SSS
INSERT INTO `sss` (`soru`, `cevap`, `kategori`, `sira`, `aktif`) VALUES
('Hangi sektörlere kumlama makinesi imal ediyorsunuz?', 'Otomotiv yan sanayi, döküm, çelik konstrüksiyon, tarım makineleri, gemi inşaatı, demiryolu, petrokimya ve havacılık sektörlerine özel tasarım kumlama makineleri imal ediyoruz.', 'Genel', 1, 1),
('Askılı mı yoksa tamburlu kumlama makinesi mi tercih etmeliyim?', 'Pratik kural: Parça ağırlığınız 10 kg altında ve düzgün şekilliyse lastik tamburlu düşünülebilir. 50 kg üzeri, karmaşık veya uzun geometrili parçalarda askılı sistem şarttır. Teknik ekibimiz projenizi analiz ederek doğru modeli önerir.', 'Makine Seçimi', 2, 1),
('Makine teslim süresi ne kadar?', 'Standart modeller için 8-12 hafta, projeye özel imalatlar için 12-20 hafta arasında değişir. Siparişle birlikte detaylı termin planı iletiriz.', 'Satış', 3, 1),
('Fabrika Kabul Testi (FAT) yapıyor musunuz?', 'Evet. Her makine teslim öncesi tesisimizde tam yük altında test edilir. Siz veya temsilciniz testi yerinde izleyebilir; istek üzerine test raporu sunulur.', 'Kalite', 4, 1),
('Satış sonrası teknik destek kaç yıl geçerli?', 'Tüm makinelerimiz 2 yıl fabrika garantisi ile teslim edilir. Garanti sonrası da hayatı boyunca yedek parça ve teknik destek sağlanır.', 'Satış Sonrası', 5, 1),
('Marka bağımsız kumlama makinesi bakımı yapıyor musunuz?', 'Evet. Endümak, Abana, SATMAK, Saygılı, Strong Makine, Alp-Kum, Wheelabrator, Rösler ve diğer markaların kumlama makinelerine teknik destek sağlıyoruz.', 'Bakım', 6, 1),
('Sa 2.5 yüzey standardı nedir?', 'ISO 8501-1 standardına göre Sa 2.5, çok kapsamlı kumlama temizliği anlamına gelir. Yüzey, metal parlaklığı görünene kadar pas, tufal ve yabancı maddelerden arındırılır. Endüstriyel boya sistemlerinin büyük çoğunluğu bu standardı şart koşar.', 'Teknik', 7, 1),
('Hangi aşındırıcı malzemeyi önerirsiniz?', 'Seçim yüzey gereksinimine göre değişir. Çelik bilye temizlik ve peening için, çelik granül kaplama aderansı gerektiren yüzeyler için, korund hassas işler için, cam bilye parlatma için kullanılır. Teknik ekibimiz doğru granülometriyi belirler.', 'Teknik', 8, 1),
('Kumlama kabininde manganlı çelik astar neden önemli?', 'Manganlı çelik darbeyle sertleşen bir malzemedir - yani kullandıkça dayanımı artar. Standart kauçuk veya yumuşak çelik astarlı makinelere göre 3-5 kat uzun ömürlüdür. İlk yatırım farkı bakım maliyetinde hızla geri döner.', 'Teknik', 9, 1);

-- Örnek Slider
INSERT INTO `slider` (`baslik`, `alt_baslik`, `aciklama`, `buton_metin`, `buton_link`, `sira`, `aktif`) VALUES
('Endüstriyel Kumlama Sistemleri', 'Yerli Mühendislik, Global Kalite', 'Askılı, tamburlu, basınçlı ve tünel tipi kumlama makineleri. Projeye özel tasarım, fabrika kabul testi ve sahada devreye alma.', 'Ürünlerimizi İnceleyin', 'urunler.php', 1, 1),
('7/24 Teknik Servis ve Yedek Parça', 'Duran Üretim Hattı Beklemez', 'Yerli yedek parça stoğumuz ve saha servis ekibimiz ile 24-48 saat içinde fabrikanızdayız. Marka bağımsız bakım-onarım.', 'Servis Talep Et', 'iletisim.php', 2, 1),
('Projeye Özel Mühendislik', 'Standart Makine Değil, Doğru Makine', 'Parça tipi ve üretim kapasitenize göre sıfırdan tasarlanan kumlama çözümleri. 3D CAD, mühendislik hesapları ve FAT dahil.', 'Teklif Alın', 'teklif-al.php', 3, 1);

-- Örnek Referanslar
INSERT INTO `referanslar` (`firma_adi`, `sektor`, `sira`, `aktif`) VALUES
('Otomotiv Yan Sanayi A.Ş.', 'Otomotiv', 1, 1),
('Döküm Sanayi Ltd.', 'Döküm', 2, 1),
('Çelik Konstrüksiyon A.Ş.', 'Çelik Yapı', 3, 1),
('Tarım Makineleri San.', 'Tarım', 4, 1),
('Gemi İnşaat Ltd.', 'Gemi', 5, 1),
('Demiryolu Araçları A.Ş.', 'Demiryolu', 6, 1);

-- Örnek Blog yazıları
INSERT INTO `bloglar` (`baslik`, `slug`, `ozet`, `icerik`, `yazar`, `etiketler`, `aktif`) VALUES
('Askılı Kumlama Makinesi Nasıl Çalışır?', 'askili-kumlama-makinesi-nasil-calisir', 'Askılı kumlama sisteminin çalışma prensibi, bileşenleri ve kullanım alanları hakkında detaylı rehber.', '<p>Askılı kumlama makineleri, <strong>büyük ve karmaşık geometrili parçaların</strong> yüzey hazırlığında vazgeçilmez bir endüstriyel çözümdür. Bu yazımızda çalışma prensibini, ana bileşenlerini ve seçim kriterlerini inceleyeceğiz.</p><h2>Çalışma Prensibi</h2><p>Parçalar monoray askı sistemine asılır ve kabin içerisine alınır. Kabin içinde yerleştirilmiş <strong>yüksek hızlı türbinler</strong>, çelik bilye veya granül aşındırıcıyı 70-80 m/s hızla parçaların üzerine savurur. Parçalar askıda dönerek her yüzeyinin homojen kumlanmasını sağlar.</p><h2>Ana Bileşenler</h2><ul><li><strong>Türbin Ünitesi:</strong> Aşındırıcı malzemeyi hızlandırır</li><li><strong>Kabin ve Manganlı Astar:</strong> Darbeye dayanıklı iç yüzey</li><li><strong>Elevatör:</strong> Dibe dökülen kumu yukarı taşır</li><li><strong>Seperatör:</strong> Kullanılabilir kumu tozdan ayırır</li><li><strong>Filtre Sistemi:</strong> Tozsuz çalışma sağlar</li><li><strong>Kumanda Panosu:</strong> PLC tabanlı otomasyon</li></ul><h2>Seçim Kriterleri</h2><p>Doğru model seçimi için <strong>parça boyutu, ağırlığı, günlük üretim adedi ve yüzey standardı</strong> (Sa 2.5, Sa 3) belirlenmelidir. Teknik ekibimiz sizin için en uygun konfigürasyonu analiz eder.</p>', 'Enamak Mühendislik', 'askılı kumlama, türbin, manganlı çelik, sa 2.5', 1),
('Kumlama Makinesi Bakımında Dikkat Edilmesi Gerekenler', 'kumlama-makinesi-bakiminda-dikkat-edilmesi-gerekenler', 'Düzenli bakım ile kumlama makinenizin ömrünü uzatmak için bilmeniz gereken 10 kritik nokta.', '<p>Kumlama makineleri <strong>yüksek aşınma koşullarında</strong> çalışır. Düzenli bakım, üretim sürekliliği ve makine ömrü açısından kritiktir. İşte dikkat etmeniz gereken 10 nokta:</p><ol><li>Türbin kanat aşınma kontrolü (haftalık)</li><li>Manganlı astar kalınlık ölçümü (aylık)</li><li>Elevatör kova ve bant kontrolü</li><li>Seperatör elek temizliği</li><li>Filtre kartuş değişim periyodu</li><li>Aşındırıcı malzeme temiz/kirli oranı</li><li>Elektrik motor ve rulman sıcaklık kontrolü</li><li>Kapı contaları ve sızdırmazlık</li><li>Pnömatik sistem basınç kontrolü</li><li>Güvenlik sensörleri testi</li></ol>', 'Enamak Teknik', 'bakım, türbin, periyodik kontrol', 1),
('Tamburlu mu Askılı mı? Doğru Seçim Rehberi', 'tamburlu-mu-askili-mi-dogru-secim-rehberi', 'Küçük-orta boy parçalarınız için tamburlu ve askılı kumlama arasındaki farkları ve seçim kriterlerini detaylı inceliyoruz.', '<p>Endüstriyel üretim forumlarında yıllardır süren tartışma: <strong>Küçük-orta boy parçalar için askılı mı yoksa lastik tamburlu kumlama makinesi mi tercih edilmeli?</strong></p><h2>Lastik Tamburlu Kumlama</h2><p>Dökme haldeki küçük parçaları (cıvata, bağlantı elemanı, küçük döküm parçaları) <strong>hasara uğratmadan homojen biçimde</strong> işler. Elastik tambur, parçaların birbirine çarptığında zarar görmesini önler.</p><h2>Askılı Kumlama</h2><p>Asılabilir boyut ve ağırlıktaki, <strong>karmaşık veya narin geometrili</strong> büyük parçalar için tasarlanmıştır. Parçalar birbirine temas etmez, askıda döner.</p><h2>Pratik Karar Tablosu</h2><ul><li><strong>10 kg altı, düzgün şekilli parça:</strong> Tamburlu</li><li><strong>10-50 kg arası, orta boyut:</strong> Proje analizi gerekir</li><li><strong>50 kg üzeri, karmaşık/uzun:</strong> Askılı</li></ul>', 'Enamak Mühendislik', 'askılı kumlama, tamburlu kumlama, seçim rehberi', 1);

SET FOREIGN_KEY_CHECKS = 1;
