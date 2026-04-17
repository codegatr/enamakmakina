-- =====================================================================
-- Enamak Makina v1.3.0 - Content Migration
-- Gerçek ürünler, hizmetler, slider, ayarlar
-- =====================================================================

-- Varsayılan örnek verileri temizle (v1.0.0 kurulumundan kalan)
DELETE FROM urunler WHERE id > 0;
DELETE FROM urun_kategoriler WHERE id > 0;
DELETE FROM hizmetler WHERE id > 0;
DELETE FROM slider WHERE id > 0;
DELETE FROM referanslar WHERE id > 0;

-- =====================================================================
-- ÜRÜN KATEGORİLERİ (8 kategori, görselleri bağlı)
-- =====================================================================
INSERT INTO urun_kategoriler (id, ad, slug, aciklama, gorsel, aktif, sira) VALUES
(1, 'Askılı Kumlama Makineleri',
 'askili-kumlama-makineleri',
 'Büyük, karmaşık veya hassas geometriye sahip parçalar için ideal çözüm. Parçalar askı sistemine yüklenir, kabin içinde dönerken türbinlerin fırlattığı çelik bilye veya granül ile her yüzey eşit biçimde kumlanır. Kör nokta sıfır, tekrarlanabilirlik %100.',
 'uploads/kategoriler/askili-kumlama.svg', 1, 1),

(2, 'Tamburlu Kumlama Makineleri',
 'tamburlu-kumlama-makineleri',
 'Dökme haldeki küçük parçalar (cıvata, dişli, bağlantı elemanları, küçük döküm parçaları) için tasarlandı. Lastik kaplı tambur, parçaların birbirine çarparak hasar görmesini önler. Yüksek verimlilik, tek seferde yüzlerce parça.',
 'uploads/kategoriler/tamburlu-kumlama.svg', 1, 2),

(3, 'Basınçlı Kumlama Kabinleri',
 'basincli-kumlama-kabinleri',
 'Hassas ve küçük parçalar için kontrollü kumlama. Operatör, eldivenli kabin önünden parçayı manuel olarak yönlendirir. Restorasyon, prototip üretimi ve özel yüzey işlemleri için vazgeçilmez.',
 'uploads/kategoriler/basincli-kumlama.svg', 1, 3),

(4, 'Tünel Tipi Kumlama Makineleri',
 'tunel-tip-kumlama-makineleri',
 'Seri üretim hatları için konveyörlü otomatik kumlama sistemi. Parça tünelin bir ucundan girer, yüzeyi temizlenmiş halde diğer uçtan çıkar. Yüksek tonajlı dökümhane, otomotiv yan sanayi ve çelik konstrüksiyon için ideal.',
 'uploads/kategoriler/tunel-tipi-kumlama.svg', 1, 4),

(5, 'Vakumlu Kumlama Makineleri',
 'vakumlu-kumlama-makineleri',
 'Toz sızıntısı olmadan çalışan, kapalı devre kumlama sistemi. Aşındırıcı otomatik geri toplanır, filtre edilir ve tekrar kullanılır. Hassas elektronik parçalar, tıbbi ekipman ve gıda teması olan yüzeyler için.',
 'uploads/kategoriler/vakumlu-kumlama.svg', 1, 5),

(6, 'Sac ve Profil Kumlama',
 'sac-ve-profil-kumlama',
 'Düz sac, profil, boru ve kutu profil gibi uzun parçalar için roller konveyörlü kumlama sistemi. Boyama öncesi yüzey hazırlığında Sa 2.5 standardına ulaşır. Çelik konstrüksiyon firmaları için temel ekipman.',
 'uploads/kategoriler/sac-profil-kumlama.svg', 1, 6),

(7, 'Boru Kumlama Makineleri',
 'boru-kumlama-makineleri',
 'İç yüzey ve dış yüzey kumlama özelliğine sahip özel tasarım. Boru ekseni etrafında döner, eş zamanlı olarak dıştan türbin, içten basınçlı nozzle ile çalışır. Petrol, doğalgaz ve su hattı boru üreticileri için.',
 'uploads/kategoriler/boru-kumlama.svg', 1, 7),

(8, 'Seyyar Kumlama Makineleri',
 'seyyar-kumlama-makineleri',
 'Tekerlekli, taşınabilir basınçlı kumlama üniteleri. Şantiye, gemi bakımı, saha onarımı gibi sabit bir tesiste yapılamayan işler için. 50L''den 500L''ye kadar farklı kapasitelerde.',
 'uploads/kategoriler/seyyar-kumlama.svg', 1, 8);

