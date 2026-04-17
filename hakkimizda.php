<?php
require_once __DIR__ . '/functions.php';

$stmt = $pdo->prepare("SELECT * FROM sayfalar WHERE slug = 'hakkimizda' AND aktif = 1 LIMIT 1");
$stmt->execute();
$sayfa = $stmt->fetch();

$sayfa_baslik = 'Hakkımızda - ' . ayar('site_baslik');
$sayfa_aciklama = $sayfa['meta_aciklama'] ?? 'Kumlama makinası sektöründe deneyimli, profesyonel ekip ve güçlü referanslarla hizmetinizdeyiz.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['Hakkımızda', ''],
];

include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <h1 class="page-title">Hakkımızda</h1>
        <p class="page-desc">Kumlama teknolojilerinde güvenilir çözüm ortağınız.</p>
    </div>
</section>

<!-- Şirket Bilgisi -->
<section class="section section-top-tight">
    <div class="container">
        <div class="hakkimizda-intro">
            <div class="intro-text">
                <span class="section-eyebrow">Biz Kimiz</span>
                <h2><?= e(ayar('site_baslik')) ?></h2>
                <?php if ($sayfa && !empty($sayfa['icerik'])): ?>
                    <div class="icerik-alani">
                        <?= $sayfa['icerik'] ?>
                    </div>
                <?php else: ?>
                    <p>Enamak Makina, kumlama teknolojileri alanında uzun yılların deneyimiyle imalat, bakım, revizyon ve yedek parça hizmetleri sunar. Askılı, tamburlu, basınçlı, tünel tip ve özel tasarım kumlama makinalarımızla binlerce üreticinin güvendiği çözüm ortağı olduk.</p>
                    <p>Modern üretim tesisimiz, deneyimli mühendis kadromuz ve geniş yedek parça stoklarımız ile Türkiye'nin dört bir yanına ulaşıyoruz. Özel projelendirmeden anahtar teslim kuruluma, saha servisinden operatör eğitimine kadar uçtan uca hizmet anlayışımızla fark yaratıyoruz.</p>
                <?php endif; ?>
            </div>
            <div class="intro-gorsel">
                <img src="<?= e(resim_url($sayfa['gorsel'] ?? null, 'assets/img/hakkimizda.jpg')) ?>" alt="Enamak Makina">
            </div>
        </div>
    </div>
</section>

<!-- Değerler -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Değerlerimiz</span>
            <h2 class="section-title">Bizi Biz Yapan İlkeler</h2>
        </div>
        <div class="degerler-grid">
            <div class="deger-card">
                <div class="deger-num">01</div>
                <h3>Kalite</h3>
                <p>Her makina, CE standartlarında üretilir ve titiz kalite kontrolden geçer. Mühendislikten montaja her adım şeffaf ve izlenebilirdir.</p>
            </div>
            <div class="deger-card">
                <div class="deger-num">02</div>
                <h3>Yenilikçilik</h3>
                <p>Ar-Ge yatırımları, yeni teknolojilerin entegrasyonu ve sürekli gelişim anlayışımızla sektörün değişim dinamiklerine öncülük ederiz.</p>
            </div>
            <div class="deger-card">
                <div class="deger-num">03</div>
                <h3>Güvenilirlik</h3>
                <p>Taahhüt ettiğimiz süre ve kaliteden taviz vermeden, satış sonrası destek dahil uzun soluklu iş birlikleri kurarız.</p>
            </div>
            <div class="deger-card">
                <div class="deger-num">04</div>
                <h3>Müşteri Odaklılık</h3>
                <p>Her projeyi müşterimizin ihtiyaçları üzerinden tasarlar, danışmanlıktan teslimata kadar şeffaf iletişim sürdürürüz.</p>
            </div>
        </div>
    </div>
</section>

<!-- İstatistikler -->
<section class="section stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-num" data-count="20">20+</div>
                <div class="stat-label">Yıllık Deneyim</div>
            </div>
            <div class="stat">
                <div class="stat-num" data-count="500">500+</div>
                <div class="stat-label">Teslim Edilmiş Proje</div>
            </div>
            <div class="stat">
                <div class="stat-num" data-count="50">50+</div>
                <div class="stat-label">Makina Modeli</div>
            </div>
            <div class="stat">
                <div class="stat-num" data-count="8">8</div>
                <div class="stat-label">İhracat Yapılan Ülke</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div class="cta-text">
                <h2>Projeniz İçin Çözüm Üretelim</h2>
                <p>İhtiyaç analizi, projelendirme ve anahtar teslim çözümler için bizi arayın.</p>
            </div>
            <div class="cta-actions">
                <a href="teklif-al.php" class="btn btn-primary btn-lg">Teklif Al</a>
                <a href="iletisim.php" class="btn btn-outline btn-lg">İletişim</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
