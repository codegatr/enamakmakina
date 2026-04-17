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
INSERT INTO urunler (kategori_id, ad, slug, kisa_aciklama, aciklama, gorsel, model_kodu, teknik_ozellikler, aktif, one_cikan, sira) VALUES

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
INSERT INTO slider (ust_baslik, baslik, aciklama, gorsel, buton_metin, buton_link, aktif, sira) VALUES

('Yerli Mühendislik, Yüksek Kalite',
 'Endüstriyel Kumlama Sistemleri',
 'Askılı, tamburlu, basınçlı ve tünel tipi kumlama makineleri. Projeye özel tasarım, fabrika kabul testi ve sahada devreye alma.',
 'uploads/slider/slider-1.svg',
 'Ürünlerimizi İnceleyin', 'urunler.php',
 1, 1),

('Duran Üretim Hattı Beklemez',
 '7/24 Teknik Servis ve Yedek Parça',
 'Yerli yedek parça stoğumuz ve saha servis ekibimiz ile 24-48 saat içinde fabrikanızdayız. Marka bağımsız bakım-onarım.',
 'uploads/slider/slider-2.svg',
 'Servis Talep Et', 'teklif-al.php',
 1, 2),

('Standart Makine Değil, Doğru Makine',
 'Projeye Özel Mühendislik',
 'Parça tipi ve üretim kapasitenize göre sıfırdan tasarlanan kumlama çözümleri. 3D CAD, mühendislik hesapları ve FAT dahil.',
 'uploads/slider/slider-3.svg',
 'Teklif Alın', 'teklif-al.php',
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

-- =====================================================================
-- SSS (Sıkça Sorulan Sorular) - 18 adet, 4 kategori
-- =====================================================================
DELETE FROM sss WHERE id > 0;

INSERT INTO sss (kategori, soru, cevap, sira, aktif) VALUES

-- Kategori: Ürünler & Teknik
('Ürünler & Teknik',
 'Hangi tip kumlama makinası benim için uygun?',
 'Seçim 3 faktöre bağlıdır: (1) Parça boyutu ve ağırlığı, (2) Günlük üretim adediniz, (3) Parça geometrisi. Küçük döküm parçalar ve cıvata için lastik tamburlu (LT serisi), 50kg+ ve karmaşık geometriler için askılı (AK serisi), hassas/küçük işler için basınçlı kabin (BK serisi) tercih edilir. Sac/profil için tünel tipi (TK veya SPK), boru için BRK serisi uygundur. Projenize uygun model seçimi için mühendislik ekibimiz ücretsiz analiz yapar.',
 1, 1),

('Ürünler & Teknik',
 'Makineleriniz hangi Sa standardına uygun sonuç verir?',
 'Standart konfigürasyonumuzda ISO 8501-1''e göre Sa 2.5 (çok kapsamlı temizlik) sonucu garanti edilir. Bu, endüstriyel boya sistemlerinin büyük çoğunluğunun zorunlu ön koşuludur. Sa 3 (tam temizlik, beyaz metal) talep ediliyorsa türbin sayısı artırılarak veya aşındırıcı tipi değiştirilerek karşılanır.',
 2, 1),

('Ürünler & Teknik',
 'Makineler yerli üretim mi, ithal mi?',
 'Tüm makinelerimiz Konya merkezli tesisimizde yerli mühendislik ile imal edilir. Ana komponentler (kabin, türbin muhafazası, filtre sistemi) %100 yerli üretimdir. Motor, PLC ve bazı standart aksesuarlar ihtiyaç duyulduğunda AB standardındaki markalardan (Siemens, Mitsubishi, SEW-Eurodrive) tedarik edilir. Bu sayede satış sonrası yedek parça stoğumuz sorunsuz çalışır.',
 3, 1),

('Ürünler & Teknik',
 'Kabin içi astar malzemesi nedir?',
 'Kabin içi astarlar manganlı çelik (Mn13 veya eşdeğeri) malzemeden imal edilir. Bu, aşınmaya karşı en dirençli çelik türlerinden biridir. Her teslimatta astar malzemesinin kimyasal analiz sertifikası müşteriye sunulur. Standart sac astar KULLANILMAZ — düşük maliyet için standart sac öneren tekliflere dikkat edin.',
 4, 1),

('Ürünler & Teknik',
 'Hangi aşındırıcı (çelik bilye, grit, garnet) kullanmalıyım?',
 'Doğru aşındırıcı seçimi makineye ve parçaya bağlıdır: Askılı kumlama için çelik bilye (steel shot) veya çelik grit; tamburlu için YALNIZCA küresel çelik bilye (köşeli grit tamburu delip deşik eder); hassas yüzeyler için cam boncuk; dekoratif kumlama için garnet; restorasyon için silisyum karbür kullanılır. Eğitim programlarımızda detaylı aşındırıcı seçim kriterleri öğretilir.',
 5, 1),

-- Kategori: Garanti & Servis
('Garanti & Servis',
 'Garanti süresi ne kadar?',
 'Tüm makinelerimize 2 yıl üretici garantisi verilir. Bu sürede işçilik veya malzeme kaynaklı tüm arızalar ücretsiz karşılanır. Garanti süresi uzatma paketi ile 5 yıla çıkarılabilir. Sarf malzemeler (türbin kanatları, astarlar, filtreler) garanti dışıdır ancak depomuzda sürekli stok bulunur.',
 1, 1),

('Garanti & Servis',
 'Arıza durumunda ne kadar sürede servis gelir?',
 'Saha servisi taleplerinde yanıt süremiz: Türkiye içi 24-48 saat, Konya ve çevre iller aynı gün. WhatsApp veya telefon ile ilk tanı ortalama 15 dakikada yapılır. Uzak masaüstü ile PLC arızalarına anında müdahale edilebilir. 7/24 teknik destek hattımız açıktır.',
 2, 1),

('Garanti & Servis',
 'Başka marka kumlama makineme bakım verir misiniz?',
 'Evet, marka bağımsız servis veriyoruz. Endümak, Abana Makina, Strong Makine, SATMAK, Saygılı, Sedmak, Tolermak ve ithal markalar için periyodik bakım, onarım ve modernizasyon yapıyoruz. Yedek parça tedariği için yerli stoğumuz ve uluslararası ağımız kullanılır.',
 3, 1),

('Garanti & Servis',
 'Bakım anlaşması yapıyor musunuz?',
 '3 seviye bakım paketi sunuyoruz: (1) Altın — 3 ayda bir tam kontrol + sarf malzeme dahil, (2) Standart — 6 ayda bir bakım ziyareti, (3) Temel — yılda 1 ziyaret. Bakım anlaşmanız olan müşterilere acil arıza durumlarında öncelik tanınır.',
 4, 1),

('Garanti & Servis',
 'Yedek parça tedarik süresi ne kadar?',
 'Kritik yedek parçalar (türbin kanatları, manganlı astar, filtre kartuşları, O-ring seti, elevatör kepçeleri) depomuzda stokta. Kargo sonrası 24-48 saat içinde sahanızdadır. Özel sipariş parçalar (örn. yabancı marka özel komponent) 7-14 iş günü içinde temin edilir.',
 5, 1),

-- Kategori: Satın Alma & Teklif
('Satın Alma & Teklif',
 'Teklif nasıl alabilirim?',
 'İletişim sayfasındaki teklif formunu doldurabilir, info@enamakmakina.com adresine e-posta atabilir veya +90 542 510 36 16 numarasını arayabilirsiniz. Teklifimizde astar malzemesi, türbin motor gücü, filtre sınıfı ve garanti kapsamı açıkça belirtilir. Şeffaflık prensiptir.',
 1, 1),

('Satın Alma & Teklif',
 'Teslim süresi ne kadar?',
 'Standart modeller (AK-100, LT-100, BK-60 gibi) için 30-45 gün, orta seri (AK-200, TK-600, SPK-1500) için 45-60 gün, özel projelerde (büyük tünel, özel tasarım) 60-120 gün teslim süresi geçerlidir. Üretim planımız siparişle başlar, her hafta müşteriye ilerleme raporu verilir.',
 2, 1),

('Satın Alma & Teklif',
 'Ödeme koşullarınız neler?',
 'Standart ödeme planı: %40 sipariş onayı, %40 üretim ortasında (mekanik montaj tamamlandığında), %20 devreye alma sonrası. Leasing ve banka kredisi seçenekleri için bankalarımızla anlaşmalarımız mevcuttur. Kurumsal müşteriler için özel ödeme planı değerlendirilebilir.',
 3, 1),

('Satın Alma & Teklif',
 'Teslimattan önce makineyi test edebilir miyim?',
 'Evet, her makine teslim öncesi fabrikamızda tam yük altında FAT (Fabrika Kabul Testi) yapılır. Müşterilerimiz isterse teste bizzat katılabilir. FAT raporu teslim evrakları ile birlikte sunulur. İsteğe bağlı olarak test videosu da kaydedilir.',
 4, 1),

-- Kategori: Kurulum & Eğitim
('Kurulum & Eğitim',
 'Makinenin kurulumu dahil mi?',
 'Evet. Tüm makinelerimizde nakliye, montaj, kalibrasyon ve devreye alma bedele dahildir. Kurulum öncesi teknik ekibimiz sahayı ziyaret eder, enerji/hava/drenaj altyapısını kontrol eder. Kurulum süresi makine boyutuna göre 1-5 gün arasındadır.',
 1, 1),

('Kurulum & Eğitim',
 'Operatör eğitimi veriyor musunuz?',
 'Her teslimat sonrası 2-3 gün operatör eğitimi verilir. Eğitim içeriği: ISO 8501-1 standartları, aşındırıcı seçim kriterleri, kabin içi astar kontrolü, türbin kanat aşınma analizi, filtre bakım rutini, iş güvenliği. Eğitim kitapçığı ve video materyaller de sağlanır.',
 2, 1),

('Kurulum & Eğitim',
 'Kurulum için hangi altyapı hazır olmalı?',
 'Genel gereksinimler: (1) Uygun zemin (beton, makinenin ağırlığını taşıyacak kalınlıkta), (2) Elektrik tesisatı (380V trifaze), (3) Hava hattı (basınçlı kabin için 7 bar), (4) Toz tahliye bağlantısı veya filtre alanı. Sahada inceleme yaptığımızda eksiklikleri yazılı olarak raporlarız.',
 3, 1),

('Kurulum & Eğitim',
 'Modernizasyon hizmeti sunuyor musunuz?',
 'Eski makineyi atmak yerine modernize etmek çoğu zaman %60-70 tasarruf sağlar. Hizmetlerimiz: PLC güncellemesi (eski röle mantık yerine Siemens/Mitsubishi PLC), türbin yenileme, kartuş filtre dönüşümü, HMI dokunmatik ekran entegrasyonu, uzaktan izleme sistemi. Her modernizasyon projesi için öncelikle saha incelemesi ve fizibilite raporu hazırlanır.',
 4, 1);

-- =====================================================================
-- BLOG — 6 SEO-optimize makale
-- =====================================================================
DELETE FROM bloglar WHERE id > 0;

INSERT INTO bloglar (baslik, slug, kategori, yazar, ozet, icerik, gorsel, meta_baslik, meta_aciklama, yayin_tarihi, aktif) VALUES

('Sa 2.5 Standardı Nedir? Kumlama Kalitesi Nasıl Ölçülür?',
 'sa-2-5-standardi-nedir',
 'Teknik',
 'Enamak Mühendislik',
 'ISO 8501-1 standardına göre Sa 2.5 yüzey temizlik derecesi, endüstriyel boya sistemlerinin büyük çoğunluğunun zorunlu ön koşuludur. Bu yazıda Sa derecelerinin ne anlama geldiğini, Sa 2.5''e ulaşmak için doğru makine konfigürasyonunu ve saha kontrolünün nasıl yapıldığını açıklıyoruz.',
 '<h2>Sa Standardı Nedir?</h2><p><strong>ISO 8501-1</strong> standardı, çelik yüzeylerin kumlama sonrası temizlik derecesini tanımlar. Bu standardın en çok kullanılan sınıflandırması "Sa" serisidir:</p><ul><li><strong>Sa 1</strong> — Hafif temizlik (gevşek yüzey kalıntıları alınır)</li><li><strong>Sa 2</strong> — Kapsamlı temizlik (pas ve çapak büyük oranda giderilir)</li><li><strong>Sa 2.5</strong> — Çok kapsamlı temizlik (yüzeyin %95''inden fazlası temizlenir — <em>endüstriyel standart</em>)</li><li><strong>Sa 3</strong> — Tam temizlik, beyaz metal (yüzey %100 temiz)</li></ul><h2>Neden Sa 2.5?</h2><p>Endüstriyel boya sistemlerinin (epoxy, polyurethane, zinc silicate) üreticileri, garanti kapsamı için <strong>minimum Sa 2.5</strong> talep eder. Bu derecenin altında yapılan boya işleminde:</p><ul><li>Boya filmi tabaka adhesion değerleri düşer</li><li>Korozyon direnci 3-5 yıl yerine 1-2 yılda biter</li><li>Kaplama garantisi geçersiz olur</li></ul><h2>Sa 2.5''e Nasıl Ulaşılır?</h2><p>Bu, 3 faktörün doğru kombinasyonuna bağlıdır:</p><ol><li><strong>Türbin sayısı ve konumu</strong> — kör nokta olmamalı</li><li><strong>Aşındırıcı tipi</strong> — çelik bilye (S-170 veya S-230) ideal</li><li><strong>Temas süresi</strong> — parça geometrisine göre 90-180 saniye</li></ol><p>Enamak''ın standart konfigürasyonundaki AK ve TK serisi makinelerde Sa 2.5 <strong>her üretim döngüsünde tekrarlanabilir</strong> biçimde elde edilir. FAT testlerimizde bu değer ölçülür ve raporlanır.</p><h2>Saha Kontrolü</h2><p>Sa derecesinin doğrulanması için:</p><ul><li><strong>Görsel karşılaştırma</strong> — ISO referans fotoğrafları ile yüzey kıyaslanır</li><li><strong>Rugosimetre ölçümü</strong> — yüzey pürüzlülük değeri kontrol edilir (ideal: 40-70 μm)</li><li><strong>Bresle yöntemi</strong> — iyon içeriği test edilir</li></ul><p>Sa 2.5 standardında üretim yapan bir tesis, müşterisine uzun vadeli kalite taahhüdü verebilir. Bu bir "bonus" değil, modern endüstrinin temel gerekliliğidir.</p>',
 'uploads/blog/blog-1-sa25-standart.svg',
 'Sa 2.5 Standardı — Kumlama Kalite Ölçümü | Enamak',
 'ISO 8501-1 Sa 2.5 yüzey temizlik standardı ne demek? Endüstriyel boya için neden kritik? Ölçüm metodları ve Enamak yaklaşımı.',
 NOW(), 1),

('Askılı mı, Tamburlu mu? Doğru Kumlama Makinesi Seçimi',
 'askili-mi-tamburlu-mu-dogru-secim',
 'Seçim Rehberi',
 'Enamak Mühendislik',
 'Endüstriyel üretim forumlarında yıllardır süren tartışma: Küçük-orta boy parçalar için askılı mı yoksa lastik tamburlu kumlama makinesi mi? Cevap parça geometrisine ve ağırlığına bağlıdır. Yanlış seçim, üretim hattınıza aylık ciddi maliyet bindirebilir.',
 '<h2>Kritik Soru: Parçanız Ne?</h2><p>Her iki teknoloji de <strong>yüzey kumlama</strong> işini yapar, ama farklı parça tipleri için tasarlanmıştır:</p><h3>Lastik Tamburlu Kumlama (LT Serisi)</h3><p>Tambur içinde yuvarlanarak işlem gören parçalar için:</p><ul><li>Cıvata, somun, pul, bağlantı elemanları</li><li>Küçük döküm parçaları (zamak, alüminyum, pirinç)</li><li>Dişli, rulman yatağı, küçük mil parçaları</li><li><strong>Maksimum parça ağırlığı: 10 kg</strong></li><li><strong>Dökme halde işlem:</strong> tek seferde yüzlerce parça</li></ul><h3>Askılı Kumlama (AK Serisi)</h3><p>Kancaya asılarak işlem gören parçalar için:</p><ul><li>Büyük döküm parçalar (pik, sfero, çelik döküm)</li><li>Karmaşık geometrili parçalar</li><li>Kaynaklı yapılar</li><li><strong>Maksimum parça ağırlığı: 300-500 kg (modele göre)</strong></li><li><strong>Tek askı tek parça:</strong> hassas geometriler zarar görmez</li></ul><h2>Pratik Kural</h2><blockquote><strong>10 kg altı ve düzgün şekilli → Lastik tamburlu</strong><br><strong>50 kg üzeri, karmaşık veya uzun formlu → Askılı</strong><br>10-50 kg arası → Parça tipine göre mühendislik analizi gerekir.</blockquote><h2>Kritik Uyarı: Tambura Grit Koymayın!</h2><p>Lastik tamburlu makinelerde <strong>yalnızca küresel çelik bilye (steel shot)</strong> kullanılabilir. Köşeli grit:</p><ul><li>Tamburu 2-3 haftada delip deşik eder</li><li>Parçaların darbe ile zarar görmesine yol açar</li><li>Garanti kapsamı dışına çıkar</li></ul><p>Bu hata, forumlarda onlarca kez yinelenmiştir. Biz her teslimatta yazılı uyarı veriyoruz.</p><h2>"Her İşi Yapan Makine"</h2><p>Rakiplerin "her parçayı kumlayan tek makine" pazarlamasına dikkat. Gerçekte bu, <strong>hiçbir işi optimum yapmayan makine</strong> anlamına gelir. Geometrisi farklı parçalar farklı makine gerektirir. Doğru seçim, uzun vadeli üretim maliyetinizi %30-40 düşürür.</p>',
 'uploads/blog/blog-2-askili-vs-tamburlu.svg',
 'Askılı vs Tamburlu Kumlama — Doğru Seçim Rehberi',
 'Parça tipinize göre askılı veya lastik tamburlu kumlama makinesi seçimi. Ağırlık, geometri ve aşındırıcı uyumluluğu.',
 DATE_SUB(NOW(), INTERVAL 3 DAY), 1),

('Manganlı Çelik Astarlar Neden Kritik?',
 'manganli-celik-astarlar-kritik-nedir',
 'Malzeme',
 'Enamak Mühendislik',
 'Kumlama makinesinin kabin içi astarları, en çok aşınan parçalarıdır. Doğru malzeme seçilmezse, 6-12 ay içinde astar delinir ve kabin gövdesi zarar görür. Manganlı çelik neden tek doğru cevap?',
 '<h2>Kabin İçi Astar Nedir?</h2><p>Kumlama makinesi kabininin iç yüzeyi, türbinlerin fırlattığı çelik bilye/grit''in sürekli çarptığı alandır. Bu yüzey <strong>astar plakaları</strong> ile kaplanır — aşınma durumunda değiştirilebilir modüler yapıda.</p><h2>Yanlış Seçim: Standart Sac</h2><p>Maliyet düşürmek için bazı üreticiler kabin astarı olarak <strong>standart yapı çeliği (St37)</strong> veya kalın sac kullanır. Sonuç:</p><ul><li>3-6 ay içinde astar plakalarında delinme</li><li>Kabin ana gövdesi aşınmaya başlar</li><li>Tam revizyon ihtiyacı 2. yılda gelir</li><li>Toplam maliyet <strong>3-4 kat</strong> artar</li></ul><h2>Doğru Cevap: Manganlı Çelik</h2><p><strong>Mn13</strong> (Hadfield çeliği) veya eşdeğeri manganlı çelik alaşımları, aşınmaya karşı en dirençli endüstriyel malzemelerden biridir. Özellikleri:</p><ul><li><strong>Yüksek yüzey sertleşmesi</strong> — çarpma etkisiyle yüzey daha da sertleşir (work hardening)</li><li><strong>Kırılma tokluğu</strong> — sert olmasına rağmen kırılgan değildir</li><li><strong>Servis ömrü</strong> — standart saca göre 5-8 kat fazla</li><li><strong>Yerli imalat</strong> — ithalatsız temin edilebilir</li></ul><h2>Kimyasal Analiz Sertifikası</h2><p>Gerçek üreticiden alınan her manganlı çelik astar, <strong>kimyasal analiz sertifikası</strong> ile belgelenmelidir. Sertifikada bulunması gereken değerler:</p><table><tr><th>Element</th><th>Değer</th></tr><tr><td>Mangan (Mn)</td><td>%11-14</td></tr><tr><td>Karbon (C)</td><td>%1.0-1.4</td></tr><tr><td>Silisyum (Si)</td><td>%0.3-1.0</td></tr><tr><td>Fosfor (P) maks.</td><td>%0.07</td></tr></table><p>Teklif alırken <strong>astar malzemesinin sertifikasını</strong> yazılı olarak isteyin. Cevap vermeyen firma, gerçek üretici değildir.</p><h2>Astar Değişim Takvimi</h2><p>Normal kullanımda manganlı astarlar:</p><ul><li>Yan duvar astarları: 24-36 ay</li><li>Taban astarları (en çok çarpan): 18-24 ay</li><li>Tavan astarları: 36-48 ay</li></ul><p>Enamak depomuzda 12+ farklı modele ait astar stoğu mevcuttur. Sipariş 24-48 saat içinde teslim edilir.</p>',
 'uploads/blog/blog-3-manganli-celik.svg',
 'Manganlı Çelik Kabin Astarları — Seçim ve Bakım Rehberi',
 'Kumlama makinesi kabin astarı için doğru malzeme: Mn13 manganlı çelik. Ömür, sertifika, değişim takvimi.',
 DATE_SUB(NOW(), INTERVAL 7 DAY), 1),

('Boya Öncesi Yüzey Hazırlığı: Neden Kumlama Zorunlu?',
 'boya-oncesi-yuzey-hazirligi',
 'Uygulama',
 'Enamak Mühendislik',
 'Boya ve kaplama sistemlerinin performansı, %50 boya kalitesine, %50 yüzey hazırlığına bağlıdır. İyi hazırlanmamış yüzeye uygulanan en pahalı epoxy boya bile 1-2 yılda soyulur. Kumlama bu eşiği sağlayan tek endüstriyel yöntemdir.',
 '<h2>Boya Neden Soyulur?</h2><p>Endüstriyel tesislerde 1-2 yıl içinde soyulan boyaların analizi yapıldığında <strong>%80 vakada yüzey hazırlık hatası</strong> tespit edilir. Ana sebepler:</p><ul><li>Pas, yağ, kalıntı üzerine boya uygulanması</li><li>Yüzey pürüzlülüğünün yetersiz olması (boya tutunamaz)</li><li>Galvaniz/mill scale''in temizlenmemesi</li><li>Nem içeriği kontrol edilmemiş yüzey</li></ul><h2>Kumlama: Tek Yol</h2><p>Elle veya mekanik fırça ile temizlik maksimum <strong>Sa 2</strong> seviyesine çıkar. Bu, endüstriyel boya için yeterli değildir. Kumlama (abrasive blasting) şu avantajları sağlar:</p><ol><li><strong>Tam temizlik</strong> — Sa 2.5 / Sa 3 derecesi</li><li><strong>Pürüzlü yüzey</strong> — 40-70 μm, boya tutunması için ideal</li><li><strong>Eşit kalite</strong> — manuel işçilik değişkeni yok</li><li><strong>Hızlı</strong> — m² başına saniyeler</li></ol><h2>Yüzey Pürüzlülüğü (Anchor Profile)</h2><p>Kumlama sadece temizlik değil, aynı zamanda yüzeyde <strong>mikro pürüzler</strong> oluşturur. Boya molekülleri bu pürüzlere "tırnak" gibi tutunur. Pürüzlülük ölçümü rugosimetre ile yapılır:</p><ul><li><strong>Ra (Arithmetic)</strong>: ortalama yükseklik</li><li><strong>Rz (Peak-to-valley)</strong>: en yüksek-en alçak fark</li></ul><p>Epoxy boyalar için ideal profil: <strong>Rz = 50-75 μm</strong>. Bu değer aşındırıcı türü ve basınç ile kontrol edilir.</p><h2>Uygulama Süresi Kritik</h2><p>Kumlama yapılmış yüzey, açık atmosferde <strong>4 saat içinde</strong> boyanmalıdır. Bu süreden sonra:</p><ul><li>Nem yüzeyde yeniden oksit oluşturur</li><li>Flash rust (hızlı pas) görünür</li><li>Boya adhesion değeri düşer</li></ul><p>Çelik konstrüksiyon firmaları için en verimli yaklaşım: kumlama + boya <strong>aynı hatta entegre</strong> yapılmalıdır. SPK-1500 gibi tünel tipi kumlama makineleri, boya kabini ile entegre çalışabilir.</p><h2>Sektörel Zorunluluklar</h2><p>Bazı sektörlerde Sa 2.5 yasal zorunluluktur:</p><ul><li>Gemi inşaatı (IMO kuralları)</li><li>Köprü ve alt yapı boyaları (EN 12944)</li><li>Petrol/gaz boru hatları (NACE SP0188)</li><li>Offshore yapılar</li></ul><p>Kalite belgesi veren müşteri için doğru kumlama hattı, değil lüks — zorunluluktur.</p>',
 'uploads/blog/blog-4-boya-oncesi.svg',
 'Boya Öncesi Yüzey Hazırlığı — Kumlamanın Önemi',
 'Endüstriyel boya performansı için kumlama neden kritik? Sa 2.5, yüzey profili, 4 saat kuralı açıklamalı.',
 DATE_SUB(NOW(), INTERVAL 14 DAY), 1),

('Aşındırıcı Seçim Rehberi: Çelik Bilye, Grit, Garnet, Cam Boncuk',
 'asindirici-secim-rehberi',
 'Üretim',
 'Enamak Mühendislik',
 'Aynı kumlama makinesinde farklı aşındırıcılar kullanılır — ve her biri farklı sonuç üretir. Çelik bilye, grit, garnet, cam boncuk, silisyum karbür... Yanlış aşındırıcı seçimi üretim kalitesini %40 düşürebilir. Bu rehberde doğru seçimi gösteriyoruz.',
 '<h2>Aşındırıcı Türleri</h2><h3>1. Çelik Bilye (Steel Shot)</h3><ul><li><strong>Şekil:</strong> Küresel</li><li><strong>Sertlik:</strong> 40-50 HRC</li><li><strong>Kullanım:</strong> Ağır döküm parça temizliği, shot peening</li><li><strong>Avantaj:</strong> Uzun ömür (1000+ saat), geri dönüştürülebilir</li><li><strong>Tür kodları:</strong> S-70, S-170, S-230, S-330, S-460</li></ul><h3>2. Çelik Grit (Steel Grit)</h3><ul><li><strong>Şekil:</strong> Köşeli</li><li><strong>Sertlik:</strong> 55-65 HRC</li><li><strong>Kullanım:</strong> Agresif temizlik, pas ve kaplama kaldırma, maksimum pürüzlülük</li><li><strong>DİKKAT:</strong> Lastik tamburlu makinelerde <strong>KULLANILMAZ</strong> — tamburu deler</li><li><strong>Tür kodları:</strong> G-10 (en iri) - G-120 (en ince)</li></ul><h3>3. Garnet</h3><ul><li><strong>Kaynak:</strong> Doğal mineral</li><li><strong>Sertlik:</strong> 7-8 Mohs</li><li><strong>Kullanım:</strong> Su kesimi, hassas yüzey, silis içermeyen kumlama</li><li><strong>Avantaj:</strong> Çevre dostu, silicosis riski yok, toksik değil</li><li><strong>Uygulama:</strong> Paslanmaz çelik, alüminyum yüzeyler</li></ul><h3>4. Cam Boncuk (Glass Bead)</h3><ul><li><strong>Sertlik:</strong> 5-6 Mohs (yumuşak)</li><li><strong>Kullanım:</strong> Hafif temizlik, dekoratif kumlama, hassas parçalar</li><li><strong>Avantaj:</strong> Yüzey hassaslığı bozulmaz</li><li><strong>Uygulama:</strong> Tıbbi cihaz, gıda ekipmanı, elektronik</li></ul><h3>5. Silisyum Karbür (SiC)</h3><ul><li><strong>Sertlik:</strong> 9.25 Mohs (en sert aşındırıcı)</li><li><strong>Kullanım:</strong> Restorasyon, taş/mermer kumlama, özel yüzey</li><li><strong>Maliyet:</strong> Yüksek</li><li><strong>Uygulama:</strong> Anıt restorasyonu, cam kumlama</li></ul><h3>6. Alüminyum Oksit (Korund)</h3><ul><li><strong>Sertlik:</strong> 9 Mohs</li><li><strong>Kullanım:</strong> Shot peening alternatifi, boya altı hazırlık</li><li><strong>Tekrar kullanım:</strong> 3-5 döngü</li></ul><h2>Makine Türüne Göre Aşındırıcı</h2><table><tr><th>Makine</th><th>Tavsiye Edilen</th><th>Kullanma</th></tr><tr><td>Askılı Kumlama</td><td>Çelik Bilye S-170 veya S-230</td><td>Köşeli grit (isteğe bağlı)</td></tr><tr><td>Lastik Tamburlu</td><td>Çelik Bilye S-70, S-110</td><td>❌ ASLA grit koymayın</td></tr><tr><td>Basınçlı Kabin</td><td>Garnet, cam boncuk, korund</td><td>Parça tipine göre</td></tr><tr><td>Vakumlu</td><td>Cam boncuk, korund</td><td>Toz üretmeyen küçük tane</td></tr></table><h2>Silis Kumu (SiO2) Tehlikesi</h2><p><strong>Türkiye dahil çoğu ülkede silis kumu endüstriyel kumlamada yasaktır.</strong> Sebep: Silicosis — ciddi akciğer hastalığı. Eski tesisatlarda hâlâ görülse de, yeni tesislerde kesinlikle kullanılmamalıdır. Alternatifleri: garnet (doğal ve güvenli), granüle cüruf (Black Beauty), demir oksit.</p><h2>Aşındırıcı Tedariği</h2><p>Enamak ile kumlama makinesi alan müşterilere, ilk 2 ton aşındırıcı ücretsiz dahildir. Sonraki tedarik için 10+ farklı aşındırıcı stoğumuzda mevcuttur.</p>',
 'uploads/blog/blog-5-asindirici-secimi.svg',
 'Aşındırıcı Seçim Rehberi — Çelik Bilye, Grit, Garnet',
 'Kumlama aşındırıcıları karşılaştırması: çelik bilye, grit, garnet, cam boncuk, silisyum karbür. Doğru seçim.',
 DATE_SUB(NOW(), INTERVAL 21 DAY), 1),

('Aylık Bakım Kontrol Listesi: Kumlama Makinenizi Koruyun',
 'aylik-bakim-kontrol-listesi',
 'Servis',
 'Enamak Mühendislik',
 'Düzenli bakım, kumlama makinesi ömrünü 2 katına çıkarır. Planlı bakım olmadan kritik parçalar beklenmedik anda arızalanır, üretim hattı durur. İşte aylık olarak yapılması gereken 12 kontrol.',
 '<h2>Neden Planlı Bakım?</h2><p>Kumlama makineleri ağır endüstriyel ekipmanlardır. Sürekli aşındırıcı partikül çarpması altında çalışırlar. Planlı bakım yapılmadığında:</p><ul><li>Türbin kanat kırılması → rotor dengesi bozulur → motor yanar</li><li>Filtre tıkanması → toz emisyonu → çevresel ceza</li><li>Elevatör kayış aşınması → aşındırıcı geri dönüşü durur</li><li>PLC arızası → üretim 24-72 saat durur</li></ul><h2>Aylık Kontrol Listesi</h2><h3>✓ 1. Türbin Kanat Kontrolü</h3><p>Her türbin için: kanatların aşınma oranı görsel olarak kontrol edilir. %40 üzeri aşınma varsa değiştirin. Dengesiz kanat rotoru titretir, motor yatağı zarar görür.</p><h3>✓ 2. Kabin İçi Astar Kontrolü</h3><p>Özellikle türbin karşı duvarı ve taban. Astar kalınlığı başlangıç değerinin %30''una düştüyse değiştirin.</p><h3>✓ 3. Elevatör Kayış Gerginliği</h3><p>Kayış elle bastırıldığında 10-15 mm esnemeli. Gevşek kayış kaydıraç, sıkı kayış yataklara zarar verir.</p><h3>✓ 4. Elevatör Kepçeleri</h3><p>Çatlak veya deforme kepçe varsa değiştirin. Eksik kepçe aşındırıcı akışını düşürür, kumlama verimi azalır.</p><h3>✓ 5. Filtre Kartuşları</h3><p>Fark basıncı manometresi kırmızı bölgedeyse kartuş değiştirilmeli. Tıkalı filtre: toz emisyonu + motor yükü artışı.</p><h3>✓ 6. Seperatör Elek</h3><p>Aşındırıcı ile tozun ayrıldığı elek. Tıkalıysa hava ile temizleyin. Yırtıksa değiştirin.</p><h3>✓ 7. Pnömatik Sistem</h3><p>Basınçlı kabinler için: hava filtresi, yağlayıcı, basınç regülatörü. Kondens boşaltılmalı.</p><h3>✓ 8. Contalar ve Keçeler</h3><p>Kabin kapağı, erişim kapakları. Aşındırıcı kaçağı yapan conta ömrünü kısaltır.</p><h3>✓ 9. PLC Hata Kayıtları</h3><p>Kontrol panelinden kayıtlara bakın. Tekrarlayan küçük alarmlar büyük arızanın habercisidir.</p><h3>✓ 10. Elektrik Panosu</h3><p>Toz birikmesi kontrol. Kontaktör temasları. Sigorta durumu.</p><h3>✓ 11. Ses ve Titreşim</h3><p>Normal dışı ses veya titreşim: rulman veya rotor dengesi sorunu habercisi. Kayıt altına alın.</p><h3>✓ 12. Aşındırıcı Seviyesi ve Kalitesi</h3><p>Hazne seviyesi, partikül boyutu, kirlilik (toz oranı). Gerektiğinde taze aşındırıcı takviyesi.</p><h2>Bakım Kaydı Tutun</h2><p>Her bakım ziyareti tarih, yapılan işlem ve değişen parçalar ile kayıt altına alınmalı. Bu kayıt:</p><ul><li>Garanti taleplerinde kanıt sağlar</li><li>Parça ömrü analizi mümkün kılar</li><li>Bakım maliyetlerini tahmin etmeye yardımcıdır</li></ul><h2>Enamak Bakım Paketleri</h2><p>Bakım için zaman veya uzmanlık yoksa, Enamak''ın 3 seviye bakım paketinden yararlanabilirsiniz: Altın (3 ayda bir + sarf dahil), Standart (6 aylık), Temel (yıllık). Detaylar hizmetler sayfamızda.</p>',
 'uploads/blog/blog-6-bakim.svg',
 'Aylık Bakım Kontrol Listesi — Kumlama Makinesi',
 '12 maddelik aylık bakım rehberi: türbin, astar, filtre, elevatör kontrolü. Makinenizin ömrünü uzatın.',
 DATE_SUB(NOW(), INTERVAL 28 DAY), 1);