-- =====================================================================
-- ÜRÜNLER (14 adet, her kategoriden örnekler)
-- =====================================================================
INSERT INTO urunler (kategori_id, ad, slug, kisa_aciklama, aciklama, gorsel, model, ozellikler, aktif, one_cikan, sira) VALUES

-- Askılı (kategori_id=1)
(1, 'AK-100 Askılı Kumlama Makinesi', 'ak-100-askili-kumlama-makinesi',
 'Kompakt atölye tipi. 800mm sepet çapı, 300kg askı kapasitesi. Küçük ve orta ölçekli imalatçılar için ideal başlangıç modeli.',
 '<h3>AK-100 Hakkında</h3><p>AK-100, Enamak Makina''nın giriş seviyesi askılı kumlama makinesidir. 800mm sepet çapı ve 300kg askı kapasitesi ile küçük-orta ölçekli atölyelerin günlük ihtiyaçlarını karşılar. Manganlı çelik astarları, 2 adet 11kW türbini ve kartuş filtre sistemiyle birlikte gelir.</p><h3>Kullanım Alanları</h3><ul><li>Döküm parçaların yüzey temizliği</li><li>Pas ve çapak giderme</li><li>Boya ve kaplama öncesi hazırlık</li><li>Revizyon işleri</li></ul><h3>Avantajlar</h3><ul><li>Sa 2.5 standardına uygun sonuç</li><li>Kompakt gabari (atölyelerde az yer kaplar)</li><li>Enerji verimli çalışma</li><li>Kolay bakım</li></ul>',
 'uploads/urunler/askili-ak-100.svg', 'AK-100',
 'Sepet Çapı: 800 mm\nAskı Kapasitesi: 300 kg\nTürbin: 2 x 11 kW\nFiltre: Kartuş tipi\nEnerji: 380V / 50Hz\nGabari: 2400 x 2200 x 2800 mm',
 1, 1, 1),

(1, 'AK-200 Çift Y-Askılı Kumlama', 'ak-200-cift-askili-kumlama',
 'Yüksek kapasiteli seri üretim için çift askılı model. 1200mm sepet, 500kg x 2 kapasite. 4 türbin ile Sa 2.5 garantisi.',
 '<h3>AK-200 Çift Askılı</h3><p>Bir parça kumlanırken diğer parça yüklenebilir. Bu sayede makine boşta kalmaz, saatlik üretim kapasitesi 2 katına çıkar. Y tipi askı sistemi, parçaların sallanmasını engeller; tam kör nokta temizliği.</p><h3>Öne Çıkan Özellikler</h3><ul><li>2 askı pozisyonu (bir yüklenir, biri kumlanır)</li><li>4 türbin (2 yandan + 2 üstten)</li><li>PLC kontrol paneli, dokunmatik ekran</li><li>Otomatik aşındırıcı elek ve geri dönüşüm</li></ul>',
 'uploads/urunler/askili-ak-200.svg', 'AK-200',
 'Sepet Çapı: 1200 mm\nAskı Kapasitesi: 2 x 500 kg\nTürbin: 4 x 11 kW\nKontrol: PLC + HMI\nFiltre: Kartuş, çift kademe\nGabari: 3800 x 2800 x 3400 mm',
 1, 1, 2),

-- Tamburlu (kategori_id=2)
(2, 'LT-100 Lastik Tamburlu Kumlama', 'lt-100-lastik-tamburlu-kumlama',
 'Dökme parçalar için ideal başlangıç. 100kg tambur kapasitesi, 5.5kW türbin, 100kg/dk granül akışı. Cıvata ve küçük döküm parçalar için.',
 '<h3>LT-100</h3><p>Dökme haldeki küçük parçaları hasara uğratmadan, homojen biçimde işleyen lastik tamburlu sistem. Atölye ölçeğindeki üretimler için optimize edilmiş model.</p><h3>Nerelerde Kullanılır?</h3><ul><li>Cıvata, somun, pul</li><li>Küçük dökümhane parçaları (zamak, alüminyum)</li><li>Sertleştirme/tavlama sonrası oksit tabakası temizliği</li><li>Dişli, rulman yatağı gibi makine parçaları</li></ul><h3>Önemli Not</h3><p>Lastik tamburlu sistemlerde <strong>yalnızca küresel çelik bilye (steel shot)</strong> kullanılabilir. Köşeli grit tamburu delip deşik eder. Biz her teslimatta bu uyarıyı yazılı olarak müşteriye sunuyoruz.</p>',
 'uploads/urunler/tamburlu-lt-100.svg', 'LT-100',
 'Tambur Kapasitesi: 100 kg\nGranül Akışı: 100 kg/dk\nTürbin Motor: 5.5 kW\nLastik Sertliği: Shore 65A\nFiltre: Kartuş\nGabari: 2200 x 1800 x 2400 mm',
 1, 1, 3),

