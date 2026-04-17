<?php
require_once __DIR__ . '/functions.php';

$sayfa = $pdo->query("SELECT * FROM sayfalar WHERE slug = 'hakkimizda' AND aktif = 1")->fetch();

$sayfa_baslik = 'Hakkımızda - ' . ayar('site_baslik');
$sayfa_aciklama = $sayfa['meta_aciklama'] ?? 'Enamak Makina - Konya merkezli kumlama makinası imalat firması. Yerli mühendislik, üretim ve satış sonrası destek.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Hakkımızda', ''],
];

include 'header.php';
?>

<!-- HERO -->
<section class="corp-hero">
    <div class="container">
        <div class="corp-hero-grid">
            <div class="corp-hero-text">
                <?= breadcrumb($breadcrumb) ?>
                <span class="section-eyebrow">KURUMSAL</span>
                <h1 class="corp-hero-title">Yerli mühendislik, <br>yüksek kaliteli üretim.</h1>
                <p class="corp-hero-lead">
                    Enamak Makina; askılı, tamburlu, basınçlı ve tünel tipi kumlama makinelerinin imalatında uzmanlaşmış, Konya merkezli bir mühendislik firmasıdır. Her proje, 3D tasarım, sonlu elemanlar analizi ve FAT testinden geçerek sahaya çıkar.
                </p>
                <div class="corp-hero-actions">
                    <a href="urunler.php" class="btn btn-primary btn-lg">Ürünlerimiz</a>
                    <a href="iletisim.php" class="btn btn-outline btn-lg">İletişim</a>
                </div>
            </div>
            <div class="corp-hero-visual">
                <div class="corp-hero-card">
                    <div class="corp-stat-big">
                        <span class="corp-stat-num">250+</span>
                        <span class="corp-stat-label">Makine Modeli</span>
                    </div>
                    <div class="corp-stat-big">
                        <span class="corp-stat-num">15+</span>
                        <span class="corp-stat-label">Yıl Deneyim</span>
                    </div>
                    <div class="corp-stat-big">
                        <span class="corp-stat-num">500+</span>
                        <span class="corp-stat-label">Kurulum</span>
                    </div>
                    <div class="corp-stat-big">
                        <span class="corp-stat-num">%100</span>
                        <span class="corp-stat-label">Yerli Üretim</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HİKAYE -->
<section class="section">
    <div class="container">
        <div class="corp-intro">
            <div class="corp-intro-left">
                <span class="section-eyebrow">HİKAYEMİZ</span>
                <h2 class="section-title" style="text-align:left;">Konya'dan Türkiye sanayisine</h2>
            </div>
            <div class="corp-intro-right">
                <?php if (!empty($sayfa['icerik'])): ?>
                    <div class="sayfa-icerik">
                        <?= $sayfa['icerik'] ?>
                    </div>
                <?php else: ?>
                    <p>Enamak Makina, kumlama teknolojileri alanında imalat, bakım ve satış sonrası destek sunan bir mühendislik firmasıdır. Yıllara dayanan sektör deneyimimizi yerli mühendislik kapasitesiyle buluşturarak, Türkiye ve yurt dışı pazarlarına yüksek kaliteli kumlama sistemleri sunuyoruz.</p>
                    <p>Konya'nın güçlü sanayi ekosisteminde kurulmuş olmak, müşterilerimizle aynı dili konuşmak ve aynı üretim koşullarını anlamak anlamına gelir. Bizim için bir makine sadece çelik ve motor değildir; müşterinin üretim hattını durdurmayan, yerli parça stoğuyla desteklenen ve 24-48 saat içinde sahada olan bir çözüm ortağıdır.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- MİSYON VİZYON -->
<section class="section section-dark">
    <div class="container">
        <div class="mv-grid">
            <div class="mv-card">
                <div class="mv-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                </div>
                <h3>Misyonumuz</h3>
                <p>Müşterilerimize, sadece bir makine değil; proje analizi, mühendislik tasarımı, imalat kalitesi, devreye alma ve satış sonrası destek içeren komple bir yüzey hazırlık çözümü sunmak.</p>
            </div>
            <div class="mv-card">
                <div class="mv-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <h3>Vizyonumuz</h3>
                <p>Kumlama makinesi sektöründe, yerli teknoloji ve yerli yedek parça stoğu ile referans alınan, yurt dışına ihracat yapan, sürdürülebilir bir mühendislik firması olmak.</p>
            </div>
            <div class="mv-card">
                <div class="mv-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3>Değerlerimiz</h3>
                <p>Dürüst mühendislik, kaliteli malzeme seçimi, satış sonrası taahhüde bağlılık ve her sözleşmede teknik şeffaflık. Katalog satıcısı değil, gerçek üretici olarak konumlanmak.</p>
            </div>
        </div>
    </div>
</section>

