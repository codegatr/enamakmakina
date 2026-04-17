# Enamak Makina - Kurumsal Web Sitesi

Kumlama makinaları imalatı yapan Enamak Makina firmasının kurumsal web sitesi. PHP 8.3+ ile geliştirilmiş, kapsamlı yönetim paneline sahip, GitHub tabanlı güncelleme sistemi içerir.

## Özellikler

### Frontend
- Endüstriyel dark + turuncu (#ff6b1a) tema
- Mobile-first responsive tasarım
- Anasayfa: Slider, özellikler, kategoriler, öne çıkan ürünler, hizmetler, referanslar, blog
- Ürün Kataloğu: Kategoriye göre filtreleme, arama, teknik özellik JSON tabloları, galeri
- Hizmet sayfaları, hakkımızda, blog, SSS, galeri, referanslar
- İletişim formu + detaylı teklif talep formu
- CSRF korumalı formlar, KVKK onayı, CAPTCHA
- Schema.org JSON-LD, Open Graph, dinamik XML sitemap
- Cookie banner, WhatsApp floating button
- Bottom nav bar (mobil için)
- Fade-in animasyonları, counter animation

### Yönetim Paneli
- Dashboard: 6 istatistik kartı, son mesajlar/teklifler, hızlı erişim
- 15+ CRUD Modülü:
  - Slider (ürün, hizmet, blog, kategoriler)
  - Blog yazıları (kategori, yazar, yayın tarihi, SEO)
  - Statik sayfalar
  - Referanslar, Galeri (çoklu yükleme)
  - SSS (kategoriye göre gruplu)
  - İletişim mesajları (durum yönetimi: yeni, okundu, yanıtlandı, arşiv)
  - Teklif talepleri (durum yönetimi)
  - Yöneticiler (superadmin rol yönetimi)
  - Denetim kaydı (tüm sistem eylemleri)
  - Profil (şifre değiştirme)
- Site ayarları (tablı: genel, iletişim, sosyal, SEO, mail, tasarım, sistem)
- GitHub Releases tabanlı otomatik güncelleme sistemi

### Güvenlik
- HMAC tabanlı CSRF koruması
- PDO prepared statements
- Session regenerate, 2 saat timeout
- Brute force koruması (5 deneme / 5 dakika)
- Upload klasöründe PHP çalıştırma engeli
- .htaccess güvenlik headers (X-Frame-Options, X-Content-Type-Options, vb.)
- Hassas dosyalar (.sql, config.php, .log) erişim engelli
- HTTPS zorlama

## Kurulum

1. **Dosyaları yükleyin**: Tüm dosyaları hosting'e yükleyin.

2. **Klasör izinleri**:
```bash
chmod 755 uploads/ updates/ migrations/
chmod -R 755 uploads/*
```

3. **Kurulum sihirbazını açın**: `https://siteniz.com/install.php`

4. **Adımları takip edin**:
   - Veritabanı bilgileri
   - SQL import (otomatik, 16 tablo)
   - Yönetici hesabı oluşturma
   - Tamamla

5. **GÜVENLİK**: Kurulum bittikten sonra:
   - `install.php` dosyasını silin veya yeniden adlandırın
   - `kurulum.sql` dosyasını silin
   - `/yonetim/` adresine giriş yapın

## Stack

- **Backend**: PHP 8.3+, PDO
- **Database**: MySQL / MariaDB (utf8mb4)
- **Frontend**: Vanilla JS, CSS (Inter + Space Grotesk fontları)
- **Hosting**: DirectAdmin + LiteSpeed uyumlu
- **Deploy**: GitHub Releases + manifest.json ZIP sistemi

## Güncelleme Sistemi

- **Mevcut sürüm**: v1.0.0
- **Otomatik güncelleme**: `/yonetim/guncelleme.php` sayfasından tek tıkla
- **Güvenlik**: config.php asla güncellenmez, her güncellemeden önce yedek alınır, hata durumunda rollback
- **manifest.json**: Güncellenecek dosyaları listeler
- **DB migrations**: Otomatik çalıştırılır (manifest.json içinde migrations dizisi)

## Database Şeması

16 tablo:
- ayarlar, yoneticiler, slider
- urun_kategoriler, urunler, hizmetler
- sayfalar, bloglar, referanslar, galeri, sss
- iletisim_mesajlari, teklif_talepleri
- denetim_kaydi, guncellemeler, ziyaretler

## Varsayılan İçerik

Kurulum sonrası örnek olarak eklenen içerik:
- 10 ürün kategorisi (Askılı, Tamburlu, Basınçlı, Vakumlu, Tünel Tip, Sac/Profil, Boru, Seyyar, Mermer, Yedek Parça)
- 7 hizmet (imalat, bakım, yedek parça, revizyon, türbin balansı, saha servisi, danışmanlık)
- Varsayılan site ayarları (başlık, iletişim, SEO, sosyal medya)

## Önemli Notlar

- Türkçe karakterler sadece display text'te kullanılır, URL/href/filename'lerde ASLA
- Upload klasöründe PHP dosyası çalıştırılamaz (.htaccess ile)
- Content-after-body önlemi: tüm dosyalar `</body></html>` sonrası boş
- LiteSpeed 103KB truncation önlemi: CSS/JS dışarıda

## İletişim

**CODEGA** — Web development & hosting
Konya / Türkiye
https://codega.com.tr

© 2026 Enamak Makina