(2, 'LT-300 Yüksek Kapasiteli Tamburlu', 'lt-300-yuksek-kapasiteli-tamburlu',
 'Seri üretim için yüksek tonaj modeli. 300kg tambur, 7.5kW türbin, 130kg/dk akış. Otomotiv yan sanayi için.',
 '<h3>LT-300</h3><p>Yüksek adetli seri üretim için tasarlanmış lastik tamburlu kumlama makinesi. Otomatik yükleme-boşaltma ile tek vardiyada 3-4 ton parça işlenebilir. Kauçuk tambur, Shore 70A sertlik değeri ile optimum darbe iletim oranına sahiptir.</p>',
 'uploads/urunler/tamburlu-lt-300.svg', 'LT-300',
 'Tambur Kapasitesi: 300 kg\nGranül Akışı: 130 kg/dk\nTürbin Motor: 7.5 kW\nLastik Sertliği: Shore 70A\nOtomatik Yükleme: Opsiyonel\nGabari: 3000 x 2200 x 2800 mm',
 1, 0, 4),

-- Basınçlı Kabin (kategori_id=3)
(3, 'BK-60 Basınçlı Kumlama Kabini', 'bk-60-basincli-kumlama-kabini',
 'Küçük atölyeler için kompakt basınçlı kabin. 60L hazne, eldivenli operatör bölmesi, LED aydınlatma.',
 '<h3>BK-60</h3><p>Küçük parçaların hassas kumlanması için tasarlanmış manuel operatörlü basınçlı kumlama kabini. Tamperli cam pencere, ergonomik eldiven pozisyonları ve iç LED aydınlatma ile operatör konforunu ön plana çıkarır.</p><h3>Tipik Uygulama Alanları</h3><ul><li>Antika ve restorasyon işleri</li><li>Motor parçaları temizliği</li><li>Prototip yüzey hazırlığı</li><li>Dekoratif kumlama (cam, mermer)</li></ul>',
 'uploads/urunler/basincli-bk-60.svg', 'BK-60',
 'Hazne Hacmi: 60 L\nÇalışma Basıncı: 4-7 bar\nNozzle Çapı: 6-10 mm seçilebilir\nFiltre: Dahili kartuş\nGabari: 1400 x 900 x 1800 mm',
 1, 0, 5),

(3, 'BK-120 Endüstriyel Basınçlı Kabin', 'bk-120-endustriyel-basincli',
 '120L hazne, büyük parçalar için geniş çalışma alanı. Otomatik geri dönüşüm sistemi.',
 '<h3>BK-120</h3><p>Endüstriyel ölçekli hassas kumlama işleri için 120 litrelik basınçlı kabin. Aşındırıcı otomatik geri toplama sistemi ile malzeme kaybı minimize edilir. Turnuvalı taban ızgarası, ağır parçaların kolay yerleştirilmesini sağlar.</p>',
 'uploads/urunler/basincli-bk-120.svg', 'BK-120',
 'Hazne Hacmi: 120 L\nÇalışma Basıncı: 5-8 bar\nÇalışma Alanı: 1000 x 800 mm\nAşındırıcı Geri Dönüşüm: Pnömatik\nGabari: 1800 x 1200 x 2000 mm',
 1, 0, 6),

-- Tünel (kategori_id=4)
(4, 'TK-600 Tünel Tipi Kumlama', 'tk-600-tunel-tipi-kumlama',
 'Sac ve profil için 600mm geniş konveyörlü tünel. Saatlik 8 metre kumlama hızı. 4 türbin.',
 '<h3>TK-600</h3><p>600mm geniş roller konveyörlü, 4 adet üst/alt türbin konfigürasyonlu tünel tipi kumlama makinesi. Orta ölçekli çelik konstrüksiyon firmaları için ideal. Çift yön akış ile hem giriş hem çıkış tarafından yükleme yapılabilir.</p>',
 'uploads/urunler/tunel-tk-600.svg', 'TK-600',
 'Konveyör Genişliği: 600 mm\nÇalışma Hızı: 1-8 m/dk (ayarlanabilir)\nTürbin Sayısı: 4 x 15 kW\nMaks. Parça Yüksekliği: 400 mm\nFiltreleme: Siklon + kartuş\nGabari: 6000 x 2400 x 3200 mm',
 1, 1, 7),