<!-- SÜREÇ / NASIL ÇALIŞIR -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">SÜREÇ</span>
            <h2 class="section-title">Proje başından teslimata</h2>
            <p class="section-desc">Her kumlama makinesi, teklif aşamasından sahada devreye alıma kadar 5 temel adımdan geçer. Şeffaf, izlenebilir ve müşteri odaklı bir süreç.</p>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-num">01</div>
                <div class="timeline-body">
                    <h3>Proje Analizi</h3>
                    <p>Parça boyutunuz, malzeme tipi, günlük üretim kapasitesi ve kalite hedefiniz analiz edilir. İhtiyaç doğru model belirlenir.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">02</div>
                <div class="timeline-body">
                    <h3>Mühendislik Tasarımı</h3>
                    <p>3D CAD çizimler, sonlu elemanlar analizi (FEA), türbin yerleşim hesapları ve filtreleme kapasitesi müşteri onayına sunulur.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">03</div>
                <div class="timeline-body">
                    <h3>İmalat</h3>
                    <p>Manganlı çelik astarlar, orijinal türbinler ve endüstriyel filtrelerle üretim yapılır. Her ana komponent test edilir.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">04</div>
                <div class="timeline-body">
                    <h3>Fabrika Kabul Testi (FAT)</h3>
                    <p>Makine, tesliminden önce tam yük altında test edilir. Müşteri isterse teste katılabilir. Raporlu teslimat garantidir.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">05</div>
                <div class="timeline-body">
                    <h3>Sahada Devreye Alma</h3>
                    <p>Makine sahanıza kurulur, kalibrasyon yapılır, operatör eğitimi verilir ve 2 yıllık teknik destek başlatılır.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEDEN ENAMAK -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">NEDEN BİZ</span>
            <h2 class="section-title">Enamak farkı</h2>
            <p class="section-desc">"Her işi yapan makine, hiçbir işi doğru yapmaz." Biz gerçek üreticiyiz, konumlanmamız nettir.</p>
        </div>

        <div class="reason-grid">
            <div class="reason-card">
                <div class="reason-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <h3>Yerli Mühendislik</h3>
                <p>Her proje bilgisayar ortamında 3D tasarlanır, sonlu elemanlar analizi yapılır. İthal kopyalama değil, projenize özel çözüm.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Hızlı Yedek Parça</h3>
                <p>Türbin kanatları, manganlı astarlar, filtre kartuşları — kritik yedekler depomuzda. 24-48 saat içinde sahada.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.91 8.84L8.56 21.19a4.5 4.5 0 0 1-6.36-6.36l12.35-12.35A3.54 3.54 0 0 1 19.5 2.5a3.54 3.54 0 0 1 1.41 6.34z"/></svg>
                </div>
                <h3>Manganlı Çelik Astar</h3>
                <p>Kabin içi astar, aşınmaya karşı en dirençli malzemelerden biri olan manganlı çelikten imal edilir. Kimyasal analiz sertifikalı.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3>Marka Bağımsız Servis</h3>
                <p>Endümak, Abana, Strong, SATMAK, Saygılı veya ithal tüm markalar için bakım ve onarım hizmeti sunuyoruz.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3>Fabrika Kabul Testi</h3>
                <p>Her makine teslim öncesi tam yük testine tabidir. Müşteri isterse teste katılır, imalat kalitesini yerinde görür.</p>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3>Şeffaf Teklif</h3>
                <p>Tekliflerimizde astar malzemesi, türbin motor gücü, filtre sınıfı ve garanti kapsamı açıkça belirtilir. Sürpriz yok.</p>
            </div>
        </div>
    </div>
</section>

<!-- CEO SÖZÜ -->
<section class="section">
    <div class="container container-narrow">
        <div class="quote-card">
            <svg class="quote-icon" width="40" height="40" viewBox="0 0 24 24" fill="currentColor"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
            <blockquote>
                "Katalog satıcılarının bıraktığı boşluk, gerçek üreticinin alanıdır. Müşterimiz aradığında makinenin hangi motorla, hangi astar malzemesiyle, hangi filtreyle geldiğini tek tek söyleyebilmeliyiz. Mühendislik şeffaflıktır. Şeffaflık olmadan güven inşa edilmez."
            </blockquote>
            <div class="quote-author">
                <strong>Yunus</strong>
                <span>Kurucu &amp; Genel Müdür</span>
            </div>
        </div>
    </div>
</section>

<!-- FİNAL CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div class="cta-text">
                <h2>Projeniz için konuşalım</h2>
                <p>Parça tipinize ve üretim kapasitenize uygun makineyi birlikte seçelim. İlk görüşme ücretsiz, teklif şeffaf, teslimat taahhüt altındadır.</p>
            </div>
            <div class="cta-actions">
                <a href="teklif-al.php" class="btn btn-primary btn-lg">Teklif Al</a>
                <a href="iletisim.php" class="btn btn-outline btn-lg">İletişim</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
