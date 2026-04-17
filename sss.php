<?php
require_once __DIR__ . '/functions.php';

$sss = $pdo->query("SELECT * FROM sss WHERE aktif = 1 ORDER BY kategori, sira ASC, id ASC")->fetchAll();

// Gruplar
$gruplar = [];
foreach ($sss as $s) {
    $k = $s['kategori'] ?: 'Genel';
    if (!isset($gruplar[$k])) $gruplar[$k] = [];
    $gruplar[$k][] = $s;
}

$sayfa_baslik = 'Sıkça Sorulan Sorular - ' . ayar('site_baslik');
$sayfa_aciklama = 'Kumlama makinaları, bakım, servis ve satış ile ilgili en çok sorulan sorular.';

$breadcrumb = [
    ['Anasayfa', 'index.php'],
    ['SSS', ''],
];

include 'header.php';
?>

<section class="sss-hero">
    <div class="container">
        <?= breadcrumb($breadcrumb) ?>
        <div class="sss-hero-inner">
            <span class="section-eyebrow">YARDIM MERKEZİ</span>
            <h1 class="sss-hero-title">Sıkça Sorulan Sorular</h1>
            <p class="sss-hero-lead">
                Kumlama makineleri, mühendislik süreci, bakım-servis ve satış sonrası destek konularında merak ettiklerinizin yanıtları.
                <?php if ($gruplar): ?>
                    <strong><?= count($sss) ?> soru &middot; <?= count($gruplar) ?> kategori</strong>
                <?php endif; ?>
            </p>

            <?php if ($gruplar): ?>
            <!-- Arama -->
            <div class="sss-search-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="sssArama" placeholder="Sorularda arayın... (örn. garanti, servis, kurulum)" autocomplete="off">
                <button type="button" class="sss-search-clear" id="sssAramaTemizle" aria-label="Temizle">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($gruplar): ?>

<!-- Kategori sekmeler -->
<section class="sss-tabs-wrap">
    <div class="container">
        <div class="sss-tabs" role="tablist">
            <button class="sss-tab active" data-kategori="tumu" role="tab" aria-selected="true">
                Tümü <span class="sss-tab-count"><?= count($sss) ?></span>
            </button>
            <?php foreach ($gruplar as $kategori => $sorular): ?>
                <button class="sss-tab" data-kategori="<?= e(slug($kategori)) ?>" role="tab" aria-selected="false">
                    <?= e($kategori) ?> <span class="sss-tab-count"><?= count($sorular) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section sss-section">
    <div class="container container-narrow">

        <!-- Soru kategorileri -->
        <div id="sssListe">
            <?php foreach ($gruplar as $kategori => $sorular): ?>
                <div class="sss-grup" data-kategori="<?= e(slug($kategori)) ?>">
                    <h2 class="sss-grup-baslik">
                        <svg class="sss-grup-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <?= e($kategori) ?>
                    </h2>
                    <div class="sss-liste">
                        <?php foreach ($sorular as $i => $s):
                            $item_id = 'sss-' . $s['id'];
                        ?>
                            <details class="sss-item" id="<?= e($item_id) ?>">
                                <summary>
                                    <span class="sss-soru-text"><?= e($s['soru']) ?></span>
                                    <span class="sss-toggle-ikon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    </span>
                                </summary>
                                <div class="sss-cevap-wrap">
                                    <div class="sss-cevap"><?= nl2br(e($s['cevap'])) ?></div>
                                    <div class="sss-cevap-foot">
                                        <span class="sss-yardim-label">Bu cevap yararlı oldu mu?</span>
                                        <a href="iletisim.php?konu=<?= urlencode($s['soru']) ?>" class="sss-daha-fazla">Daha fazla bilgi iste →</a>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Sonuç yok uyarısı -->
        <div class="sss-sonuc-yok" id="sssSonucYok" style="display:none;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="12"/><line x1="11" y1="16" x2="11.01" y2="16"/></svg>
            <h3>Sonuç bulunamadı</h3>
            <p>Aradığınız kelimelere uygun bir SSS yok. Doğrudan bize sorabilirsiniz.</p>
            <a href="iletisim.php" class="btn btn-primary">İletişime Geç</a>
        </div>

    </div>
</section>

<?php else: ?>
<section class="section">
    <div class="container container-narrow">
        <div class="empty-state">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <h3>Henüz SSS eklenmedi</h3>
            <p>Yönetim panelinden soru-cevap ekleyebilirsiniz.</p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- İletişim CTA -->
<section class="section section-top-tight">
    <div class="container container-narrow">
        <div class="sss-iletisim">
            <div class="sss-iletisim-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="sss-iletisim-metin">
                <h3>Cevabını bulamadınız mı?</h3>
                <p>Sorularınızı doğrudan mühendislik ekibimize iletebilirsiniz. İş saatlerinde 2 saat içinde, dışında en geç ertesi iş günü dönüş yapıyoruz.</p>
            </div>
            <div class="sss-iletisim-actions">
                <a href="iletisim.php" class="btn btn-primary">İletişime Geç</a>
                <a href="tel:<?= e(str_replace(' ', '', ayar('telefon'))) ?>" class="btn btn-outline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Hemen Ara
                </a>
            </div>
        </div>
    </div>
</section>

<?php if ($gruplar): ?>
<script>
(function() {
    var tabs = document.querySelectorAll('.sss-tab');
    var gruplar = document.querySelectorAll('.sss-grup');
    var aramaKutu = document.getElementById('sssArama');
    var aramaTemizle = document.getElementById('sssAramaTemizle');
    var sonucYok = document.getElementById('sssSonucYok');
    var aktifKategori = 'tumu';

    function filtrele() {
        var arama = (aramaKutu.value || '').toLowerCase().trim();
        var bulunan = 0;

        gruplar.forEach(function(grup) {
            var kat = grup.getAttribute('data-kategori');
            var sorular = grup.querySelectorAll('.sss-item');
            var gorunenSoru = 0;

            sorular.forEach(function(item) {
                var soru = (item.querySelector('.sss-soru-text').textContent || '').toLowerCase();
                var cevap = (item.querySelector('.sss-cevap').textContent || '').toLowerCase();
                var aramaEsles = arama === '' || soru.indexOf(arama) !== -1 || cevap.indexOf(arama) !== -1;
                var katEsles = aktifKategori === 'tumu' || aktifKategori === kat;

                if (aramaEsles && katEsles) {
                    item.style.display = '';
                    gorunenSoru++;
                    bulunan++;
                    // Aramada eşleşme varsa otomatik aç
                    if (arama !== '' && (soru.indexOf(arama) !== -1 || cevap.indexOf(arama) !== -1)) {
                        item.setAttribute('open', '');
                    } else if (arama === '') {
                        item.removeAttribute('open');
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            grup.style.display = gorunenSoru > 0 ? '' : 'none';
        });

        sonucYok.style.display = bulunan === 0 ? 'flex' : 'none';
        aramaTemizle.style.display = arama !== '' ? 'inline-flex' : 'none';
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            aktifKategori = tab.getAttribute('data-kategori');
            filtrele();
        });
    });

    aramaKutu.addEventListener('input', filtrele);

    aramaTemizle.addEventListener('click', function() {
        aramaKutu.value = '';
        aramaKutu.focus();
        filtrele();
    });

    // URL'de hash varsa ilgili sss'i aç ve scroll
    if (window.location.hash) {
        var hedef = document.querySelector(window.location.hash);
        if (hedef && hedef.classList.contains('sss-item')) {
            hedef.setAttribute('open', '');
            setTimeout(function() { hedef.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 150);
        }
    }
})();
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