(4, 'TK-1200 Büyük Tünel Kumlama', 'tk-1200-buyuk-tunel-kumlama',
 'Dev yapı elemanları için. 1200mm konveyör, 8 türbin, otomatik sac besleme.',
 '<h3>TK-1200</h3><p>Ağır sanayi, gemi inşaatı ve büyük çelik konstrüksiyon için. 1200mm konveyör, 8 türbin, saatlik 20 ton işleme kapasitesi. Çift katmanlı filtre sistemi, çevresel mevzuata tam uyum.</p>',
 'uploads/urunler/tunel-tk-1200.svg', 'TK-1200',
 'Konveyör Genişliği: 1200 mm\nÇalışma Hızı: 0.5-10 m/dk\nTürbin Sayısı: 8 x 18.5 kW\nMaks. Parça Yüksekliği: 800 mm\nGabari: 9000 x 3400 x 4200 mm',
 1, 0, 8),

-- Vakumlu (kategori_id=5)
(5, 'VK-80 Vakumlu Kumlama Sistemi', 'vk-80-vakumlu-kumlama',
 'Toz sızıntısız kapalı devre. 80L kapasite. Otomatik aşındırıcı geri kazanım.',
 '<h3>VK-80</h3><p>Temiz oda standartlarına uygun, toz sızıntısız vakumlu kumlama sistemi. Aşındırıcı kapalı devrede döner, filtre edilir ve tekrar kullanılır. Hassas elektronik bileşenler, tıbbi cihaz parçaları ve gıda teması olan yüzeyler için.</p>',
 'uploads/urunler/vakumlu-vk-80.svg', 'VK-80',
 'İşlem Haznesi: 80 L\nVakum Gücü: 350 mbar\nAşındırıcı Geri Kazanım: Otomatik\nFiltre: HEPA + kartuş\nGabari: 1600 x 1200 x 2000 mm',
 1, 0, 9),

-- Sac ve Profil (kategori_id=6)
(6, 'SPK-1500 Sac ve Profil Kumlama Hattı', 'spk-1500-sac-profil-kumlama',
 '1500mm geniş komplex sac hattı. 6 türbin, otomatik fırça temizleme, boya kabini entegrasyonu.',
 '<h3>SPK-1500</h3><p>Çelik konstrüksiyon firmaları için komple sac ve profil kumlama hattı. Roller konveyör, 6 türbin (3 üst + 3 alt), fırçalı parça temizleme modülü ve opsiyonel boya kabini entegrasyonu ile tek geçişte Sa 2.5 standardında yüzey elde edilir.</p>',
 'uploads/urunler/sac-profil-spk-1500.svg', 'SPK-1500',
 'Konveyör Genişliği: 1500 mm\nTürbin Sayısı: 6 x 22 kW\nKonveyör Hızı: 0.5-5 m/dk\nFırça Modülü: Dahili\nGabari: 8000 x 3200 x 3600 mm',
 1, 1, 10),

-- Boru (kategori_id=7)
(7, 'BRK-400 İç-Dış Boru Kumlama', 'brk-400-ic-dis-boru-kumlama',
 'Boru ekseninde dönerek hem iç hem dış yüzey kumlar. 400mm maks. çap.',
 '<h3>BRK-400</h3><p>Petrol, doğalgaz ve su hattı boru üreticileri için özel tasarım. Boru merkez etrafında döner; dıştan 4 türbin, içten basınçlı nozzle sistemi ile eş zamanlı çalışır. İç yüzey Sa 2.5 garantisi.</p>',
 'uploads/urunler/boru-brk-400.svg', 'BRK-400',
 'Maks. Boru Çapı: 400 mm\nMin. Boru Çapı: 80 mm\nBoru Uzunluğu: 1-12 m\nDış Türbin: 4 x 11 kW\nİç Nozzle Basıncı: 6-8 bar\nGabari: 14000 x 2600 x 3000 mm',
 1, 0, 11),

-- Seyyar (kategori_id=8)
(8, 'SK-50 Seyyar Kumlama Ünitesi', 'sk-50-seyyar-kumlama',
 'Tekerlekli, 50L hazne. Şantiye ve saha onarımı için. 8-15m hortum.',
 '<h3>SK-50</h3><p>Taşınabilir basınçlı kumlama ünitesi. Gemi bakımı, şantiye boyama öncesi yüzey hazırlığı, köprü ve tank saha onarımı gibi sabit tesisin ulaşamadığı işler için. Çelik şaside yüksek dayanım.</p>',
 'uploads/urunler/seyyar-sk-50.svg', 'SK-50',
 'Hazne Hacmi: 50 L\nÇalışma Basıncı: 7 bar\nHortum Uzunluğu: 8-15 m\nAğırlık: 85 kg\nTekerlek Çapı: 250 mm',
 1, 0, 12),

(8, 'SK-200 Büyük Seyyar Kumlama', 'sk-200-buyuk-seyyar-kumlama',
 '200L kapasite, endüstriyel saha uygulamaları için. Dizel kompresörle uyumlu.',
 '<h3>SK-200</h3><p>Endüstriyel ölçekli saha kumlama işleri için. Büyük depo kapağı ve uzun hortum ile operatör yorulmadan uzun süre çalışabilir. Dizel veya elektrikli kompresörlerle uyumlu.</p>',
 'uploads/urunler/seyyar-sk-50.svg', 'SK-200',
 'Hazne Hacmi: 200 L\nÇalışma Basıncı: 7-10 bar\nHortum Uzunluğu: 15-30 m\nAğırlık: 220 kg\nAltyapı: Gemi, tank, köprü bakımı',
 1, 0, 13),

-- Özel İmalat
(1, 'Projeye Özel Kumlama Makinesi', 'projeye-ozel-kumlama-makinesi',
 'Standart makine çözüm getirmiyorsa — projenize özel tasarım, imalat ve devreye alma.',
 '<h3>Özel İmalat</h3><p>Standart modeller ihtiyacınızı karşılamıyorsa, Enamak mühendislik ekibi size özel bir makine tasarlar. Süreç:</p><ol><li><strong>Analiz:</strong> Parça geometrisi, üretim tonajı, yer kısıtlamaları ölçülür</li><li><strong>3D Tasarım:</strong> CAD modeli hazırlanır, sonlu elemanlar analizi yapılır</li><li><strong>Proforma:</strong> Şeffaf maliyet kalemleri ile teklif sunulur</li><li><strong>İmalat:</strong> Onay sonrası 60-120 günde üretim</li><li><strong>FAT + Devreye Alma:</strong> Fabrikamızda test, sahanızda kurulum</li></ol><p>Standart ürünlere girmeyen her proje burada çözülür: konveyör entegrasyonlu hat, özel boyut kabin, çift rotor sistem, ATEX uyumlu kumlama...</p>',
 'uploads/urunler/askili-ak-200.svg', 'Özel',
 'Teslim Süresi: 60-120 gün\nGaranti: 2 yıl + uzatılabilir\nFAT Testi: Fabrikamızda\nKurulum: Dahil\nEğitim: Operatör + teknisyen',
 1, 1, 99);

-- =====================================================================
-- HİZMETLER (7 adet)
-- =====================================================================
INSERT INTO hizmetler (ad, slug, kisa_aciklama, aciklama, gorsel, ikon, aktif, sira) VALUES

('Mühendislik ve Proje Tasarımı', 'muhendislik-proje-tasarimi',
 'Parça tipinize, üretim kapasitenize ve mekan koşullarınıza göre özel makine tasarımı. 3D CAD, FEA, türbin hesapları.',
 '<h3>Mühendislik Hizmetimiz</h3><p>Her kumlama makinesi, sahaya çıkmadan önce mühendislik masasından geçer. 3D CAD çizimler, sonlu elemanlar analizi (FEA), türbin yerleşim optimizasyonu ve filtreleme kapasite hesaplamaları yapılır.</p><h3>Süreç</h3><ol><li>Parça analizi ve üretim tonajı belirleme</li><li>Kabin, türbin ve askı/konveyör tasarımı</li><li>Müşteri onayı için 3D render ve teknik çizimler</li><li>FEA raporu ile yapısal güvence</li><li>Şeffaf maliyet analizi</li></ol>',
 'uploads/hizmetler/muhendislik.svg', 'blueprint', 1, 1),

('Bakım ve Onarım', 'bakim-ve-onarim',
 'Tüm kumlama makineleri için bakım ve onarım. Marka bağımsız: Endümak, Abana, Strong, SATMAK, ithal markalar.',
 '<h3>Bakım Anlaşmalarımız</h3><p>Kumlama makinenizin ömrünü uzatmak ve beklenmedik arızaları önlemek için periyodik bakım programları:</p><ul><li><strong>Altın Paket:</strong> 3 ayda bir tam kontrol + sarf malzeme</li><li><strong>Standart Paket:</strong> 6 ayda bir bakım ziyareti</li><li><strong>Acil Onarım:</strong> Arıza durumunda 24-48 saat içinde sahada</li></ul><h3>Marka Bağımsız</h3><p>Sadece kendi ürettiğimiz makinelere değil, tüm markalara servis veriyoruz: Endümak, Abana Makina, Strong Makine, SATMAK, Saygılı, Sedmak, Tolermak ve ithal markalar.</p>',
 'uploads/hizmetler/bakim-onarim.svg', 'tool', 1, 2),

('Devreye Alma ve Kurulum', 'devreye-alma-kurulum',
 'Makinenin sahaya nakli, montajı, kalibrasyonu ve operatör eğitimi. 2 yıllık ücretsiz teknik destek başlangıcı.',
 '<h3>Kurulum Süreci</h3><ol><li><strong>Saha Ön İncelemesi:</strong> Teslim öncesi teknik ekip sahayı ziyaret eder, enerji/hava/drenaj altyapısını kontrol eder</li><li><strong>Nakil:</strong> Enamak nakliye ekibi makineyi özel ambalajla teslim eder</li><li><strong>Montaj:</strong> Kompresör, filtre, aşındırıcı hattı bağlantıları yapılır</li><li><strong>Kalibrasyon:</strong> Tam yük testi, türbin dengelemesi, filtre çekiş optimizasyonu</li><li><strong>Eğitim:</strong> 2-3 günlük operatör ve bakım teknisyeni eğitimi</li></ol>',
 'uploads/hizmetler/devreye-alma.svg', 'truck', 1, 3),

('Yedek Parça Tedariki', 'yedek-parca-tedariki',
 'Türbin kanatları, manganlı astarlar, filtre kartuşları, O-ring setleri. Kritik yedekler yerli depomuzda.',
 '<h3>Yedek Parça Stokumuz</h3><p>Kumlama makinenizin kritik yedek parçaları depomuzda. Arıza durumunda yurtdışı bekleme süresi yok.</p><h3>Stokumuzda Olan Ana Parçalar</h3><ul><li><strong>Türbin Kanatları</strong> - mangan alaşımlı, tüm standart modeller</li><li><strong>Manganlı Çelik Astarlar</strong> - yan, taban, tavan</li><li><strong>Filtre Kartuşları</strong> - HEPA, standart, G4</li><li><strong>Kauçuk Tamburlar</strong> - Shore 65A ve 70A</li><li><strong>O-ring ve Conta Setleri</strong></li><li><strong>Elevatör Kepçeleri</strong></li></ul>',
 'uploads/hizmetler/yedek-parca.svg', 'gear', 1, 4),

('Operatör ve Bakım Eğitimi', 'operator-bakim-egitimi',
 'Makine kullanıcılarınız için teknik eğitim. Sa 2.5 standardı, aşındırıcı seçimi, günlük bakım kontrolü.',
 '<h3>Eğitim Programlarımız</h3><p>Kumlama makinesinin performansı %50 makineden, %50 operatörden gelir. Doğru kullanım bilgisi olmadan en iyi makine bile zayıf performans gösterir.</p><h3>Eğitim Modülleri</h3><ul><li>ISO 8501-1 ve Sa yüzey temizlik standartları</li><li>Aşındırıcı türleri ve seçim kriterleri (çelik bilye, grit, garnet, silisyum karbür)</li><li>Kabin içi astar kontrolü ve değişim prosedürü</li><li>Türbin kanat yıpranma analizi</li><li>Filtre bakım ve temizleme rutini</li><li>İş güvenliği ve toz maruziyeti yönetimi</li></ul>',
 'uploads/hizmetler/egitim.svg', 'book', 1, 5),

('7/24 Teknik Destek Hattı', '7-24-teknik-destek',
 'Arıza, kullanım sorusu, yedek parça talebi için 7/24 telefon ve WhatsApp hattı. Uzak masaüstü destek.',
 '<h3>7/24 Destek Kanallarımız</h3><p>Makineniz duruyor. Üretim hattı bekliyor. Hemen ulaşılabilir biri gerekiyor. Biz buradayız.</p><h3>Erişim Kanalları</h3><ul><li><strong>Telefon:</strong> 7/24 açık teknik destek hattı</li><li><strong>WhatsApp:</strong> Fotoğraf/video gönderin, hızlı tanı yapalım</li><li><strong>Uzak Masaüstü:</strong> PLC arızalarında TeamViewer ile müdahale</li><li><strong>Saha Ziyareti:</strong> Bölgenize göre 24-48 saat içinde</li></ul>',
 'uploads/hizmetler/teknik-destek.svg', 'headset', 1, 6),

('Modernizasyon ve Revizyon', 'modernizasyon-revizyon',
 'Eskiyen makineyi yenileyin. PLC güncellemesi, türbin yenileme, filtre modernizasyonu, otomasyon entegrasyonu.',
 '<h3>Neden Modernizasyon?</h3><p>10-15 yaşında bir kumlama makinesi atılacak anlamına gelmez. Enamak modernizasyon programı ile ana gövdeyi koruyarak modern teknolojiye geçiş yaparsınız:</p><ul><li><strong>PLC Güncellemesi:</strong> Eski role mantık kontrol yerine Siemens S7 veya Mitsubishi FX serisi PLC</li><li><strong>Türbin Yenileme:</strong> Eski düşük verim türbinler, yeni yüksek verimli motorlarla değiştirilir</li><li><strong>Filtre Modernizasyonu:</strong> Torbalı filtre yerine kartuş filtre — %30 daha az yer, %40 daha iyi verim</li><li><strong>Otomasyon:</strong> HMI dokunmatik ekran, uzaktan izleme, üretim raporlama</li></ul>',
 'uploads/hizmetler/modernizasyon.svg', 'refresh', 1, 7);

-- =====================================================================
-- SLIDER (3 slayt)
-- =====================================================================
INSERT INTO slider (baslik, ust_baslik, aciklama, gorsel, buton_yazi, buton_link, buton2_yazi, buton2_link, aktif, sira) VALUES

('Endüstriyel Kumlama Sistemleri',
 'Yerli Mühendislik, Yüksek Kalite',
 'Askılı, tamburlu, basınçlı ve tünel tipi kumlama makineleri. Projeye özel tasarım, fabrika kabul testi ve sahada devreye alma.',
 'uploads/slider/slider-1.svg',
 'Ürünlerimizi İnceleyin', 'urunler.php',
 'Teklif Al', 'teklif-al.php',
 1, 1),

('7/24 Teknik Servis ve Yedek Parça',
 'Duran Üretim Hattı Beklemez',
 'Yerli yedek parça stoğumuz ve saha servis ekibimiz ile 24-48 saat içinde fabrikanızdayız. Marka bağımsız bakım-onarım.',
 'uploads/slider/slider-2.svg',
 'Servis Talep Et', 'teklif-al.php',
 'Teklif Al', 'teklif-al.php',
 1, 2),

('Projeye Özel Mühendislik',
 'Standart Makine Değil, Doğru Makine',
 'Parça tipi ve üretim kapasitenize göre sıfırdan tasarlanan kumlama çözümleri. 3D CAD, mühendislik hesapları ve FAT dahil.',
 'uploads/slider/slider-3.svg',
 'Teklif Alın', 'teklif-al.php',
 'Hakkımızda', 'hakkimizda.php',
 1, 3);

-- =====================================================================
-- REFERANSLAR (8 sektör örneği)
-- =====================================================================
INSERT INTO referanslar (firma_adi, sektor, aciklama, website, aktif, sira) VALUES
('Konya Döküm A.Ş.', 'Dökümhane',
 'Sfero ve pik döküm parçaları için AK-200 çift askılı kumlama ve LT-300 tamburlu kumlama hattı teslim edildi.',
 '', 1, 1),
('Anadolu Çelik Konstrüksiyon', 'Çelik Yapı',
 'Tünel tipi SPK-1500 sac hattı ile günlük 15 ton profil kumlama kapasitesi.',
 '', 1, 2),
('Mercan Otomotiv Yan Sanayi', 'Otomotiv',
 'Motor parçaları için BK-120 basınçlı kumlama kabini ve VK-80 vakumlu sistem.',
 '', 1, 3),
('Emir Makina Sanayi', 'Makine İmalat',
 'Özel imalat projesi: 2400mm askı çaplı büyük askılı kumlama, FAT onaylı teslim.',
 '', 1, 4),
('Konya OSB Boru Fabrikası', 'Boru Üretimi',
 'BRK-400 iç-dış boru kumlama makinesi. Petrol ve doğalgaz hattı boru yüzey hazırlığı için.',
 '', 1, 5),
('Seyhan Gemi Bakım', 'Gemicilik',
 'SK-200 seyyar kumlama üniteleri ile liman bölgesinde saha bakım hizmeti.',
 '', 1, 6),
('Beyşehir Tarım Makinaları', 'Tarım Makinası',
 'LT-100 ve AK-100 kombinasyonu ile küçük ve büyük parça üretim hattı.',
 '', 1, 7),
('KAZ Restorasyon', 'Restorasyon',
 'Tarihi cephe ve anıt restorasyon projeleri için 3 adet BK-60 basınçlı kumlama kabini.',
 '', 1, 8);

-- =====================================================================
-- SAYFALAR UPDATE (hakkimizda zenginleştirildi)
-- =====================================================================
UPDATE sayfalar SET icerik = '<h2>Enamak Makina: Yerli Mühendislik Odaklı Kumlama Çözümleri</h2>
<p><strong>Enamak Makina</strong>, kumlama teknolojileri alanında <strong>imalat, bakım ve satış sonrası destek</strong> sunan mühendislik odaklı bir firmadır. Yıllara dayanan sektör deneyimimizi, yerli mühendislik kapasitesiyle buluşturarak Türkiye ve yurt dışı pazarlarına yüksek kaliteli kumlama sistemleri sunuyoruz.</p>

<h3>Konumlanmamız</h3>
<p>"Her işi yapan makine, gerçekte hiçbir işi doğru yapmayan makinedir." Bizim yaklaşımımız farklıdır. Her proje için doğru model, doğru türbin konfigürasyonu, doğru astar malzemesi ve doğru filtreleme kapasitesi hesaplanır. Katalog satıcısı değil, <strong>gerçek üreticiyiz</strong>.</p>

<h3>Neden Enamak Makina?</h3>
<ul>
<li><strong>Yerli Mühendislik:</strong> Her proje bilgisayar ortamında 3D tasarlanır, sonlu elemanlar analizi yapılır</li>
<li><strong>Kaliteli Malzeme:</strong> Manganlı çelik astarlar (kimyasal analiz sertifikalı), orijinal türbin kanatları, endüstriyel sınıf kartuş filtreler</li>
<li><strong>Yerli Stok:</strong> Kritik yedek parçalar depomuzda; arıza durumunda 24-48 saat içinde teslimat</li>
<li><strong>Marka Bağımsız Servis:</strong> Endümak, Abana, Strong, SATMAK, Saygılı, Sedmak, Tolermak veya ithal tüm markalar için bakım-onarım</li>
<li><strong>Fabrika Kabul Testi (FAT):</strong> Her makine teslim öncesi tam yük testine tabidir</li>
<li><strong>Sahada Devreye Alma:</strong> Kurulum ve operatör eğitimi dahil</li>
<li><strong>Şeffaf Teklif:</strong> Astar malzemesi, türbin motor gücü, filtre sınıfı ve garanti kapsamı açıkça belirtilir</li>
</ul>

<h3>Ürün Yelpazemiz</h3>
<p>Askılı, tamburlu, basınçlı kabin, tünel tipi, vakumlu, sac-profil, boru ve seyyar olmak üzere <strong>8 ana kategoride 14+ model</strong> ile çalışıyoruz. Standart modeller ihtiyacınızı karşılamazsa, <strong>projeye özel imalat</strong> ekibimiz devrededir.</p>' WHERE slug = 'hakkimizda';

-- =====================================================================
-- AYARLAR UPDATE (site_aciklama, hizmet detayları)
-- =====================================================================
UPDATE ayarlar SET deger = 'Enamak Makina - Yerli mühendislik odaklı kumlama makinesi imalatı. Askılı, tamburlu, basınçlı, tünel tipi. Konya merkez, Türkiye geneli servis.' WHERE anahtar = 'site_aciklama';
UPDATE ayarlar SET deger = 'Enamak Makina | Endüstriyel Kumlama Makineleri' WHERE anahtar = 'site_baslik';
UPDATE ayarlar SET deger = 'Kumlama Teknolojileri' WHERE anahtar = 'slogan';
